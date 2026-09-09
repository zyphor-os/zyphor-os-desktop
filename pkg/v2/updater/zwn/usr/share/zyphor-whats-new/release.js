const releaseNotes = {
    distro: "Zyphor OS Ada Lovelace LTS",
    version: "2.2.1-ada-lovelace-lts-u54",
    date: "Updated On: September 9, 2026 @ 08:25 PM",

    sections: [
        {
            "title": "Zyphor Package Search",
            "items": [
                "Added zyphor pkg search <keyword> for finding apps by name, package, type, channel, or source URL",
                "Search now checks the official app registry first, then falls back to apt, snap, and flatpak if the system has them installed",
                "Live fallback results are clearly marked 'unregistered' and de-duplicated against known registry entries",
                "Added an animated progress spinner (with Ctrl+C cancel support) while live package managers are queried in the background",
                "Registry results now show type, channel, and package/URL in a clean aligned table for faster scanning"
            ]
        },
        {
            "title": "Zyphor Package Install",
            "items": [
                "Added zyphor pkg install <app> [apt|snap|flatpak] to install apps directly from the Zyphor registry",
                "Apps not found in the registry can now be installed anyway via a direct apt, snap, or flatpak install, with an interactive picker if no manager is specified",
                "Snap installs now auto-detect and set up snapd if it isn't already present, and retry automatically if the first attempt fails",
                "Flatpak installs now auto-add the Flathub remote when missing, and generate a CLI shim in /usr/local/bin so flatpak apps can be launched by name",
                "Added SHA256 checksum verification for direct .deb downloads to protect against corrupted or tampered packages",
                "Registry-driven installs now support custom install flags and channels defined per app"
            ]
        },
        {
            "title": "Zyphor Package Remove",
            "items": [
                "Added zyphor pkg remove <app> to uninstall apps tracked in the Zyphor registry, with a confirmation prompt before anything is removed",
                "Apps not found in the registry can now still be removed: Zyphor checks apt, snap, and flatpak directly for a matching installed package",
                "When an unregistered app is installed via more than one package manager, Zyphor now prompts you to choose which one to remove",
                "An installation manager can now be specified directly (e.g. zyphor pkg remove <app> flatpak) to skip the picker prompt",
                "Flatpak removals now automatically clean up any CLI shim created during install",
                "Added an optional apt autoremove step after any removal to clean up now-unused dependencies"
            ]
        },
        {
            "title": "System Information Banner",
            "items": [
                "Introduced a new professional Zyphor OS terminal banner",
                "Added the current Zyphor OS build version to the system banner",
                "Added a clear upgrade message with the recommended Zyphor system upgrade command",
                "Improved terminal startup presentation with cleaner formatting and visual hierarchy"
            ]
        },
        {
            "title": "GRUB Bootloader Theme",
            "items": [
                "Updated the GRUB bootloader theme with the new Zyphor OS visual design",
                "Replaced the default Kali GRUB background with the Zyphor OS splash screen",
                "Added the Zyphor OS splash image as the GRUB desktop background",
                "Improved the OS selection menu layout with centered positioning and cleaner spacing",
                "Updated boot menu colors and selection styling for better readability",
                "Added a boot countdown message showing the remaining seconds before automatic boot"
            ]
        },
        {
            "title": "Package Updates",
            "items": [
                "Upgraded 10 packages, including zyphor-cli, zyphor-os-release, zyphor-repo-config, zyphor-whats-new, zysh, zywin, exfatprogs, ksnip, nano, and libmsgraph-1-1",
                "Installed 3 new dependencies: libkcolorpicker-qt6-0, libkimageannotator-qt6-0, and zyphor-archive-keyring",
                "Added zyphor-archive-keyring to provide the official Zyphor OS repository signing key for package authenticity verification",
                "Removed 6 obsolete packages, including older Qt5 dependencies and linux-base-7.0.12+kali-amd64 and linux-binary-7.0.12+kali-amd64",
                "Removed dependencies associated with older Linux kernel versions 6.19.14 and 7.0.12",
                "Cleaned up obsolete kernel dependencies and packages that are no longer required by the current Zyphor OS installation"
            ]
        },
        {
            "title": "ZyWin UI Installer and Zywin Configuration",
            "items": [
                "Added a Wine configuration check on launch that detects whether Wine is set up and prompts the user to run \"sudo zyphor setup wine\" if it isn't",
                "Application installs are handed off to the zywin CLI, with live status updates and error reporting surfaced in the UI",
                "Added a dark, Zyphor-branded interface consistent with the rest of the desktop",
                "zywin CLI now ensures the Wine prefix directory (~/.local/zywin/prefixes/) exists before use, creating it automatically if missing"
            ]
        },
        {
            "title": "Zyphor Package Repository Signing",
            "items": [
                "Added cryptographic GPG signing for the official Zyphor OS APT repository",
                "Implemented signed InRelease and Release.gpg repository metadata",
                "Added the official Zyphor OS archive keyring for APT repository verification",
                "APT now verifies repository authenticity using the Zyphor archive signing key instead of relying on trusted=yes",
                "Added automated repository signature generation and verification to the Zyphor build process"
            ]
        }
    ]
};