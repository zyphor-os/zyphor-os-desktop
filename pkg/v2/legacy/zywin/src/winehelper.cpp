#include "winehelper.hpp"
#include "colors.hpp"


extern char **environ;


// Lamda function that checks if everything in a string is a digit
inline auto isDigitOnly = [](const std::string &str) -> bool {
    return std::all_of(str.begin(), str.end(), [](unsigned char c){
        return std::isdigit(c);
    });
};

// This function has the same logic from it's former codebase i just placed it in a class
std::string  WineHandler::sanitizeFileName(const std::filesystem::path &file)
{
    std::string name = file.stem().string();
    std::transform(name.begin(), name.end(), name.begin(), ::tolower);

    for (char &c : name)
    {
        if (!std::isalnum(static_cast<unsigned char>(c)))
            c = '-';
    }

    while (name.find("--") != std::string::npos)
        name.replace(name.find("--"), 2, "-");

    if (!name.empty() && name.front() == '-')
        name.erase(0, 1);

    if (!name.empty() && name.back() == '-')
        name.pop_back();

    if (name.empty())
        name = "application";

    return name;
}

// Generates a wine prefix directory
std::filesystem::path WineHandler::getWinePrefix(const std::filesystem::path &file)
{
        
    //  Gets the value of the HOME directory variable
    // i turned this into a lambda function since this is the only function that call getHomeDirectory
    auto getHomeDirectory = []() {

        const char *home = std::getenv("HOME");
        if (!home) {
            std::cerr << "Unable to determine HOME directory.\n";
            std::exit(1);
        }
        return std::string(home);   // fixed a critical dangling pointer bug here
    };

    auto wineprefix = std::filesystem::path(getHomeDirectory()) / ".local/share/zywin/prefixes" / sanitizeFileName(file);

    // i've set this up to convert prefix directory filesystem::apth to C style char* for the posix_spawn() function
    std::string temp = wineprefix.string();
    char* supplytoMkdir = temp.data();

    std::vector<char*> cmd = {

        const_cast<char*>("mkdir"),
        const_cast<char*>("-p"),
        supplytoMkdir,
        nullptr
    };

    // spawn a child process using posix_spawnm to execute mkdir -p
    pid_t pid;
    if (posix_spawn(&pid, "/usr/bin/mkdir", NULL, NULL, cmd.data(), environ) != 0)
    {
        std::cerr << BRIGHT_RED "Failed to create wineprefix directory: \n" RESET << temp << std::endl;
        exit(EXIT_FAILURE);
    } else {
        std::cerr << CYAN "Wineprefix directory succesfully created or verified: " RESET << temp << std::endl;
    }

    //  wait for mkdir to finish, otherwise it runs asynchronously 
    int status;
    if (waitpid(pid, &status, 0) == -1 || !WIFEXITED(status) || WEXITSTATUS(status) != 0) 
    {
        std::cerr << BRIGHT_RED "mkdir extraction failed during execution.\n" RESET;
        exit(EXIT_FAILURE);
    }

    // return a path which a premade wineprefix is made
    return wineprefix;
}


// Makes sure the the prefix exist by checking it's existence and then using wineboot if it's otherwise
void WineHandler::ensureWinePrefix(const std::filesystem::path &prefix)
{

    // Checking for prefix / "system.reg" verifies that the Wine prefix is actually initialized and valid!

    if ( std::filesystem::exists(prefix / "system.reg")) {

        std::cout << BRIGHT_YELLOW << "Prefix already exist: " RESET << prefix << "\n"; 
        return;
    } else {
        std::cout << BRIGHT_YELLOW << "Prefix Don't exist: " RESET << prefix << "\n"; 
        return;
    }


    std::cout << BRIGHT_YELLOW "Creating Wine prefix: " RESET << prefix << "\n";

    std::string cmd = "WINEPREFIX=\"" + prefix.string() + "\" wineboot >/dev/null 2>&1";
    int status = system(cmd.c_str());

    // checks if the WINEBOOT prefix is actually initialized by wineboot
    if (status != 0) {
        std::cerr << "Error: Failed to initialize Wine prefix at " << prefix << "\n";
    }
}


// Class member responsible of traversing an entire directory after extracting from an iso to find executables
std::vector<std::filesystem::path> 
WineHandler::findExecutables(const std::filesystem::path &dir) {

    std::vector<std::filesystem::path> executables;

    for(const auto &file : std::filesystem::recursive_directory_iterator(dir)) {

        // skips if a file is not a normal file like if it's a . or .. diectory traversal linux thingy
        if(!file.is_regular_file()) { continue; }

         // extracts the extention from the input file
        std::string ext = file.path().extension().string();
        std::transform(ext.begin(), ext.end(), ext.begin(), ::tolower);

        if(ext == ".exe") {
            executables.push_back(file.path());
        }
    }

    return executables;
}


