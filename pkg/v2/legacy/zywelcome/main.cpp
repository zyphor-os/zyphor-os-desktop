#include <QApplication>
#include <QWebEngineView>
#include <QProcess>
#include <QTimer>
#include <QObject>
#include <QDir>
#include <QDebug>

int main(int argc, char *argv[])
{
    QApplication app(argc, argv);

    // Optional: disable GPU if you're having rendering issues
    // qputenv("QTWEBENGINE_CHROMIUM_FLAGS", "--disable-gpu");

    // Create the web server process
    QProcess server;

    // Change this to your PHP project's directory
    server.setWorkingDirectory("/usr/share/zywelcome"); // production
    // server.setWorkingDirectory("zywelcome/usr/share/zywelcome"); // development

    // Start PHP
    server.start("php", QStringList()
                 << "-S"
                 << "localhost:2438");

    if (!server.waitForStarted(5000))
    {
        qDebug() << "Failed to start PHP server.";
        qDebug() << "Error:" << server.error();
        qDebug() << "Error String:" << server.errorString();
        return -1;
    }

    qDebug() << "PHP server started.";

    QWebEngineView view;
    view.resize(900, 500);
    view.setWindowTitle("Welcome To Zyphor OS!");

    // Wait a moment before loading the page
    QTimer::singleShot(2000, [&]() {
        view.load(QUrl("http://localhost:2438"));
    });

    view.show();

    // Stop the server when the application exits
    QObject::connect(&app, &QApplication::aboutToQuit, [&]() {

        qDebug() << "Stopping PHP server...";

        server.terminate();

        if (!server.waitForFinished(3000))
        {
            qDebug() << "Force killing PHP server...";
            server.kill();
            server.waitForFinished();
        }

        qDebug() << "PHP server stopped.";
    });

    return app.exec();
}