#include <QApplication>
#include <QWebEngineView>

int main(int argc, char *argv[])
{
    QApplication app(argc, argv);

    QWebEngineView view;
    view.load(QUrl("https://qt.io"));
// or
// view.load(QUrl("http://localhost:3000"));

    view.resize(1200, 800);
    view.show();

    return app.exec();
}