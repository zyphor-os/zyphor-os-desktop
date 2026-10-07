#include <QApplication>
#include <QWidget>
#include <QVBoxLayout>
#include <QHBoxLayout>
#include <QLabel>
#include <QLineEdit>
#include <QPushButton>
#include <QFileDialog>
#include <QMessageBox>
#include <QProcess>
#include <QProgressBar>
#include <QFrame>
#include <QStyle>
#include <QDir>
#include <QFileInfo>
#include <QDebug>
#include <QTimer>
#include <QStandardPaths>

// Launched by absolute path so a hijacked PATH cannot substitute a
// different binary; zywin is a hard package dependency and always
// installs here.
static constexpr auto ZYWIN_BINARY = "/usr/bin/zywin";

class ZyWinUI : public QWidget
{
public:

    ZyWinUI(QWidget *parent = nullptr)
        : QWidget(parent)
    {
        setWindowTitle("ZyWin Installer");
        setMinimumSize(800, 780);
        resize(900, 830);

        createUI();

        // Kick off the Wine configuration check once the UI is up.
        QTimer::singleShot(0, this, [this]() { checkWineConfigured(); });
    }

private:

    QLineEdit *filePathEdit;
    QPushButton *browseButton;
    QPushButton *installButton;
    QLabel *statusLabel;
    QLabel *statusIcon;
    QProgressBar *progressBar;

    // Wine check widgets
    QFrame *winePanel;
    QLabel *wineStatusIcon;
    QLabel *wineStatusLabel;
    QLabel *wineHintLabel;
    QProgressBar *wineCheckSpinner;

    QString selectedFile;
    bool wineConfigured = false;

