#include <iostream>
#include <string>
#include "helpers/markjason.h"

using namespace std;

int main()
{
    string name;
    cout << "What is your name?: ";

    getline(cin, name);

    cout << "Hello World" << name << " " << subtract(2, 3) << endl;
    return 0;
}