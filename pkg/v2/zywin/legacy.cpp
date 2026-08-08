#include <algorithm>
#include <cstdlib>
#include <ctime>
#include <filesystem>
#include <iostream>
#include <string>
#include <vector>

// needed for posix_spawn()
#include <spawn.h>
#include <sys/wait.h>


namespace fs = std::filesystem;
extern char **environ;


const std::string TMP_PREFIX = "zywin-installer-iso-extract-";


// ? i won't touch this
std::string sanitizeName(const fs::path &file)
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
fs::path getWinePrefix(const fs::path &file)
{
        
    // ! Gets the value of the HOME directory variable
    // ? i turned this into a lambda function since this is the only function that call getHomeDirectory
    auto getHomeDirectory = []() {

        const char *home = std::getenv("HOME");
        if (!home) {
            std::cerr << "Unable to determine HOME directory.\n";
            std::exit(1);
        }
        return home;
    };

    return fs::path(getHomeDirectory()) / ".local/share/zywin/prefixes" / sanitizeName(file);
}


// Makes sure the the prefix exist by checking it's existence and then using wineboot if it's otherwise
void ensureWinePrefix(const fs::path &prefix)
{
    if (fs::exists(prefix))
        return;

    fs::create_directories(prefix.parent_path());

    std::cout << "Creating Wine prefix:\n";
    std::cout << "  " << prefix << "\n\n";

    std::string cmd = "WINEPREFIX=\"" + prefix.string() + "\" wineboot >/dev/null 2>&1";
    system(cmd.c_str());
}

void cleanupExtracted()
{
    if (!fs::exists("/tmp"))
        return;

    int removed = 0;

    for (const auto &entry : fs::directory_iterator("/tmp"))
    {
        if (!entry.is_directory())
            continue;

        std::string name = entry.path().filename().string();

        if (name.rfind(TMP_PREFIX, 0) == 0)
        {
            std::error_code ec;
            fs::remove_all(entry.path(), ec);

            if (!ec)
                removed++;
        }
    }

    if (removed > 0)
    {
        std::cout << "Removed "
                  << removed
                  << " extracted ISO director"
                  << (removed == 1 ? "y" : "ies")
                  << ".\n";
    }
}

void runWine(const fs::path &exe, const fs::path &prefix)
{

     // ? ensures that the directory exists before executing wine on that prefix
    ensureWinePrefix(prefix);

    std::string cmd = "WINEPREFIX=\"" + prefix.string() + "\" wine \"" + exe.string() + "\"";

    std::cout << "\nLaunching:\n"; std::cout << cmd << "\n\n";

    // ! Dangerous system() shell execution prone to shell injection, this needs a fix
    system(cmd.c_str());
}

void runMSI(const fs::path &msi, const fs::path &prefix)
{

    // ? ensures that the directyr exists before executing wine on that prefix
    ensureWinePrefix(prefix);

    std::string cmd = "WINEPREFIX=\"" + prefix.string() + "\" wine msiexec /i \"" + msi.string() + "\"";

    std::cout << "\nLaunching:\n";
    std::cout << cmd << "\n\n";

     // ! Dangerous system() shell execution prone to shell injection, this needs a fix
    system(cmd.c_str());
}

std::vector<fs::path> findExecutables(const fs::path &dir)
{
    std::vector<fs::path> exes;

    for (const auto &entry : fs::recursive_directory_iterator(dir))
    {
        if (!entry.is_regular_file())
            continue;

        std::string ext = entry.path().extension().string();

        std::transform(ext.begin(), ext.end(), ext.begin(), ::tolower);

        if (ext == ".exe")
            exes.push_back(entry.path());
    }

    return exes;
}

int main(int argc, char *argv[])
{
    if (argc != 2)
    {
        std::cout << "Usage:\n\n";
        std::cout << "  zywin <file.exe>\n";
        std::cout << "  zywin <file.msi>\n";
        std::cout << "  zywin <file.iso>\n";
        std::cout << "  zywin cleanup\n";
        return 1;
    }

    std::string arg = argv[1];

    if (arg == "cleanup")
    {
        cleanupExtracted();
        return 0;
    }

    fs::path file(arg);

    if (!fs::exists(file))
    {
        std::cerr << "File not found.\n";
        return 1;
    }

    // extracts the extention from the input file
    std::string ext = file.extension().string();
    std::transform(ext.begin(), ext.end(), ext.begin(), ::tolower);


    //------------------------------------------------------
    // EXE
    //------------------------------------------------------

    if (ext == ".exe")
    {
        fs::path prefix = getWinePrefix(file);
		runWine(file, prefix);
        return 0;
    }

    //------------------------------------------------------
    // MSI
    //------------------------------------------------------

    if (ext == ".msi")
    {
        fs::path prefix =  getWinePrefix(file);
		runMSI(file, prefix);
        return 0;
    }

    //------------------------------------------------------
    // ISO
    //------------------------------------------------------

    if (ext == ".iso")
    {
        // Remove previous extractions first.
        cleanupExtracted();

        std::time_t now = std::time(nullptr);

        fs::path tmp =
            "/tmp/" + TMP_PREFIX + std::to_string(now);

        fs::create_directories(tmp);

        std::cout << "Extracting ISO...\n";


        std::string file_str = file.string();
        std::string tmp_str  = "-o" + tmp.string(); // 7z expects -o immediately followed


        pid_t pid;

        std::vector<char *> cmd = {
            const_cast<char *>("7z"),
             const_cast<char *>("x"),
             const_cast<char *> (file.c_str()),
             const_cast<char *>(tmp_str.c_str()),
             nullptr
        }; 

        if (posix_spawn(&pid, "/usr/bin/7z", NULL, NULL, cmd.data(), environ) != 0)
        {
            std::cerr << "Failed to start extraction process ISO.\n";
            fs::remove_all(tmp);
            return 1;
        }


        //  MUST wait for 7z to finish, otherwise it runs asynchronously 
        int status;
        if (waitpid(pid, &status, 0) == -1 || !WIFEXITED(status) || WEXITSTATUS(status) != 0) 
        {
            std::cerr << "7z extraction failed during execution.\n";
            fs::remove_all(tmp);
            return 1;
        }

        auto exes = findExecutables(tmp);

        if (exes.empty())
        {
            std::cerr << "No executable found.\n";
            fs::remove_all(tmp);
            return 1;
        }

        if (exes.size() == 1)
        {
            fs::path prefix = getWinePrefix(file);
			runWine(exes[0], prefix);
            return 0;
        }

        std::cout << "\nExecutables found:\n\n";

        for (size_t i = 0; i < exes.size(); ++i)
        {
            std::cout
                << (i + 1)
                << ". "
                << fs::relative(exes[i], tmp)
                << '\n';
        }

        std::cout << "\nSelect an exe file: ";

        int choice;
        std::cin >> choice;

        if (!std::cin ||
            choice < 1 ||
            choice > static_cast<int>(exes.size()))
        {
            std::cerr << "Invalid selection.\n";
            return 1;
        }

        fs::path prefix = getWinePrefix(file);
		runWine(exes[choice - 1], prefix);
        return 0;
    }

    std::cerr << "Unsupported file type.\n";
    return 1;
}