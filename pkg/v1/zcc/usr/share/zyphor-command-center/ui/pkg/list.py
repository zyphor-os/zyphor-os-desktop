from PyQt6.QtWidgets import QWidget, QVBoxLayout, QTextEdit, QPushButton
from core.process import ProcessManager
from ui.components.loading import LoadingIndicator


class PkgListPage(QWidget):
    def __init__(self):
        super().__init__()

        layout = QVBoxLayout()
        self.setLayout(layout)

        # =========================
        # OUTPUT (TERMINAL STYLE)
        # =========================
        self.console = QTextEdit()
        self.console.setReadOnly(True)

        # =========================
        # LOADING
        # =========================
        self.loading = LoadingIndicator("Loading packages...")

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
        self.btn = QPushButton("Refresh Package List")
        self.btn.clicked.connect(self.run_list)

        layout.addWidget(self.btn)
        layout.addWidget(self.loading)
        layout.addWidget(self.console)

        # auto load on start
        self.run_list()

    # =========================
    # RUN COMMAND
    # =========================
    def run_list(self):
        self.console.clear()
        self.process.run("zyphor pkg list")