    void createUI()
    {
        // -------------------------------------------------
        // Main layout
        // -------------------------------------------------

        QVBoxLayout *mainLayout = new QVBoxLayout(this);

        mainLayout->setContentsMargins(40, 35, 40, 30);
        mainLayout->setSpacing(20);


        // -------------------------------------------------
        // Header
        // -------------------------------------------------

        QLabel *title = new QLabel("ZyWin UI");

        title->setStyleSheet(
            "font-size: 36px;"
            "font-weight: bold;"
        );


        QLabel *subtitle =
            new QLabel("Install and run Windows applications on Zyphor OS");

        subtitle->setStyleSheet(
            "font-size: 17px;"
            "color: #9aa4b2;"
        );


        QVBoxLayout *headerLayout =
            new QVBoxLayout();

        headerLayout->setSpacing(2);

        headerLayout->addWidget(title);
        headerLayout->addWidget(subtitle);


        mainLayout->addLayout(headerLayout);


        // -------------------------------------------------
        // Wine configuration check panel
        // -------------------------------------------------

        winePanel = new QFrame();

        winePanel->setObjectName("panel");


        QVBoxLayout *wineLayout =
            new QVBoxLayout(winePanel);

        wineLayout->setContentsMargins(25, 25, 25, 25);
        wineLayout->setSpacing(15);


        QLabel *wineTitle =
            new QLabel("Wine Configuration");

        wineTitle->setStyleSheet(
            "font-size: 20px;"
            "font-weight: bold;"
        );


        QHBoxLayout *wineStatusRow =
            new QHBoxLayout();

        wineStatusRow->setSpacing(12);


        wineStatusIcon =
            new QLabel("●");

        wineStatusIcon->setStyleSheet(
            "font-size: 20px;"
            "color: #3b82f6;"
        );


        wineStatusLabel =
            new QLabel("Checking if Wine is configured...");

        wineStatusLabel->setStyleSheet(
            "font-size: 15px;"
            "color: #d6dce5;"
        );


        wineStatusRow->addWidget(wineStatusIcon);
        wineStatusRow->addWidget(wineStatusLabel);
        wineStatusRow->addStretch();


        wineCheckSpinner = new QProgressBar();

        wineCheckSpinner->setRange(0, 0);
        wineCheckSpinner->setTextVisible(false);


        wineHintLabel =
            new QLabel(
                "Wine isn't set up yet. Open a terminal and run "
                "\"sudo zyphor setup wine\", then relaunch ZyWin."
            );

        wineHintLabel->setStyleSheet(
            "color: #f59e0b;"
            "font-size: 13px;"
        );

        wineHintLabel->setWordWrap(true);
        wineHintLabel->setVisible(false);


        wineLayout->addWidget(wineTitle);
        wineLayout->addLayout(wineStatusRow);
        wineLayout->addWidget(wineCheckSpinner);
        wineLayout->addWidget(wineHintLabel);


        mainLayout->addWidget(winePanel);


        // -------------------------------------------------
        // Application selection panel
        // -------------------------------------------------

        QFrame *filePanel = new QFrame();

        filePanel->setObjectName("panel");


        QVBoxLayout *fileLayout =
            new QVBoxLayout(filePanel);

        fileLayout->setContentsMargins(25, 25, 25, 25);
        fileLayout->setSpacing(15);


        QLabel *fileTitle =
            new QLabel("Select Application File");

        fileTitle->setStyleSheet(
            "font-size: 20px;"
            "font-weight: bold;"
        );


        QLabel *fileDescription =
            new QLabel(
                "Choose a Windows application file to install with ZyWin."
            );

        fileDescription->setStyleSheet(
            "font-size: 14px;"
            "color: #aab4c3;"
        );


        // File input row

        QHBoxLayout *fileInputLayout =
            new QHBoxLayout();

        fileInputLayout->setSpacing(10);


        filePathEdit = new QLineEdit();

        filePathEdit->setPlaceholderText(
            "No file selected"
        );

        filePathEdit->setReadOnly(true);


        browseButton =
            new QPushButton("Browse...");

        browseButton->setMinimumWidth(120);

        // Disabled until the Wine check completes successfully.
        browseButton->setEnabled(false);


        fileInputLayout->addWidget(filePathEdit);
        fileInputLayout->addWidget(browseButton);


        QLabel *supportedLabel =
            new QLabel(
                "Supported formats: EXE, MSI, and ISO."
            );

        supportedLabel->setStyleSheet(
            "color: #8f9bad;"
            "font-size: 13px;"
        );


        fileLayout->addWidget(fileTitle);
        fileLayout->addWidget(fileDescription);
        fileLayout->addLayout(fileInputLayout);
        fileLayout->addWidget(supportedLabel);


        mainLayout->addWidget(filePanel);


        // -------------------------------------------------
        // Installation status panel
        // -------------------------------------------------

        QFrame *statusPanel = new QFrame();

        statusPanel->setObjectName("panel");


        QVBoxLayout *statusLayout =
            new QVBoxLayout(statusPanel);

        statusLayout->setContentsMargins(25, 25, 25, 25);
        statusLayout->setSpacing(15);


        QLabel *statusTitle =
            new QLabel("Installation Status");

        statusTitle->setStyleSheet(
            "font-size: 20px;"
            "font-weight: bold;"
        );


        QHBoxLayout *statusRow =
            new QHBoxLayout();

        statusRow->setSpacing(12);


        statusIcon =
            new QLabel("●");

        statusIcon->setStyleSheet(
            "font-size: 20px;"
            "color: #3b82f6;"
        );


        statusLabel =
            new QLabel("Ready to install.");

        statusLabel->setStyleSheet(
            "font-size: 15px;"
            "color: #d6dce5;"
        );


        statusRow->addWidget(statusIcon);
        statusRow->addWidget(statusLabel);
        statusRow->addStretch();


        progressBar = new QProgressBar();

        progressBar->setRange(0, 0);
        progressBar->setVisible(false);


        statusLayout->addWidget(statusTitle);
        statusLayout->addLayout(statusRow);
        statusLayout->addWidget(progressBar);


        mainLayout->addWidget(statusPanel);


        // -------------------------------------------------
        // Install button
        // -------------------------------------------------

        installButton = new QPushButton("Install with ZyWin");

        installButton->setMinimumHeight(50);

        installButton->setEnabled(false);


        mainLayout->addWidget(installButton);


        // -------------------------------------------------
        // Footer
        // -------------------------------------------------

        QHBoxLayout *footerLayout =
            new QHBoxLayout();


        QLabel *versionLabel =
            new QLabel("ZyWin Installer");

        versionLabel->setStyleSheet(
            "color: #7f8a9a;"
            "font-size: 13px;"
        );


        footerLayout->addWidget(versionLabel);
        footerLayout->addStretch();


        mainLayout->addLayout(footerLayout);


        // -------------------------------------------------
        // Signals
        // -------------------------------------------------

        connect(
            browseButton,
            &QPushButton::clicked,
            this,
            [this]()
            {
                selectApplication();
            }
        );


        connect(
            installButton,
            &QPushButton::clicked,
            this,
            [this]()
            {
                installApplication();
            }
        );


        // -------------------------------------------------
        // Dark theme
        // -------------------------------------------------

        setStyleSheet(

            "QWidget {"
            "    background-color: #0f1724;"
            "    color: #f1f5f9;"
            "    font-family: Sans Serif;"
            "}"

            "QFrame#panel {"
            "    background-color: #172131;"
            "    border: 1px solid #293548;"
            "    border-radius: 10px;"
            "}"

            "QLineEdit {"
            "    background-color: #0d1522;"
            "    border: 1px solid #344155;"
            "    border-radius: 7px;"
            "    padding: 12px;"
            "    color: #e5e7eb;"
            "}"

            "QLineEdit:focus {"
            "    border: 1px solid #3b82f6;"
            "}"

            "QPushButton {"
            "    background-color: #2563eb;"
            "    border: none;"
            "    border-radius: 7px;"
            "    padding: 10px 18px;"
            "    color: white;"
            "    font-weight: bold;"
            "}"

            "QPushButton:hover {"
            "    background-color: #3b82f6;"
            "}"

            "QPushButton:pressed {"
            "    background-color: #1d4ed8;"
            "}"

            "QPushButton:disabled {"
            "    background-color: #263247;"
            "    color: #68758a;"
            "}"

            "QProgressBar {"
            "    background-color: #0d1522;"
            "    border: none;"
            "    border-radius: 5px;"
            "    height: 8px;"
            "}"

            "QProgressBar::chunk {"
            "    background-color: #2563eb;"
            "    border-radius: 5px;"
            "}"
        );
    }


