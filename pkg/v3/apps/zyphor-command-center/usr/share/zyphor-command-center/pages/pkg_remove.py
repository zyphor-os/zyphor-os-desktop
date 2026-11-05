from core.registry import page
from ui.components.command_page import InputCommandPage


@page(id="pkg_remove", title="Uninstall Packages/Apps", section="Packages", icon="✖", order=36)
class PkgRemovePage(InputCommandPage):
    description = "Remove a package or app from this system."
    placeholder = "Enter app name to remove"
    button_text = "Remove Package"
    loading_text = "Removing package…"
    empty_message = "Zyphor: ✖ Missing app name\n"
    confirm = "Remove '{value}' from system?"
    # The GUI popup above replaces the CLI's yes/no question, so we answer "y" for it.
    command = "printf 'y\\n' | pkexec zyphor pkg remove {value}"
