from core.registry import page
from ui.components.web_page import WebPage


@page(id="zyphor-cli-commands", title="Zyphor CLI Commands", section="Help", icon=">", order=34)
class HomePage(WebPage):
    # url = "/home/markjason/zyphor-os-desktop/pkg/v2/apps/zyphor-command-center/usr/share/zyphor-command-center/pages/html_pages/commands.html"
    url = "/usr/share/zyphor-command-center/pages/html_pages/commands.html"
