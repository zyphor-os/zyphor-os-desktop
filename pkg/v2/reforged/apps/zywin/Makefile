COMPILER = g++
CXXFLAGS = -Wall -std=c++17 -O2 -Wextra -Wconversion -Wformat 
SECFLAG := -fstack-protector-strong -fPIE -pie -D_FORTIFY_SOURCE=2 -Wl,-z,relro,-z,now -Wformat-security

BUILD = build
TARGET = $(BUILD)/zywin
SRC := src/*.cpp

all: $(TARGET)

$(TARGET): 
	mkdir -p $(BUILD)
	$(COMPILER) $(CXXFLAGS) $(SECFLAG) $(SRC) -o $@

install: $(TARGET)
	install -Dm755 $(TARGET) zywin/usr/bin/zywin
	install -Dm755 $(TARGET) zywindow

clean:
	rm -rf $(BUILD)

.PHONY: all install clean