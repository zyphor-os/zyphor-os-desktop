from PyQt6.QtWidgets import (
    QWidget, QVBoxLayout, QTextEdit,
    QPushButton, QLineEdit
)
from core.process import ProcessManager
from ui.components.loading import LoadingIndicator


class PkgSearchPage(QWidget):
    def __init__(self):
        super().__init__()

        layout = QVBoxLayout()
        self.setLayout(layout)

        # =========================
        # INPUT
        # =========================
        self.input = QLineEdit()
        self.input.setPlaceholderText("Search packages (e.g. firefox, snap, editor)")

        # =========================
        # BUTTON
        # =========================
        self.btn = QPushButton("Search")
        self.btn.clicked.connect(self.run_search)

        # =========================
        # OUTPUT
        # =========================
        self.console = QTextEdit()
        self.console.setReadOnly(True)

        # =========================
        # LOADING
        # =========================
        self.loading = LoadingIndicator("Searching packages...")

        # =========================
        # PROCESS MANAGER
        # =========================
        self.process = ProcessManager(self.console.append)

        # 🔥 auto loading binding
        self.process.started.connect(self.loading.start)
        self.process.finished.connect(self.loading.stop)

        # =========================
        # LAYOUT
        # =========================
        layout.addWidget(self.input)
        layout.addWidget(self.btn)
        layout.addWidget(self.loading)
        layout.addWidget(self.console)

    # =========================
    # RUN SEARCH
    # =========================
    def run_search(self):
        keyword = self.input.text().strip()

        if not keyword:
            self.console.append("Zyphor: ✖ Please enter a search keyword\n")
            return

        self.console.clear()
        self.process.run(f"zyphor pkg search {keyword}")