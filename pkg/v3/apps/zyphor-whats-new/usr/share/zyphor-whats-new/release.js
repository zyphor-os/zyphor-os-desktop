const releaseNotes = {
    distro: "Zyphor OS 3 \"Bethany\" LTS",
    version: "3.0.0-bethany-lts-u2",
    date: "Updated On: October 8, 2026 @ 07:44 AM",

    sections: [
        {
            "title": "New Desktop Environment",
            "items": [
                "Introduced a refreshed Zyphor OS desktop environment with an updated visual experience",
                "Added a new default desktop layout with updated panel launchers and desktop shortcuts",
                "Added new default wallpapers and refreshed desktop artwork",
                "Updated the default application launcher for a cleaner and more accessible desktop experience",
                "Improved the default XFCE desktop configuration for new installations and newly created users",
                "Desktop environment updates can be applied through sudo zy system upgrade",
                "New desktop configurations are applied to newly created users through /etc/skel",
                "Existing users can keep their current desktop configuration until they deliberately apply the new desktop environment updates",
                "Desktop configuration updates are being tested in a virtual machine to help prevent existing user configurations from being overwritten or damaged"
            ]
        }
    ]
};