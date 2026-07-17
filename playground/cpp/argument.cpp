#include <iostream>
#include <string>
#include <cstdlib>
#include "helpers/markjason.h"

using namespace std;

int main(int argc, char* argv[])
{
    cout << "The number of arguments are: " << argc << endl;

    cout << "These are the arguments: " << endl;

    for(int x = 0; x <= argc; x++) {
        cout << argv[x] << endl;
    }

    return 0;
}