const releaseNotes = {
    distro: "Zyphor OS Ada Lovelace LTS",
    version: "2.2.1-ada-lovelace-lts-u29",
    date: "Updated On: July 14, 2026 @ 11:33 AM",

    sections: [
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