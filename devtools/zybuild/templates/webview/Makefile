COMPILER = g++
QTFLAGSWEB := $(shell pkg-config --cflags --libs Qt6Widgets Qt6WebEngineWidgets)

compile:
	$(COMPILER) main.cpp -o webview/usr/bin/webview $(QTFLAGSWEB)
	$(COMPILER) main.cpp -o init $(QTFLAGSWEB)

clean:
	sudo rm -rf webview/usr/bin/webview
	sudo rm -rf init
