from PyQt6.QtWidgets import QFrame, QVBoxLayout, QPushButton
from PyQt6.QtCore import pyqtSignal

class Sidebar(QFrame):
    home_clicked = pyqtSignal()

    upgrade_clicked = pyqtSignal()
    clean_clicked = pyqtSignal()
    sysinfo_clicked = pyqtSignal()

    doctor_fix_clicked = pyqtSignal()
    doctor_report_clicked = pyqtSignal()
    doctor_scan_clicked = pyqtSignal()

    pkg_list_clicked = pyqtSignal()
    pkg_search_clicked = pyqtSignal()
    pkg_install_clicked = pyqtSignal()
    pkg_remove_clicked = pyqtSignal()

    def __init__(self):
        super().__init__()
        self.setFixedWidth(220)
        self.setStyleSheet("background-color:#2c3e50;")

        layout = QVBoxLayout()
        self.setLayout(layout)

        btn_home = QPushButton("Home")
        # system
        btn_upgrade = QPushButton("System Upgrade")
        btn_clean = QPushButton("System Clean")
        btn_sysinfo = QPushButton("System Info")
        # doctor
        btn_doctor_fix = QPushButton("Doctor Fix")
        btn_doctor_report = QPushButton("Doctor Report")
        btn_doctor_scan = QPushButton("Doctor Scan")
        # pkg
        btn_pkg_list = QPushButton("Packages/Apps")
        btn_pkg_search = QPushButton("Search Packages/Apps")
        btn_pkg_install = QPushButton("Install Packages/Apps")
        btn_pkg_remove = QPushButton("Uninstall Packages/Apps")

        btn_home.clicked.connect(self.home_clicked)
        # system
        btn_upgrade.clicked.connect(self.upgrade_clicked)
        btn_clean.clicked.connect(self.clean_clicked)
        btn_sysinfo.clicked.connect(self.sysinfo_clicked)
        # doctor
        btn_doctor_fix.clicked.connect(self.doctor_fix_clicked)
        btn_doctor_report.clicked.connect(self.doctor_report_clicked)
        btn_doctor_scan.clicked.connect(self.doctor_scan_clicked)
        # pkg
        btn_pkg_list.clicked.connect(self.pkg_list_clicked)
        btn_pkg_search.clicked.connect(self.pkg_search_clicked)
        btn_pkg_install.clicked.connect(self.pkg_install_clicked)
        btn_pkg_remove.clicked.connect(self.pkg_remove_clicked)

        layout.addWidget(btn_home)
        # system
        layout.addWidget(btn_upgrade)
        layout.addWidget(btn_clean)
        layout.addWidget(btn_sysinfo)
        # doctor
        layout.addWidget(btn_doctor_fix)
        layout.addWidget(btn_doctor_report)
        layout.addWidget(btn_doctor_scan)
        # pkg
        layout.addWidget(btn_pkg_list)
        layout.addWidget(btn_pkg_search)
        layout.addWidget(btn_pkg_install)
        layout.addWidget(btn_pkg_remove)

        layout.addStretch()