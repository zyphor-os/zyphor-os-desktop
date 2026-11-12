const releaseNotes = {
    distro: "Zyphor OS Ada Lovelace LTS",
    version: "2.2.1-ada-lovelace-lts-u75",
    date: "Updated On: November 12, 2026 @ 06:47 PM",

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
            "title": "Git Branch in Terminal Prompt",
            "items": [
                "Added a git branch indicator to the default bash prompt, so the current branch name is shown right after the working directory whenever you are inside a git repository",
                "A '*' appears after the branch name when the repository has uncommitted changes, so you can tell at a glance whether your working tree is clean",
                "On a detached HEAD, the prompt shows the short commit hash instead of a branch name",
                "The branch is shown in yellow when the terminal supports colors, and in plain text otherwise, so the prompt works in both modes",
                "Nothing extra is shown outside a git repository, so the prompt stays clean in all other folders"
            ]
        },
        {
            "title": "Windows/Super Key Opens Whisker Menu",
            "items": [
                "Configured the Windows/Super key to trigger the XFCE Whisker Menu, providing a familiar Start Menu experience similar to Windows",
                "Improved keyboard navigation by allowing users to access the application menu directly using the Windows/Super key"
            ]
        },
        {
            "title": "Redesigned Zyphor Command Center",
            "items": [
                "Introduced a completely redesigned Zyphor Command Center with a modern dark look",
                "Added a new sidebar with the Zyphor logo, grouped sections (Account, System, Doctor, Packages), and a highlighted current page",
                "Every page now has a clear title and a short description of what it does",
                "Added a terminal-style output panel with easier-to-read text and a Clear button",
                "Added a progress animation while System Upgrade, System Clean, Doctor, and Package tasks are running",
                "Added clear ✔ Finished and ✖ Failed messages at the end of every task, so you always know how it went",
                "Task buttons are now disabled while a task is running to prevent starting the same task twice",
                "Pressing Enter in the Search, Install, and Uninstall boxes now starts the action",
                "Colored terminal codes are no longer shown as unreadable symbols in the output panel",
                "Command Center now opens faster, and each page loads only when you open it",
                "The Packages/Apps list now loads automatically the first time you open it",
                "User Management now starts its local service only when you open it",
                "If a page fails to open, an error message is shown instead of closing the whole app"
            ]
        },
        {
            "title": "Security & Reliability",
            "items": [
                "Improved the safety of the Search, Install, and Uninstall package boxes so typed text can no longer be run as system commands",
                "Package names that begin with a dash are now rejected as invalid input",
                "Improved how long task output is displayed, so lines no longer break in the wrong places"
            ]
        },
        {
            "title": "GRUB OS Probing",
            "items": [
                "Automatically enables GRUB OS probing to detect other operating systems during boot",
                "Automatically regenerates the GRUB configuration after enabling OS probing"
            ]
        }
    ]
};