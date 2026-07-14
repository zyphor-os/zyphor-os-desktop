from app import MainWindow
from PyQt6.QtWidgets import QApplication
from ui.profile import server
import sys

if __name__ == "__main__":
    app = QApplication(sys.argv)

    window = MainWindow()
    window.show()

    exit_code = app.exec()

    server.stop()

    sys.exit(exit_code)