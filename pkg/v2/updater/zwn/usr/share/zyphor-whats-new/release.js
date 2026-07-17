const releaseNotes = {
    distro: "Zyphor OS Ada Lovelace LTS",
    version: "2.2.1-ada-lovelace-lts-u30",
    date: "Updated On: July 17, 2026 @ 07:01 PM",

    sections: [
        {
            "title": "✨ Profile Management Improvements",
            "items": [
                "Introduced a redesigned Profile page for easier account management.",
                "Users can now upload and update their profile picture directly from the Command Center.",
                "Added automatic square cropping for uploaded profile images.",
                "Improved image validation to support PNG and JPG formats.",
                "Enhanced profile data loading and saving reliability.",
                "Profile images are now displayed in the desktop panel Start Menu for a more personalized experience.",
                "Integrated user profile pictures into the LightDM login screen.",
                "Optimized profile picture processing for better quality and consistency.",
                "Improved the overall profile management user experience."
            ]
        },
        {
            "title": "Change Log - Latest",
            "items": [
                "Relocated the release variable from release.js to release-notes.html for easier maintenance."
            ]
        },
        {
            "title": "Repository Configuration",
            "items": [
                "Updated the default Zyphor OS repository configuration to use the 'arch=amd64' filter.",
                "APT will now only fetch Zyphor packages for the AMD64 architecture, reducing unnecessary package index downloads.",
                "This change improves repository consistency and prepares the package manager for future multi-architecture support."
            ]
        },
        {
            "title": "Package Manager",
            "items": [
                "Added Flatpak support to 'zyphor pkg install', 'zyphor pkg list', and 'zyphor pkg remove', including automatic Flathub remote setup and retry-on-failure handling.",
                "Installing a Flatpak app now creates a CLI shim (e.g. 'wechat') so it can be launched by name instead of requiring 'flatpak run <app-id>'.",
                "Removing a Flatpak app now also cleans up its associated CLI shim."
            ]
        }
    ]
};