    // -----------------------------------------------------
    // Check whether Wine has been configured via
    // "sudo zyphor setup wine". We look for the wine
    // binary on PATH; ZyWin can't do anything useful
    // without it.
    // -----------------------------------------------------

    void checkWineConfigured()
    {
        wineCheckSpinner->setVisible(true);
        wineHintLabel->setVisible(false);

        wineStatusIcon->setStyleSheet(
            "font-size: 20px;"
            "color: #3b82f6;"
        );

        wineStatusLabel->setText(
            "Checking if Wine is configured..."
        );


        // Resolved in-process. Spawning "which" would itself be a PATH
        // lookup, so a planted "which" could fake the result.
        onWineCheckFinished(
            !QStandardPaths::findExecutable("wine").isEmpty()
        );
    }


    void onWineCheckFinished(bool configured)
    {
        wineConfigured = configured;

        wineCheckSpinner->setVisible(false);

        if (wineConfigured)
        {
            wineStatusIcon->setStyleSheet(
                "font-size: 20px;"
                "color: #22c55e;"
            );

            wineStatusLabel->setText(
                "Wine is configured."
            );

            wineHintLabel->setVisible(false);

            browseButton->setEnabled(true);

            // Install button re-enables once a file is selected.
        }
        else
        {
            wineStatusIcon->setStyleSheet(
                "font-size: 20px;"
                "color: #ef4444;"
            );

            wineStatusLabel->setText(
                "Wine is not configured."
            );

            wineHintLabel->setVisible(true);

            browseButton->setEnabled(false);
            installButton->setEnabled(false);
        }
    }


    // -----------------------------------------------------
    // Select application
    // -----------------------------------------------------

