from PyQt6.QtWidgets import QWidget, QVBoxLayout, QTextEdit, QPushButton, QLineEdit
from core.process import ProcessManager
from ui.components.loading import LoadingIndicator


class PkgInstallPage(QWidget):
    def __init__(self):
        super().__init__()

        layout = QVBoxLayout()
        self.setLayout(layout)

        # =========================
        # INPUT
        # =========================
        self.input = QLineEdit()
        self.input.setPlaceholderText("Enter app name (e.g. firefox, vscode)")

        # =========================
        # BUTTON
        # =========================
        self.btn = QPushButton("Install")
        self.btn.clicked.connect(self.run_install)

        # =========================
        # CONSOLE
        # =========================
        self.console = QTextEdit()
        self.console.setReadOnly(True)

        # =========================
        # LOADING
        # =========================
        self.loading = LoadingIndicator("Installing...")

        # =========================
        # PROCESS MANAGER (ONLY ONCE)
        # =========================
        self.process = ProcessManager(self.console.append)

        # 🔥 auto binding
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
    # RUN INSTALL
    # =========================
    def run_install(self):
        app = self.input.text().strip()

        if not app:
            self.console.append("Zyphor: ✖ Missing app name\n")
            return

        self.console.clear()

        self.process.run(f"pkexec zyphor pkg install {app}")