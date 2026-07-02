from PyQt6.QtWidgets import QLabel
from PyQt6.QtCore import Qt


class LoadingIndicator(QLabel):
    def __init__(self, text="Running..."):
        super().__init__(text)

        self.setAlignment(Qt.AlignmentFlag.AlignCenter)
        self.setVisible(False)

    def start(self, text="Running..."):
        self.setText(text)
        self.setVisible(True)

    def stop(self):
        self.setVisible(False)