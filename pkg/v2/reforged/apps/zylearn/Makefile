CXX = g++
CXXFLAGS = -Wall -std=c++17 -O2 -Wextra -Wconversion -Wformat 
SECFLAG := -fstack-protector-strong -fPIE -pie -D_FORTIFY_SOURCE=2 -Wl,-z,relro,-z,now -Wformat-security

compile:
	sudo $(CXX) $(CXXFLAG) $(SECFLAG) main.cpp -o /usr/bin/zylearn
	$(CXX) $(CXXFLAG) $(SECFLAG) main.cpp -o ./zylearn/usr/bin/zylearn

clear:
	sudo rm -rf /usr/bin/zylearn
	rm -rf /usr/bin/zylearn