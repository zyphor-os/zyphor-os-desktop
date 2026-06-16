from PyQt6.QtWidgets import QWidget, QVBoxLayout, QTextEdit, QPushButton
from core.process import ProcessManager
from ui.components.loading import LoadingIndicator


class DoctorReportPage(QWidget):
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
        self.loading = LoadingIndicator("Generating system report...")

        # =========================
        # PROCESS MANAGER
        # =========================
        self.process = ProcessManager(self.console.append)

        # 🔥 auto loading binding
        self.process.started.connect(self.loading.start)
        self.process.finished.connect(self.on_finished)

        # =========================
        # BUTTON
        # =========================
        self.btn = QPushButton("Generate System Report")
        self.btn.clicked.connect(self.run_report)

        # =========================
        # LAYOUT
        # =========================
        layout.addWidget(self.btn)
        layout.addWidget(self.loading)
        layout.addWidget(self.console)

    # =========================
    # RUN REPORT
    # =========================
    def run_report(self):
        self.console.clear()
        self.process.run("pkexec zyphor doctor report")

    # =========================
    # AFTER PROCESS FINISH
    # =========================
    def on_finished(self):
        self.loading.stop()
        self.load_report_file()

    # =========================
    # LOAD SAVED FILE
    # =========================
    def load_report_file(self):
        try:
            with open("/tmp/zyphor_doctor_report.txt", "r") as f:
                content = f.read()

            self.console.append("\n\n--- SYSTEM REPORT ---\n")
            self.console.append(content)

        except Exception as e:
            self.console.append(f"\nFailed to read report file: {e}")