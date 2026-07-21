COMPILER = g++
QTFLAGSWEB := $(shell pkg-config --cflags --libs Qt6Widgets Qt6WebEngineWidgets)

compile:
	$(COMPILER) main.cpp -o zywelcome/usr/bin/zywelcome $(QTFLAGSWEB)
	$(COMPILER) main.cpp -o init $(QTFLAGSWEB)

clean:
	sudo rm -rf zywelcome/usr/bin/zywelcome
	sudo rm -rf init