#include <iostream>
#include <unistd.h>
#include <cerrno>
#include <cstring>

int main()
{
    const char* guvcview = "/usr/bin/guvcview";

    // Check if guvcview exists and is executable
    if (access(guvcview, X_OK) != 0)
    {
        std::cerr
            << "Error: guvcview is not installed or is not executable.\n"
            << "Expected location: " << guvcview << "\n"
            << "Please install guvcview and try again.\n";

        return 1;
    }

    // Launch guvcview
    execl(
        guvcview,
        "guvcview",
        (char*)nullptr
    );

    // If execl() returns, launching failed
    std::cerr
        << "Error: Failed to launch guvcview: "
        << std::strerror(errno)
        << "\n";

    return 2;
}
