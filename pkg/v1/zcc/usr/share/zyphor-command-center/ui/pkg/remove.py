from PyQt6.QtWidgets import (
    QWidget, QVBoxLayout, QTextEdit,
    QPushButton, QLineEdit, QMessageBox
)
from core.process import ProcessManager
from ui.components.loading import LoadingIndicator


class PkgRemovePage(QWidget):
    def __init__(self):
        super().__init__()

        layout = QVBoxLayout()
        self.setLayout(layout)

        # =========================
        # INPUT
        # =========================
        self.input = QLineEdit()
        self.input.setPlaceholderText("Enter app name to remove")

        # =========================
        # BUTTON
        # =========================
        self.btn = QPushButton("Remove Package")
        self.btn.clicked.connect(self.confirm_remove)

        # =========================
        # CONSOLE
        # =========================
        self.console = QTextEdit()
        self.console.setReadOnly(True)

        # =========================
        # LOADING
        # =========================
        self.loading = LoadingIndicator("Removing package...")

        # =========================
        # PROCESS MANAGER
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
    # CONFIRM (GUI REPLACES CLI PROMPT)
    # =========================
    def confirm_remove(self):
        app = self.input.text().strip()

        if not app:
            self.console.append("Zyphor: ✖ Missing app name\n")
            return

        reply = QMessageBox.question(
            self,
            "Confirm Removal",
            f"Remove '{app}' from system?",
            QMessageBox.StandardButton.Yes | QMessageBox.StandardButton.No
        )

        if reply == QMessageBox.StandardButton.Yes:
            self.run_remove(app)

    # =========================
    # RUN REMOVE
    # =========================
    def run_remove(self, app):
        self.console.clear()

        # IMPORTANT:
        # We bypass CLI prompt using forced input
        cmd = f"printf 'y\\n' | pkexec zyphor pkg remove {app}"

        self.process.run(cmd)