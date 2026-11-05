const releaseNotes = {
    distro: "Zyphor OS 3 \"Bethany\" LTS",
    version: "3.2.0-bethany-lts-u7",
    date: "Updated On: November 05, 2026 @ 04:12 PM",

    sections: [
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