#include <iostream>
#include <fstream>
#include <string>
#include <filesystem>
#include <unistd.h>
#include <pwd.h>

using namespace std;

namespace fs = std::filesystem;


const string GIT_BIN_PATH = "/bin/git";
const string REMOVE_BIN_PATH = "/bin/rm";
const string GITHUB_REPO_LINK = "https://github.com/zyphor-os/zyphor-os-minimal";

const string HISTORY_PATH = "/tmp/zylearn_history.txt";


void logCommand(int id, int argc, char* argv[])
{
    ofstream file(HISTORY_PATH, ios::app);

    if (!file.is_open()) {
        cout << "zylearn: failed to write history\n";
        return;
    }

    file << "commandId: " << id << "\n";

    file << "command: ";

    for (int i = 0; i < argc; i++) {
        file << argv[i] << " ";
    }

    file << "\n---\n";

    file.close();
}


void executeCommand(const string& command)
{
    system(command.c_str());
}


void showHelp()
{
    cout
        << "zylearn v2.0.0-r2-beta-release\n\n"

        << "Usage:\n"
        << "  zylearn <command> [options]\n\n"

        << "Commands:\n"
        << "  setup skeleton      Install the Zyphor OS learning skeleton environment.\n"
        << "  show logs           Display learning activity logs.\n"
        << "  clear logs          Remove all learning activity logs.\n\n"

        << "Options:\n"
        << "  --help              Show this help message.\n"
        << "  --version           Show version information.\n\n"

        << "Examples:\n"
        << "  zylearn setup skeleton\n"
        << "  zylearn show logs\n"
        << "  zylearn clear logs\n\n"

        << "Learn Zyphor OS from the ground up.\n";
}


int main(int argc, char* argv[])
{
    int commandId = 1;

    logCommand(commandId++, argc, argv);


    if (argc < 2) {

        cout << "zylearn: missing arguments\n";
        cout << "usage: zylearn setup <template>\n";

        return 1;
    }


    string command = argv[1];


    if (command == "--version") {

        cout << "zylearn version v1.0.0-r1-beta-release\n";

        return 0;
    }


    if (command == "--help") {

        showHelp();

        return 0;
    }



    // SETUP

    if (command == "setup") {

        if (argc < 3) {
            cout << "zylearn: missing setup option\n";
            return 1;
        }


        string option = argv[2];


        if (option == "skeleton") {

            cout
                << "\nInitializing the installation of the Zyphor Minimal\n"
                << "Learning skeleton operating system environment.\n"
                << "Please wait while required components are configured...\n\n";


            executeCommand(
                GIT_BIN_PATH +
                " clone " +
                GITHUB_REPO_LINK
            );


        } else {

            cout 
                << "zylearn: unknown '" 
                << option 
                << "'\n";

            return 1;
        }

    }



    // SHOW

    else if (command == "show") {

        if (argc < 3) {
            cout << "zylearn: missing show option\n";
            return 1;
        }


        if (string(argv[2]) == "logs") {


            ifstream file(HISTORY_PATH);


            if (!file.is_open()) {

                cout << "zylearn: no logs found\n";

                return 1;
            }


            string line;

            while(getline(file, line)) {

                cout << line << "\n";

            }


            file.close();

        }

    }



    // CLEAR

    else if (command == "clear") {


        if (argc < 3) {

            cout << "zylearn: missing clear option\n";

            return 1;
        }


        if (string(argv[2]) == "logs") {


            cout << "zylearn history cleared.\n";


            if (fs::exists(HISTORY_PATH)) {

                fs::remove(HISTORY_PATH);

            }

        }

    }



    // TEST

    else if (command == "test") {


        if (argc < 3) {

            cout << "zylearn: missing test option\n";

            return 1;
        }



        if (string(argv[2]) == "username") {


            passwd* pw = getpwuid(getuid());


            if (pw) {

                cout << pw->pw_name << "\n";

            } else {

                cout << "Error\n";

            }

        }

    }



    else {

        cout 
            << "zylearn: unknown command '" 
            << command 
            << "'. Type zylearn --help for more information.\n";

        return 1;
    }


    return 0;
}