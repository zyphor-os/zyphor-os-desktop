from PyQt6.QtWidgets import QWidget, QVBoxLayout, QTextEdit, QPushButton
from PyQt6.QtCore import QProcess


class SysInfoPage(QWidget):
    def __init__(self):
        super().__init__()

        layout = QVBoxLayout()
        self.setLayout(layout)

        # Console output
        self.console = QTextEdit()
        self.console.setReadOnly(True)

        # Button
        self.btn = QPushButton("Run System Info")
        self.btn.clicked.connect(self.run_info)

        # Process
        self.process = QProcess()
        self.process.readyReadStandardOutput.connect(self.read_stdout)
        self.process.readyReadStandardError.connect(self.read_stdout)

        layout.addWidget(self.btn)
        layout.addWidget(self.console)

    def run_info(self):
        self.console.clear()
        self.process.start("bash", ["-c", "zyphor system info"])

    def read_stdout(self):
        data = self.process.readAllStandardOutput().data().decode()
        self.console.append(data)