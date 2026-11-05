from core.registry import page
from ui.components.command_page import InputCommandPage


@page(id="pkg_install", title="Install via APT", section="Packages", icon="⤓", order=32)
class PkgInstallPage(InputCommandPage):
    description = "Install a package or app. Uses the Zyphor repository, or apt if it isn't listed."
    placeholder = "Enter app name (e.g. firefox, vscode)"
    button_text = "Install"
    loading_text = "Installing…"
    empty_message = "Zyphor: ✖ Missing app name\n"
    command = "pkexec zyphor pkg install {value} apt"


@page(id="pkg_install_snap", title="Install via Snap", section="Packages", icon="⤓", order=33)
class PkgInstallSnapPage(InputCommandPage):
    description = "Install an app directly from the Snap store."
    placeholder = "Enter snap name (e.g. spotify)"
    button_text = "Install Snap"
    loading_text = "Installing via snap…"
    empty_message = "Zyphor: ✖ Missing app name\n"
    command = "pkexec zyphor pkg install {value} snap"


@page(id="pkg_install_flatpak", title="Install via Flatpak", section="Packages", icon="⤓", order=34)
class PkgInstallFlatpakPage(InputCommandPage):
    description = "Install an app directly from Flathub (use the app ID, e.g. com.spotify.Client)."
    placeholder = "Enter Flatpak app ID"
    button_text = "Install Flatpak"
    loading_text = "Installing via flatpak…"
    empty_message = "Zyphor: ✖ Missing app name\n"
    command = "pkexec zyphor pkg install {value} flatpak"