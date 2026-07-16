from PyQt6.QtWebEngineWidgets import QWebEngineView
from PyQt6.QtCore import QUrl

from . import server


class ProfilePage(QWebEngineView):

    def __init__(self):
        super().__init__()

        # Start Laravel if needed
        server.start()

        # Load the Laravel page
        self.load(QUrl("http://127.0.0.1:2437/profile.php"))