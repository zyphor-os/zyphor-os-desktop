#pragma once
#include <algorithm>
#include <cstdlib>
#include <ctime>
#include <filesystem>
#include <iostream>
#include <string>
#include <vector>
#include <chrono>

#include "logo.hpp"


// needed for posix_spawn()
#include <spawn.h>
#include <sys/wait.h>
#include <string.h>



// Class Template dedicated to help anything to do with wine prefixes

class WineHandler {

public:

    // Class member that runs files with .exe extension
    void execExe(const std::filesystem::path &file);

    // Class member that runs files with .msi extension
    void execMsi(const std::filesystem::path &file);

    // Class member that extracts an .iso image and looks for an executable file
    void execIso(const std::filesystem::path &file);

private:

    // formatting file name into a valid prefix path
    std::string sanitizeFileName(const std::filesystem::path &file);

    // Generates a wine prefix
    std::filesystem::path getWinePrefix(const std::filesystem::path &file);

    // ensures that the wineprefix is generated
    void ensureWinePrefix(const std::filesystem::path &prefix);
    
    // Class member responsible of traversing an entire directory after extracting from an iso to find executables
    std::vector<std::filesystem::path> findExecutables(const std::filesystem::path &dir);
};