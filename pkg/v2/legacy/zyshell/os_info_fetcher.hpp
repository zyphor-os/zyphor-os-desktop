#pragma once
#include <fstream>
#include <sstream>
#include <iomanip>
#include <iostream>
#include <map>
#include <memory>

// NOT DONE YET!! 
// I'll turn the welcome screen into a neofetch like style of output
// that's the reason why i'll turn each feature of show_os_screen() from the legacy code ./legacy C/shell.c
// into a modular parts


// just a lamda function designed to remove quoates in a string
inline auto remove_quotes = [](std::string_view data) {
    
    std::stringstream output;
    for (char c : data) {
        if ( c == '"') {
            continue;
        }
        output << c;
    }

    return output.str();
};

class OS_INFO_FETCHER {

public:

    OS_INFO_FETCHER() : file(OS_INFO), buffer("") {

        if(!file.is_open()) {
            std::cerr << "Failed to open " << OS_INFO;
            exit(-1);
        }

        std::stringstream ss; 
        ss << file.rdbuf();
        buffer = ss.str();
    }

    ~OS_INFO_FETCHER() {
       file.close();  
   }

    // Class member responsible for fetching each info individually from /etc/os-release
    std::pair<std::string, std::string> get_os_info(std::string key);

private:

   
    const std::string OS_INFO = "/etc/os-release";
    std::ifstream file;
    std::string buffer;

};
