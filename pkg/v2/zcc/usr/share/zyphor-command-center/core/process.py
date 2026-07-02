from PyQt6.QtCore import QProcess, QObject, pyqtSignal

class ProcessManager(QObject):
    started = pyqtSignal()
    finished = pyqtSignal()

    def __init__(self, output_callback):
        super().__init__()

        self.output_callback = output_callback

        self.process = QProcess()
        self.process.readyReadStandardOutput.connect(self.read_output)
        self.process.readyReadStandardError.connect(self.read_output)

        # 🔥 AUTO HOOK lifecycle
        self.process.started.connect(self.started)
        self.process.finished.connect(self.finished)

    def run(self, command):
        self.process.start("bash", ["-c", command])

    def read_output(self):
        data = self.process.readAllStandardOutput().data().decode()
        err = self.process.readAllStandardError().data().decode()

        if data:
            self.output_callback(data)
        if err:
            self.output_callback(err)