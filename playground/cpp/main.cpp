#include <iostream>
#include <string>
#include <cstdlib>
#include "helpers/markjason.h"

using namespace std;

int main()
{
    string name;
    int num1;
    int num2;
    int result;

    cout << "What is your name?: ";
    getline(cin, name);

    cout << "Please enter a number: ";
    cin >> num1;

    cout << "Please enter a second number: ";
    cin >> num2;

    result = subtract(num1, num2);

    cout << "Hello " << name << "!" << endl;

    if(result < 0) {
        cout << "The result is less than 0." << result << endl;
    } else {
        cout << "The result is fine. " << result << endl;
    }

    system("ls -l");

    return 0;
}