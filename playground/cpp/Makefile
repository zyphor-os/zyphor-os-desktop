QTFLAGS := $(shell pkg-config --cflags --libs Qt6Widgets)
QTFLAGSWEB := $(shell pkg-config --cflags --libs Qt6Widgets Qt6WebEngineWidgets)

all:
# 	g++ main.cpp -o init
	g++ main.cpp \
	helpers/markjason.cpp \
	-o init

argu:
	g++ argument.cpp \
	helpers/markjason.cpp \
	-o init

dataArr:
	g++ data.cpp \
	helpers/markjason.cpp \
	-o init

guiApp:
	g++ gui.cpp -o init $(QTFLAGS)

weberView:
	g++ webview.cpp helpers/markjason.cpp -o init $(QTFLAGSWEB)

weberViewProject:
	g++ webserverwebview.cpp helpers/markjason.cpp -o init $(QTFLAGSWEB)