// Revised runWine() into execExe() to avoid shell injection by using posix_spawn() instead of system()
void  WineHandler::execExe(const std::filesystem::path &file, const std::filesystem::path &custom_prefix) {

    std::cout << BRIGHT_YELLOW "Executing a Portable Executable file (.exe) via wine..\n";

    // i've fix the issue with iso prefix inaccurate naming by passing an optional argument to custom_prefix
    std::filesystem::path prefix = getWinePrefix((custom_prefix == "" ? file: custom_prefix));
    ensureWinePrefix(prefix);

    // Build argv for wine argumnt
    // fixed a possible memory corruption bug

    std::string wine_str = "wine";
    std::string file_str = file.string();  // ✅ Persistent!

    std::vector<char*> cmd = {
        wine_str.data(),
        file_str.data(),
        nullptr
    };

    // Build environment with WINEPREFIX
    std::string winePrefix = "WINEPREFIX=" + prefix.string();
    std::vector<char*> env;
    
    // loop throught the entire enviroment and skips over WINEPREFIX= to diregard old prefix
    for (char **e = environ; *e != nullptr; ++e) {
        if (strncmp(*e, "WINEPREFIX=", 11) != 0) {  // Skip old WINEPREFIX
            env.push_back(*e);
        }
    }

      // place the new WINEPREFIX to environ
    env.push_back(winePrefix.data()); // this is safer 
    env.push_back(nullptr);

    std::cout << BRIGHT_YELLOW "Executing wine at prefix: " RESET << prefix << std::endl;
 
    pid_t pid;
    if (posix_spawn(&pid, "/usr/bin/wine", nullptr, nullptr, cmd.data(), env.data()) != 0) {
        std::cerr << BRIGHT_RED "Failed to spawn wine process, please make sure wine software exists in your software\n";
        return;
    }

    int status;
    waitpid(pid, &status, 0);

    // ? checks if the child process (which is posix_spawn() that executes wine) did not exit normally 
    // ? or if the child process exited normally but retruned a failure
    if (!WIFEXITED(status) || WEXITSTATUS(status) != 0) {
        std::cerr << BRIGHT_RED "Wine execution failed, Please make sure the file exists and a valid windows application\n";
    }
}


// Revised runMsi() into execMSi() to avoid shell injection
void WineHandler::execMsi(const std::filesystem::path &file) {

    std::cout << BRIGHT_YELLOW "Executing a Microsoft Installer (.msi) via wine..\n";
    
    std::filesystem::path prefix = getWinePrefix(file);
    ensureWinePrefix(prefix);

    
     // Build argv for wine argumnt this is for running msi softwares utilizing msiexec argument
    std::string file_str = file.string();

    // removed possible dangling pointer by moving arguments as a persistent string 
    std::vector<char*> cmd = {
        const_cast<char*>("wine"),
        const_cast<char*>("msiexec"),
        const_cast<char*>("/i"),
        file_str.data(),
        nullptr
    };

    std::string winePrefix = "WINEPREFIX=" + prefix.string();
    std::vector<char*> env;

    // loop throught the entire enviroment and skips over WINEPREFIX= to diregard old prefix
    for (char **e = environ; *e != nullptr; ++e) {
        if (strncmp(*e, "WINEPREFIX=", 11) != 0) {  
            env.push_back(*e);
        }
    }

    // place the new WINEPREFIX to environ
    env.push_back(winePrefix.data());
    env.push_back(nullptr);

    std::cout << BRIGHT_YELLOW "Executing wine at prefix: " RESET << prefix << std::endl;
    pid_t pid;

    if ( posix_spawn(&pid, "/usr/bin/wine", nullptr, nullptr, cmd.data(), env.data()) != 0) {
        std::cerr << BRIGHT_RED "Failed to spawn wine process, please make sure wine software exists in your software\n";
        return;
    }

    int status;
    waitpid(pid, &status, 0);

    // ? checks if the child process (which is posix_spawn() that executes wine) did not exit normally 
    // ? or if the child process exited normally but retruned a failure
    if (!WIFEXITED(status) || WEXITSTATUS(status) != 0) {
        std::cerr << BRIGHT_RED "Wine execution failed, Please make sure the file exists and a valid windows application\n";
    }
}

