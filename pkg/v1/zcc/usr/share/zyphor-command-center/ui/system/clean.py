from PyQt6.QtWidgets import QWidget, QVBoxLayout, QPushButton, QTextEdit
from core.process import ProcessManager
from ui.components.loading import LoadingIndicator

class CleanPage(QWidget):
    def __init__(self):
        super().__init__()
        layout = QVBoxLayout()
        self.setLayout(layout)

        self.console = QTextEdit()
        self.console.setReadOnly(True)

        self.process = ProcessManager(self.console.append)
        
        self.loading = LoadingIndicator()

        # 🔥 automatic binding
        self.process.started.connect(self.loading.start)
        self.process.finished.connect(self.loading.stop)

        btn = QPushButton("Run System Clean")
        btn.clicked.connect(self.run)

        layout.addWidget(btn)
        layout.addWidget(self.loading)
        layout.addWidget(self.console)

    def run(self):
        self.console.clear()
        self.process.run("pkexec zyphor system clean")