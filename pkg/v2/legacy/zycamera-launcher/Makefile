COMPILER = g++
CXXFLAGS = -Wall -std=c++17 -O2 -Wextra -Wconversion -Wformat 
SECFLAG := -fstack-protector-strong -fPIE -pie -D_FORTIFY_SOURCE=2 -Wl,-z,relro,-z,now -Wformat-security -fstack-protector-all


compile:
	$(COMPILER) $(CXXFLAGS) $(SECFLAG) main.cpp -o zycamera-launcher/usr/bin/zycamera-launcher
	$(COMPILER) $(CXXFLAGS) $(SECFLAG) main.cpp -o init

clean:
	sudo rm -rf zycamera-launcher/usr/bin/zycamera-launcher
	sudo rm -rf init
