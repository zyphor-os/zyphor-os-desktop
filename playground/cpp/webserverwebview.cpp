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

    // Change this to your Laravel project's directory
    server.setWorkingDirectory("/home/programmer/projects/dict2");

    // Start Laravel
    server.start("php", QStringList()
                 << "artisan"
                 << "serve"
                 << "--host=127.0.0.1"
                 << "--port=2437");

    if (!server.waitForStarted(5000))
    {
        qDebug() << "Failed to start Laravel server.";
        qDebug() << "Error:" << server.error();
        qDebug() << "Error String:" << server.errorString();
        return -1;
    }

    qDebug() << "Laravel server started.";

    QWebEngineView view;
    view.resize(1200, 800);
    view.setWindowTitle("My Application");

    // Wait a moment before loading the page
    QTimer::singleShot(2000, [&]() {
        view.load(QUrl("http://127.0.0.1:2437"));
    });

    view.show();

    // Stop the server when the application exits
    QObject::connect(&app, &QApplication::aboutToQuit, [&]() {

        qDebug() << "Stopping Laravel server...";

        server.terminate();

        if (!server.waitForFinished(3000))
        {
            qDebug() << "Force killing Laravel server...";
            server.kill();
            server.waitForFinished();
        }

        qDebug() << "Laravel server stopped.";
    });

    return app.exec();
}