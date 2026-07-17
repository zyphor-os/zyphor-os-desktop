#include <iostream>
#include <string>
#include <cstdlib>
#include "helpers/markjason.h"
#include <vector>

using namespace std;

class User {
    public:
        string name;
        string email;
        int age;
        string address;
};

int main()
{
    vector<User> users;

    users.push_back({"Mark Jason Espelita", "markjasonespelita@gmail.com", 26, "Salvacion"});
    users.push_back({"Eljohn Gordo Razos", "nonoy@gmail.com", 22, "Salvacion"});

    for(const User& user : users) {
        cout << user.name << " " << user.email << " " << user.age << " " << user.address << endl;
    }

    return 0;
}