# COMPILER = g++
# QTFLAGSWEB := $(shell pkg-config --cflags --libs Qt6Widgets Qt6WebEngineWidgets)

# compile:
# 	$(COMPILER) main.cpp -o zywin-ui/usr/bin/zywin-ui $(QTFLAGSWEB)
# 	$(COMPILER) main.cpp -o init $(QTFLAGSWEB)

# clean:
# 	sudo rm -rf zywin-ui/usr/bin/zywin-ui
# 	sudo rm -rf init

COMPILER = g++

QTFLAGS := $(shell pkg-config --cflags --libs Qt6Widgets)

CXXFLAGS = -Wall -std=c++17 -O2 -Wextra -Wconversion -Wformat 
SECFLAG := -fstack-protector-strong -fPIE -pie -D_FORTIFY_SOURCE=2 -Wl,-z,relro,-z,now -Wformat-security

TARGET = zywin-ui/usr/bin/zywin-ui
INIT = init

compile:
	mkdir -p zywin-ui/usr/bin
	$(COMPILER) $(CXXFLAGS) main.cpp -o  $(TARGET) $(SECFLAG) $(QTFLAGS)
	$(COMPILER) $(CXXFLAGS) main.cpp -o $(INIT) $(SECFLAG) $(QTFLAGS)

clean:
	rm -f $(TARGET)
	rm -f $(INIT)