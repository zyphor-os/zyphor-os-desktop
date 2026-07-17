#include <QApplication>
#include <QMainWindow>
#include <QLabel>
#include <QPushButton>
#include <QLineEdit>
#include <QTextEdit>
#include <QComboBox>
#include <QCheckBox>
#include <QRadioButton>
#include <QProgressBar>
#include <QGroupBox>
#include <QVBoxLayout>
#include <QHBoxLayout>
#include <QMenuBar>
#include <QStatusBar>
#include <QToolBar>
#include <QAction>
#include <QMessageBox>

int main(int argc, char *argv[])
{
    QApplication app(argc, argv);

    // Main Window
    QMainWindow window;
    window.setWindowTitle("Qt Widgets Demo");
    window.resize(700, 500);

    // Menu
    QMenu *fileMenu = window.menuBar()->addMenu("&File");
    QAction *exitAction = fileMenu->addAction("Exit");

    // Toolbar
    QToolBar *toolbar = window.addToolBar("Toolbar");
    toolbar->addAction(exitAction);

    // Status Bar
    window.statusBar()->showMessage("Ready");

    // Central Widget
    QWidget *central = new QWidget();
    window.setCentralWidget(central);

    // Main Layout
    QVBoxLayout *layout = new QVBoxLayout();

    // Title
    QLabel *title = new QLabel("<h2>Qt6 Widget Showcase</h2>");
    layout->addWidget(title);

    // ===========================
    // Row 1 (Bootstrap-like)
    // ===========================
    QHBoxLayout *row = new QHBoxLayout();

    // Left Column
    QVBoxLayout *leftCol = new QVBoxLayout();

    QLabel *nameLabel = new QLabel("Name");
    QLineEdit *nameEdit = new QLineEdit();
    nameEdit->setPlaceholderText("Enter your name");

    leftCol->addWidget(nameLabel);
    leftCol->addWidget(nameEdit);

    // Right Column
    QVBoxLayout *rightCol = new QVBoxLayout();

    QLabel *passLabel = new QLabel("Password");
    QLineEdit *passEdit = new QLineEdit();
    passEdit->setPlaceholderText("Enter your password");
    passEdit->setEchoMode(QLineEdit::Password);

    rightCol->addWidget(passLabel);
    rightCol->addWidget(passEdit);

    // col-6 / col-6
    row->addLayout(leftCol, 6);
    row->addLayout(rightCol, 6);

    layout->addLayout(row);

    // Programming Language
    QLabel *courseLabel = new QLabel("Programming Language:");
    QComboBox *combo = new QComboBox();
    combo->addItems({
        "C++",
        "Python",
        "PHP",
        "Java",
        "Rust"
    });

    layout->addWidget(courseLabel);
    layout->addWidget(combo);

    // Remember Me
    QCheckBox *check = new QCheckBox("Remember Me");
    layout->addWidget(check);

    // Gender
    QGroupBox *genderBox = new QGroupBox("Gender");
    QVBoxLayout *genderLayout = new QVBoxLayout();

    QRadioButton *male = new QRadioButton("Male");
    QRadioButton *female = new QRadioButton("Female");

    genderLayout->addWidget(male);
    genderLayout->addWidget(female);

    genderBox->setLayout(genderLayout);
    layout->addWidget(genderBox);

    // Bio
    QLabel *bioLabel = new QLabel("Bio");
    QTextEdit *bio = new QTextEdit();
    bio->setPlaceholderText("Write something...");

    layout->addWidget(bioLabel);
    layout->addWidget(bio);

    // Progress
    QProgressBar *progress = new QProgressBar();
    progress->setValue(30);

    layout->addWidget(progress);

    // Buttons
    QHBoxLayout *buttonLayout = new QHBoxLayout();

    QPushButton *submit = new QPushButton("Submit");
    QPushButton *clear = new QPushButton("Clear");

    buttonLayout->addStretch();
    buttonLayout->addWidget(submit);
    buttonLayout->addWidget(clear);

    layout->addLayout(buttonLayout);

    central->setLayout(layout);

    // Submit
    QObject::connect(submit, &QPushButton::clicked, [&]() {

        QString info =
            "Name: " + nameEdit->text() +
            "\nPassword: " + passEdit->text() +
            "\nLanguage: " + combo->currentText() +
            "\nRemember: " + QString(check->isChecked() ? "Yes" : "No");

        QMessageBox::information(&window, "Submitted", info);

        progress->setValue(100);
        window.statusBar()->showMessage("Submitted successfully");
    });

    // Clear
    QObject::connect(clear, &QPushButton::clicked, [&]() {

        nameEdit->clear();
        passEdit->clear();
        bio->clear();
        check->setChecked(false);
        male->setAutoExclusive(false);
        female->setAutoExclusive(false);
        male->setChecked(false);
        female->setChecked(false);
        male->setAutoExclusive(true);
        female->setAutoExclusive(true);

        progress->setValue(0);

        window.statusBar()->showMessage("Cleared");
    });

    // Exit
    QObject::connect(exitAction, &QAction::triggered,
                     &window, &QMainWindow::close);

    window.show();

    return app.exec();
}