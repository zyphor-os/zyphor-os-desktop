from PyQt6.QtWidgets import QWidget, QVBoxLayout, QTextEdit, QPushButton
from core.process import ProcessManager
from ui.components.loading import LoadingIndicator


class DoctorScanPage(QWidget):
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
        self.loading = LoadingIndicator("Scanning system health...")

        # =========================
        # PROCESS MANAGER
        # =========================
        self.process = ProcessManager(self.console.append)

        # 🔥 auto binding
        self.process.started.connect(self.loading.start)
        self.process.finished.connect(self.loading.stop)

        # =========================
        # BUTTON
        # =========================
        self.btn = QPushButton("Run System Scan")
        self.btn.clicked.connect(self.run_scan)

        # =========================
        # LAYOUT
        # =========================
        layout.addWidget(self.btn)
        layout.addWidget(self.loading)
        layout.addWidget(self.console)

    # =========================
    # RUN SCAN
    # =========================
    def run_scan(self):
        self.console.clear()
        self.process.run("zyphor doctor scan")