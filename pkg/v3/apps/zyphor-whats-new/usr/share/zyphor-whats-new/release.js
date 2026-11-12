const releaseNotes = {
    distro: "Zyphor OS 3 \"Bethany\" LTS",
    version: "3.2.0-bethany-lts-u10",
    date: "Updated On: November 12, 2026 @ 06:42 PM",

    sections: [
        {
            "title": "Cleaner Terminal Banner",
            "items": [
                "The terminal welcome banner is now minimal and clean, showing only the OS name and the most useful commands",
                "Removed the build time from the banner, so the output is shorter and easier to read",
                "The banner now shows only in interactive terminals, so scripts, SSH commands and piped output are no longer cluttered with it",
                "If figlet and lolcat are installed, the OS name is shown in a colorful style; otherwise a simple colored text is used as a fallback",
                "The OS name is read from your system's release information, so it always matches the installed version",
                "Added quick command hints for updating the system with sudo zyphor system upgrade, plus the shorter sudo zy system upgrade alternative",
                "Added a hint for sudo zy system clean, so you can easily clean and optimize your system right from the banner"
            ]
        },
        {
            "title": "Smarter System Cleanup",
            "items": [
                "The zyphor system clean command now does a much more thorough cleanup: it removes package caches, unused packages, leftover configuration files, old rotated logs, and stale temporary files",
                "The system journal is trimmed to the last 7 days, which can free up gigabytes on systems that have been running for a long time",
                "Added a --dry-run option to system clean, so you can preview what would be removed before anything is changed",
                "Added a --deep option to system clean, which also empties your Trash and removes unused Flatpak runtimes",
                "Thumbnail cleanup now targets your own user account correctly, even when the command is run with sudo",
                "After cleaning, Zyphor shows how much disk space was freed in easy-to-read units like MB and GB",
                "The system clean command and its options are listed in the zyphor help screen and in the Zyphor CLI documentation"
            ]
        },
        {
            "title": "Package Install Works From the Command Center",
            "items": [
                "Installing a package or app from the Zyphor Command Center no longer gets stuck on a menu that cannot be answered, so apps that are not in the Zyphor repository now install without freezing",
                "Apps that are not in the Zyphor repository are installed with apt by default, so you only need to type the app name",
                "Added Install via Snap and Install via Flatpak pages to the Packages section, so you can install directly from the Snap store or Flathub",
                "Apps that are listed in the Zyphor repository still install the way the repository specifies, whichever page you use"
            ]
        },
        {
            "title": "Zyphor CLI Help, Available Offline",
            "items": [
                "The Zyphor CLI documentation is now built into the Zyphor Command Center's offline mode, so you can look up any zyphor command without an internet connection",
                "Added a button on this What's New page that takes you straight to the Zyphor CLI documentation, so you no longer have to find it at the bottom of the sidebar"
            ]
        },
        {
            "title": "Profile Picture Works on Every Desktop",
            "items": [
                "Changing your profile picture in the Zyphor Command Center now updates correctly on XFCE, GNOME, KDE Plasma, Cinnamon, MATE and other desktops, instead of being tuned for XFCE only",
                "The Zyphor Command Center detects which desktop you are running and applies only the extra refresh steps that desktop needs",
                "The new picture is saved as your user icon and registered with the system, so login screens and user menus pick it up",
                "The XFCE panel is only refreshed when you are actually running XFCE, so GNOME and KDE sessions are left untouched",
                "Support for additional desktops can be added in the future without changing how the profile page works"
            ]
        },
        {
            "title": "More Reliable Avatar Uploads",
            "items": [
                "Added php-gd as a dependency of the Zyphor Command Center web package, so the image cropping used for profile pictures is installed automatically",
                "If image support is ever missing, the upload now shows a clear message asking you to install php-gd instead of failing with a server error"
            ]
        },
        {
            "title": "Administrator Actions on Fresh Installs",
            "items": [
                "The Zyphor Command Center now depends on pkexec (or policykit-1 on older Debian bases), so actions that need administrator rights, such as installing and removing packages, can ask for your password",
                "Fixes the 'pkexec: command not found' error on minimal installs, including KDE setups without a full desktop package set"
            ]
        }
    ]
};