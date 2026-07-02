from PyQt6.QtWidgets import QWidget, QVBoxLayout
from PyQt6.QtWebEngineWidgets import QWebEngineView
from PyQt6.QtCore import QUrl


class HomePage(QWidget):
    def __init__(self):
        super().__init__()

        layout = QVBoxLayout(self)
        layout.setContentsMargins(0, 0, 0, 0)

        webview = QWebEngineView()

        # Load local HTML file
        webview.load(
            QUrl.fromLocalFile(
                "/usr/share/zyphor-whats-new/release-notes.html"
            )
        )

        layout.addWidget(webview)