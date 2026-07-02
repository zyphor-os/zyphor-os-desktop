from PyQt6.QtWidgets import QMainWindow, QWidget, QHBoxLayout, QVBoxLayout

from ui.home import HomePage
from ui.sidebar import Sidebar
# system
from ui.system.upgrade import UpgradePage
from ui.system.clean import CleanPage
from ui.system.info import SysInfoPage
# doctor
from ui.doctor.fix import DoctorFixPage
from ui.doctor.report import DoctorReportPage
from ui.doctor.scan import DoctorScanPage
# packages
from ui.pkg.list import PkgListPage
from ui.pkg.search import PkgSearchPage
from ui.pkg.install import PkgInstallPage
from ui.pkg.remove import PkgRemovePage


class MainWindow(QMainWindow):
    def __init__(self):
        super().__init__()

        self.setWindowTitle("Zyphor Command Center")
        self.setGeometry(100, 100, 1000, 600)

        main_widget = QWidget()
        self.setCentralWidget(main_widget)

        layout = QHBoxLayout()
        main_widget.setLayout(layout)

        # Sidebar
        self.sidebar = Sidebar()

        # Content container
        self.container = QVBoxLayout()
        self.wrapper = QWidget()
        self.wrapper.setLayout(self.container)

        # Pages
        self.pages = {
            "home": HomePage(),
            "upgrade": UpgradePage(),
            "clean": CleanPage(),
            "sysinfo": SysInfoPage(),
            "doctor_fix": DoctorFixPage(),
            "doctor_report": DoctorReportPage(),
            "doctor_scan": DoctorScanPage(),
            "pkg_list": PkgListPage(),
            "pkg_search": PkgSearchPage(),
            "pkg_install": PkgInstallPage(),
            "pkg_remove": PkgRemovePage()
        }

        # Signals
        self.sidebar.home_clicked.connect(lambda: self.switch("home"))
        # system
        self.sidebar.upgrade_clicked.connect(lambda: self.switch("upgrade"))
        self.sidebar.clean_clicked.connect(lambda: self.switch("clean"))
        self.sidebar.sysinfo_clicked.connect(lambda: self.switch("sysinfo"))
        # doctor
        self.sidebar.doctor_fix_clicked.connect(lambda: self.switch("doctor_fix"))
        self.sidebar.doctor_report_clicked.connect(lambda: self.switch("doctor_report"))
        self.sidebar.doctor_scan_clicked.connect(lambda: self.switch("doctor_scan"))
        # packages
        self.sidebar.pkg_list_clicked.connect(lambda: self.switch("pkg_list"))
        self.sidebar.pkg_search_clicked.connect(lambda: self.switch("pkg_search"))
        self.sidebar.pkg_install_clicked.connect(lambda: self.switch("pkg_install"))
        self.sidebar.pkg_remove_clicked.connect(lambda: self.switch("pkg_remove"))

        layout.addWidget(self.sidebar)
        layout.addWidget(self.wrapper)

        self.switch("home")

    def switch(self, name):
        while self.container.count():
            item = self.container.takeAt(0)
            if item.widget():
                item.widget().setParent(None)

        self.container.addWidget(self.pages[name])