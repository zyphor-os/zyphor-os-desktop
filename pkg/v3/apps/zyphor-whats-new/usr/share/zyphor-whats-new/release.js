const releaseNotes = {
    distro: "Zyphor OS 3 \"Bethany\" LTS",
    version: "3.2.0-bethany-lts-u16",
    date: "Updated On: November 26, 2026 @ 07:54 PM",

    sections: [
        {
            "title": "Install .deb Packages With a Double-Click",
            "items": [
                "Downloaded a `.deb` file? Just double-click it and Zyphor OS handles the rest, no terminal and no `dpkg -i` needed",
                "A confirmation dialog shows the package name, version, architecture and description before anything is installed, and tells you if it will replace an existing version or reinstall the same one",
                "Installation runs through `apt`, so missing dependencies are resolved and installed automatically",
                "Admin access is requested through a graphical password prompt, and nothing runs as root until you approve it",
                "You get a notification when it finishes, or a clear error message with the last lines of the log if something goes wrong",
                "Optional install (sudo zy pkg install zyphor-debian-handler) for existing users but included by default on new ISO releases"
            ]
        },
        {
            "title": "Clean and Reversible Pentest Setup",
            "items": [
                "Done testing? Run `sudo zy setup pentest undo` to remove every pentest tool in one step, no matter which targets you installed (recon, web, wireless, passwords, sniffing, forensics, or exploitation)",
                "You're shown exactly which packages will be removed and asked to confirm before anything is deleted; add `-y` to skip the prompt",
                "Unused dependencies are cleaned up afterward, and your user is removed from the `wireshark` group, returning your system to its original state"
            ]
        },
        {
            "title": "Clean and Reversible Wine Setup",
            "items": [
                "Changed your mind? Run `sudo zy setup wine undo` to remove Wine, its related packages, and Zyphor Windows Handler in one step",
                "Your Windows apps and saves are safe by default; you're asked before `~/.wine` and `~/.local/share/zywin` are deleted, so nothing is removed by accident",
                "Optionally disable the 32-bit (i386) architecture afterward to return your system to its original state"
            ]
        },
        {
            "title": "Seamless Windows App Integration",
            "items": [
                "No need for terminal setup, complicated Wine configuration, or environment variables; simply double-click a supported file to install or run",
                "Getting started is as easy as running `sudo zy system upgrade` followed by `sudo zy setup wine` to enable the feature",
                "Integration includes MIME-type associations, making your Windows apps feel right at home in the desktop environment"
            ]
        },
        {
            "title": "Cleaner Command Prompt",
            "items": [
                "Removed the exit status notification that appeared after a command failed, so your terminal stays clean and uncluttered",
                "This is part of the default ble.sh configuration included with the Bash setup, so no action is needed on your part",
                "If you already have your own ~/.blerc file, it overrides the default, so you can copy /etc/zyphor/blerc to your local ~/.blerc to get the update",
                "If you prefer to see the failure notice again, set bleopt exec_errexit_mark to your preferred mark in your ~/.blerc"
            ]
        },
        {
            "title": "Smarter Command History",
            "items": [
                "Added Kali-style gray suggestions that finish your command as you type, based on your command history. Press the right arrow key to accept",
                "Suggestions are powered by ble.sh, which is now included as a separate package (zyphor-blesh) and installed automatically with the Bash configuration",
                "Commands are now saved to your history immediately, so every open terminal shares the same history and nothing is lost if a terminal closes unexpectedly",
                "The history now remembers up to 50,000 commands, and duplicate entries and commands starting with a space are skipped",
                "If ble.sh is not available, typing the start of a command and pressing the up or down arrow still searches your history by prefix",
                "If fzf is installed, Ctrl+R opens a fuzzy search of your command history",
                "Tab completion is now case-insensitive and shows all matches at once when there is more than one",
                "Added default suggestion and syntax highlighting colors, which you can override by creating your own ~/.blerc file"
            ]
        },
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