#include <iostream>
#include <string>
#include <unistd.h>

using namespace std;

int main(int argc, char* argv[])
{
    // Check if a command was provided
    if (argc < 2) {
        cerr << "Error: No command specified." << endl;
        cerr << "Usage: " << argv[0] << " <command> [argument]" << endl;
        cerr << "Try '" << argv[0] << " help' for more information." << endl;
        return 1;
    }

    string firstArg = argv[1];

    // Help command
    if (firstArg == "help") {

        cout << "Usage: " << argv[0] << " <command> [argument]" << endl;
        cout << endl;
        cout << "Commands:" << endl;
        cout << "  help          Show this help message" << endl;
        cout << "  build app     Build an application" << endl;

        return 0;
    }

    // Build command
    else if (firstArg == "build") {

        // build requires a second argument
        if (argc < 3) {
            cerr << "Error: 'build' requires an argument." << endl;
            cerr << "Usage: " << argv[0] << " build app" << endl;
            return 2;
        }

        string secondArg = argv[2];

        if (secondArg == "app") {

	    string targetDirectory = "../../pkg/v2";
	    string appName;
	    bool isWebView;
     	    int isWebViewOrNotWebViewDecision;

	    cout << "Is this a web view applicatio? [1] Yes | [2] No: ";

	    cin >> isWebViewOrNotWebViewDecision;

	    if (isWebViewOrNotWebViewDecision == 1) {
	    	cout << "Webview Selected." << endl;
		execl(
			"/bin/cp",
			"cp",
			"-r",
			"templates/webview",
			targetDirectory.c_str(),
			(char*)nullptr
		);
		cout << "Done." << endl;
	    } else if (isWebViewOrNotWebViewDecision == 2) {
	    	cout << "Not Webview" << endl;
		
		execl(
			"/bin/cp",
			"cp",
			"-r",
			"templates/utility",
			targetDirectory.c_str(),
			(char*)nullptr
		);

		cout << "Done" << endl;

	    } else {
	    	cout << "Error";
		return 0;
	    }

            cout << "Build App here..." << endl;

            return 0;

        } else {

            cerr << "Error: Unknown build argument '" 
                 << secondArg << "'." << endl;

            cerr << "Available build arguments:" << endl;
            cerr << "  app" << endl;

            return 3;
        }
    }

    // Unknown command
    else {

        cerr << "Error: Unknown command '" 
             << firstArg << "'." << endl;

        cerr << "Try '" << argv[0] 
             << " help' for more information." << endl;

        return 4;
    }
}