    void selectApplication()
    {
        if (!wineConfigured)
        {
            QMessageBox::warning(
                this,
                "Wine Not Configured",
                "Wine isn't set up yet.\n\n"
                "Run \"sudo zyphor setup wine\" in a terminal, "
                "then relaunch ZyWin UI."
            );

            return;
        }

        QString filePath =
            QFileDialog::getOpenFileName(
                this,
                "Select Windows Application",
                QDir::homePath(),
                "Windows Applications (*.exe *.msi *.iso)"
            );


        if (filePath.isEmpty())
            return;


        QFileInfo fileInfo(filePath);

        QString extension = fileInfo.suffix().toLower();


        if (
            extension != "exe" &&
            extension != "msi" &&
            extension != "iso"
        )
        {
            QMessageBox::warning(
                this,
                "Unsupported File",
                "Please select an EXE, MSI, or ISO file."
            );

            return;
        }


        selectedFile = filePath;

        filePathEdit->setText(filePath);

        statusLabel->setText(
            "Ready to install " +
            fileInfo.fileName()
        );

        statusIcon->setStyleSheet(
            "font-size: 20px;"
            "color: #22c55e;"
        );

        installButton->setEnabled(true);
    }


    // -----------------------------------------------------
    // Execute ZyWin
    // -----------------------------------------------------

    void installApplication()
    {
        if (!wineConfigured)
        {
            QMessageBox::warning(
                this,
                "Wine Not Configured",
                "Wine isn't set up yet.\n\n"
                "Run \"sudo zyphor setup wine\" in a terminal, "
                "then relaunch ZyWin."
            );

            return;
        }

        if (selectedFile.isEmpty())
        {
            return;
        }


        QFileInfo fileInfo(selectedFile);


        statusLabel->setText(
            "Installing " +
            fileInfo.fileName() +
            "..."
        );


        statusIcon->setStyleSheet(
            "font-size: 20px;"
            "color: #f59e0b;"
        );


        progressBar->setVisible(true);

        installButton->setEnabled(false);
        browseButton->setEnabled(false);


        QProcess *process =
            new QProcess(this);


        QStringList arguments;

        arguments << selectedFile;


        qDebug() << "Executing:";
        qDebug() << ZYWIN_BINARY << selectedFile;


        process->start(
            ZYWIN_BINARY,
            arguments
        );


        connect(
            process,
            &QProcess::started,
            this,
            [this]()
            {
                statusLabel->setText(
                    "ZyWin is running..."
                );
            }
        );


        connect(
            process,
            &QProcess::errorOccurred,
            this,
            [this, process](QProcess::ProcessError error)
            {
                Q_UNUSED(error);

                progressBar->setVisible(false);

                statusIcon->setStyleSheet(
                    "font-size: 20px;"
                    "color: #ef4444;"
                );

                statusLabel->setText(
                    "Failed to start ZyWin."
                );


                QMessageBox::critical(
                    this,
                    "ZyWin Error",
                    "Failed to start ZyWin.\n\n" +
                    process->errorString()
                );


                installButton->setEnabled(true);
                browseButton->setEnabled(true);
            }
        );


        connect(
            process,
            &QProcess::finished,
            this,
            [this, process](
                int exitCode,
                QProcess::ExitStatus exitStatus
            )
            {
                progressBar->setVisible(false);

                QString output =
                    process->readAllStandardOutput();

                QString errorOutput =
                    process->readAllStandardError();


                qDebug() << "ZyWin exit code:"
                         << exitCode;

                qDebug().noquote()
                    << output;

                qDebug().noquote()
                    << errorOutput;


                if (
                    exitStatus == QProcess::NormalExit &&
                    exitCode == 0
                )
                {
                    statusIcon->setStyleSheet(
                        "font-size: 20px;"
                        "color: #22c55e;"
                    );

                    statusLabel->setText(
                        "Installation completed successfully."
                    );
                }
                else
                {
                    statusIcon->setStyleSheet(
                        "font-size: 20px;"
                        "color: #ef4444;"
                    );

                    statusLabel->setText(
                        "Installation failed."
                    );


                    QMessageBox::critical(
                        this,
                        "ZyWin Error",
                        "ZyWin failed to install the application.\n\n"
                        "Exit code: " +
                        QString::number(exitCode)
                    );
                }


                installButton->setEnabled(true);
                browseButton->setEnabled(true);

                process->deleteLater();
            }
        );
    }
};

int main(int argc, char *argv[])
{
    QApplication app(argc, argv);

    ZyWinUI window;
    window.show();

    return app.exec();
}