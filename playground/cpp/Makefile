QTFLAGS := $(shell pkg-config --cflags --libs Qt6Widgets)

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