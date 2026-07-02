from PyQt6.QtWidgets import QWidget, QVBoxLayout, QTextEdit, QPushButton
from core.process import ProcessManager
from ui.components.loading import LoadingIndicator


class DoctorFixPage(QWidget):
    def __init__(self):
        super().__init__()

        layout = QVBoxLayout()
        self.setLayout(layout)

        # =========================
        # CONSOLE OUTPUT
        # =========================
        self.console = QTextEdit()
        self.console.setReadOnly(True)

        # =========================
        # LOADING
        # =========================
        self.loading = LoadingIndicator("Repairing system...")

        # =========================
        # PROCESS MANAGER
        # =========================
        self.process = ProcessManager(self.console.append)

        # 🔥 auto loading binding
        self.process.started.connect(self.loading.start)
        self.process.finished.connect(self.loading.stop)

        # =========================
        # BUTTON
        # =========================
        self.btn = QPushButton("Run Doctor Fix")
        self.btn.clicked.connect(self.run_fix)

        # =========================
        # LAYOUT
        # =========================
        layout.addWidget(self.btn)
        layout.addWidget(self.loading)
        layout.addWidget(self.console)

    # =========================
    # RUN FIX
    # =========================
    def run_fix(self):
        self.console.clear()
        self.process.run("pkexec zyphor doctor fix")