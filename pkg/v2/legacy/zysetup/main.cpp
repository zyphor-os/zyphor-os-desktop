#include <cstdlib>
#include <filesystem>
#include <iostream>
#include <pwd.h>
#include <string>
#include <unistd.h>
#include <vector>

bool hasInternet()
{
    return system("ping -c 1 -W 2 1.1.1.1 > /dev/null 2>&1") == 0;
}

bool installPackage(const std::string &package)
{
    std::cout << "\n==========================================" << std::endl;
    std::cout << " Installing " << package << std::endl;
    std::cout << "==========================================" << std::endl;

    std::string command = "apt-get install -y " + package;
    int result = system(command.c_str());

    if (result == 0)
    {
        std::cout << "[OK] " << package << " installed successfully." << std::endl;
        return true;
    }

    std::cout << "[FAILED] Failed to install " << package << "." << std::endl;
    return false;
}

void createDesktopShortcuts()
{
    const char *sudoUser = getenv("SUDO_USER");
    struct passwd *pw = sudoUser ? getpwnam(sudoUser) : getpwuid(getuid());

    if (!pw)
    {
        std::cout << "[WARNING] Unable to determine current user." << std::endl;
        return;
    }

    std::string desktop = std::string(pw->pw_dir) + "/Desktop";

    std::filesystem::create_directories(desktop);

    std::vector<std::string> shortcuts = {
        "libreoffice-writer.desktop",
        "libreoffice-calc.desktop",
        "libreoffice-impress.desktop",
        "firefox.desktop",
        "zyphor-command-center.desktop"
    };

    std::cout << "\n==========================================" << std::endl;
    std::cout << " Creating Desktop Shortcuts" << std::endl;
    std::cout << "==========================================" << std::endl;

    for (const auto &shortcut : shortcuts)
    {
        std::string source = "/usr/share/applications/" + shortcut;
        std::string destination = desktop + "/" + shortcut;

        if (!std::filesystem::exists(source))
        {
            std::cout << "[SKIPPED] " << shortcut << " not found." << std::endl;
            continue;
        }

        try
        {
            std::filesystem::copy_file(
                source,
                destination,
                std::filesystem::copy_options::overwrite_existing);

            std::filesystem::permissions(
                destination,
                std::filesystem::perms::owner_read |
                std::filesystem::perms::owner_write |
                std::filesystem::perms::owner_exec |
                std::filesystem::perms::group_read |
                std::filesystem::perms::others_read,
                std::filesystem::perm_options::add);

            std::cout << "[OK] " << shortcut << " shortcut created." << std::endl;
        }
        catch (...)
        {
            std::cout << "[FAILED] Could not create shortcut for " << shortcut << "." << std::endl;
        }
    }
}

int main()
{
    std::cout << "==========================================" << std::endl;
    std::cout << "        Welcome to ZySetup" << std::endl;
    std::cout << " Let's setup your PC completely!" << std::endl;
    std::cout << "==========================================" << std::endl;

    std::cout << "\n[*] Detecting internet..." << std::endl;

    if (!hasInternet())
    {
        std::cout << "[ERROR] No internet connection detected." << std::endl;
        return 1;
    }

    std::cout << "[OK] Internet connection detected." << std::endl;

    std::cout << "\n==========================================" << std::endl;
    std::cout << " Updating package lists" << std::endl;
    std::cout << "==========================================" << std::endl;

    system("apt-get update");

    std::vector<std::string> packages = {
        "zyphor-os-release",
        "fastfetch-config-1",
        "grub-screensaver-1",
        "fastfetch",
        "vim",
        "curl",
        "jq",
        "figlet",
        "lolcat",
        "php8.4",
        "php8.4-cli",
        "php8.4-fpm",
        "php8.4-mysql",
        "php8.4-sqlite3",
        "php8.4-xml",
        "php8.4-mbstring",
        "php8.4-curl",
        "php8.4-zip",
        "php8.4-bcmath",
        "php8.4-intl",
        "php8.4-redis",
        "php8.4-gd",
        "libapache2-mod-php8.4",
        "guvcview",
        "libreoffice",
        "vlc",
        "drawing",
        "simplescreenrecorder",
        "ufw",
        "ksnip",
        "zysh",
        "zyphor-whats-new",
        "zyphor-repo-config",
        "zyphor-command-center",
        "zyphor-command-center-web",
        "zyphor-updates"
    };

    for (const auto &pkg : packages)
    {
        installPackage(pkg);
    }

    createDesktopShortcuts();

    std::cout << "\n==========================================" << std::endl;
    std::cout << " ZySetup completed successfully!" << std::endl;
    std::cout << "==========================================" << std::endl;

    return 0;
}