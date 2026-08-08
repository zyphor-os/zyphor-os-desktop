COMPILER = g++
CXXFLAGS = -std=c++17 -O2

BUILD = build
TARGET = $(BUILD)/zywin
SRC := src/*.cpp

all: $(TARGET)

$(TARGET): 
	mkdir -p $(BUILD)
	$(COMPILER) $(CXXFLAGS) $(SRC) -o $@

install: $(TARGET)
	install -Dm755 $(TARGET) zywin/usr/bin/zywin
	install -Dm755 $(TARGET) zywindow

clean:
	rm -rf $(BUILD)

.PHONY: all install clean