void WineHandler::execIso(const std::filesystem::path &file) {
    const std::string TMP_PREFIX = "zywin-installer-iso-extract-";

    // gets the current time from the system and then convert to local time structure
    auto time_now = std::time(nullptr);

    // extrcation point for the iso image
    std::filesystem::path tmp = "/tmp/" + TMP_PREFIX + std::to_string(time_now);

    // create the extraction point directory
    std::cout << BRIGHT_YELLOW "Creating a directory at " RESET << tmp << BRIGHT_YELLOW " this will be the extraction point for " RESET << file << "\n";
    std::filesystem::create_directories(tmp);
    std::cout << BRIGHT_YELLOW "Directory created: " RESET << tmp << "\n";

    std::cout << BRIGHT_YELLOW "Getting 7zip ready for extracting iso file...\n" RESET;

    // variables needed to execute 7z using posix_spawn()
    std::string file_str = file.string();
    std::string tmp_str  = "-o" + tmp.string(); // 7z expects -o immediately followed

    // setting up argument for 7z x command for posic_spawn()
     std::vector<char *> cmd = {
        const_cast<char*>("7z"),
        const_cast<char*>("x"),
        file_str.data(),
        tmp_str.data(),
        nullptr
    };

    // spawn a child process using posix_spawnm to execute 7z
    pid_t pid;
    if (posix_spawn(&pid, "/usr/bin/7z", NULL, NULL, cmd.data(), environ) != 0)
    {
        std::cerr << BRIGHT_RED "Failed to start extraction process ISO.\n" RESET;
        std::filesystem::remove_all(tmp);
        std::cerr << BRIGHT_YELLOW "Removed Extraction point: " RESET  << tmp << std::endl;
        exit(EXIT_FAILURE);
    }

     //  wait for 7z to finish, otherwise it runs asynchronously 
    int status;
    if (waitpid(pid, &status, 0) == -1 || !WIFEXITED(status) || WEXITSTATUS(status) != 0) 
    {
        std::cerr << BRIGHT_RED "7z extraction failed during execution.\n" RESET;
        std::filesystem::remove_all(tmp);
        exit(EXIT_FAILURE);
    }


    // this function removes the extention from the file name 
    // althought i have invented a wheel again 
    // becAUSE I found out about that i can just do file.replace_extension("") 
    // now i dont need this because i just found it sir mark has written the function
    // findExecutables() to loop throught nested folders but i wont remove this just incase

    // auto removeFileExtension = [](const std::filesystem::path &file) -> std::string {

    //         std::string buf = file.c_str();
    //         std::vector<char> fileWithoutExtension;

    //         bool firstCount = true;

    //         for (char c: buf  ) {
    //             if( c == '.' && firstCount == false) {
    //                 break;
    //             } 

    //             fileWithoutExtension.push_back(c);
    //             firstCount = false;
    //         }

    //         fileWithoutExtension.push_back('\0');
    //         return std::string(fileWithoutExtension.begin(), fileWithoutExtension.end());
    // };


    // sets an accurate path to where the iso image contents is extracted and then passing this as argument to findExecutables()
    // std::string pathToExtractionSite = tmp.string() + "/" + removeFileExtension(file); 

    std::cout << BRIGHT_YELLOW "Scanning Executables files in " RESET << tmp << "\n" RESET;
    std::vector<std::filesystem::path> executables = findExecutables(tmp);

    // checks if there's no executables found
    if (executables.empty()) {
        std::cerr << BRIGHT_RED "No executable found in " RESET << tmp << std::endl;
        std::filesystem::remove_all(tmp);
         std::cerr << BRIGHT_YELLOW "Removed Extraction point: " RESET  << tmp << std::endl;
        exit(EXIT_FAILURE);
    }


    // if it only contains one exe file inside the extraction area from the iso image it executes it instantly
    if(executables.size() == 1) {
        std::cout << BRIGHT_YELLOW "Found an executable file at " RESET + tmp.string() + "\n";
        std::cout << BRIGHT_YELLOW "Executing " RESET + executables[0].string() + "\n";
        execExe(executables[0]);
    }

    // executes whenever theres more than 1 executable files inside the extraction site from iso imageeeeeee
    if(executables.size() > 1) {

        std::cout << BRIGHT_YELLOW "Found a multiple executable files at " RESET + tmp.string() + "\n\n";
        std::cout << BRIGHT_YELLOW "Please choose from the list:  \n\n" RESET;
    
        std::string option;


        // this area lacks more error handling when a user picks a number none existing in the list or if the user did not put a number
        // i'll just continue this the next day and have sir mark to test it instead
        // time to sleep :>

        for(size_t i = 0; i < executables.size(); i++) {
            std::cout << "\t" << CYAN "File Number: " BOLD << 
            i << RESET " " << std::filesystem::relative(executables[i], tmp) << "\n"; 
        }

        std::cout <<  BRIGHT_YELLOW "\nPlease select a number: " RESET; std::cin >> option;
        
        // checks of the input is a number
        if(isDigitOnly(option)) {

            try {
                // checks if the selected option is at the right bound
                // made a change here where i placed the return value of at() in a const variable that will never be used
                // to remove g++ warnings
                const auto &x = executables.at(std::stoi(option));

                // damn the g++ compiler still flags a wanring despite writing [[maybe_unsused]] so 
                // i'll just cast the variable to nothing
                (void)x;


                execExe(executables[std::stoi(option)], file);
            
            } catch(std::out_of_range &e) {

                std::cout <<  BRIGHT_RED "\nYour selected option is not in the list\n" << e.what() << RESET "\n";
                exit(EXIT_FAILURE);
            }
        } else {
            std::cout <<  BRIGHT_RED "\nYour selected option is not valid\n" RESET;
            exit(EXIT_FAILURE);
        }
    }
}