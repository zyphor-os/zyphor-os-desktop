#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "helpers/headers/helperString.h"
#include "helpers/headers/helperInput.h"

#define MAX_PEOPLE 100
#define DATA_FILE "people.txt"

typedef struct
{
    char name[100];
    int age;
} Person;

Person people[MAX_PEOPLE];
int peopleCount = 0;

/* ---------- Persistence ---------- */

void loadPeople()
{
    FILE *file = fopen(DATA_FILE, "r");
    if (file == NULL)
    {
        peopleCount = 0;
        return;
    }

    peopleCount = 0;
    while (peopleCount < MAX_PEOPLE &&
           fscanf(file, "%99[^|]|%d\n", people[peopleCount].name, &people[peopleCount].age) == 2)
    {
        peopleCount++;
    }

    fclose(file);
}

void savePeople()
{
    FILE *file = fopen(DATA_FILE, "w");
    if (file == NULL)
    {
        printf("Failed to open %s for writing.\n", DATA_FILE);
        return;
    }

    for (int i = 0; i < peopleCount; i++)
    {
        fprintf(file, "%s|%d\n", people[i].name, people[i].age);
    }

    fclose(file);
}

/* ---------- CRUD operations ---------- */

void createPerson()
{
    if (peopleCount >= MAX_PEOPLE)
    {
        printf("\nStorage full, cannot add more people.\n");
        return;
    }

    Person p;
    printf("\nEnter name: ");
    helperString(p.name, 100);
    printf("Enter age: ");
    p.age = helperInt();

    people[peopleCount] = p;
    peopleCount++;
    savePeople();

    printf("\nSaved %s (age %d).\n", p.name, p.age);
}

void readPeople()
{
    if (peopleCount == 0)
    {
        printf("\nNo records yet.\n");
        return;
    }

    printf("\n%-4s %-20s %-5s\n", "No.", "Name", "Age");
    printf("---------------------------\n");
    for (int i = 0; i < peopleCount; i++)
    {
        printf("%-4d %-20s %-5d\n", i + 1, people[i].name, people[i].age);
    }
}

int promptForIndex()
{
    readPeople();
    if (peopleCount == 0)
    {
        return -1;
    }

    printf("\nEnter record number: ");
    int choice = helperInt();

    if (choice < 1 || choice > peopleCount)
    {
        printf("\nInvalid record number.\n");
        return -1;
    }

    return choice - 1; /* convert to 0-based index */
}

void updatePerson()
{
    int index = promptForIndex();
    if (index == -1)
    {
        return;
    }

    printf("\nUpdating %s (age %d)\n", people[index].name, people[index].age);
    printf("Enter new name: ");
    helperString(people[index].name, 100);
    printf("Enter new age: ");
    people[index].age = helperInt();

    savePeople();
    printf("\nRecord updated.\n");
}

void deletePerson()
{
    int index = promptForIndex();
    if (index == -1)
    {
        return;
    }

    printf("\nDelete %s (age %d)? [y/N]: ", people[index].name, people[index].age);
    char confirm;
    scanf(" %c", &confirm);

    if (confirm != 'y' && confirm != 'Y')
    {
        printf("\nCancelled.\n");
        return;
    }

    for (int i = index; i < peopleCount - 1; i++)
    {
        people[i] = people[i + 1];
    }
    peopleCount--;

    savePeople();
    printf("\nRecord deleted.\n");
}

/* ---------- Menu ---------- */

void printMenu()
{
    printf("\n===== Person CRUD =====\n");
    printf("1. Create\n");
    printf("2. Read (list all)\n");
    printf("3. Update\n");
    printf("4. Delete\n");
    printf("5. Exit\n");
    printf("Choose an option: ");
}

int main()
{
    loadPeople();

    while (1)
    {
        printMenu();
        int choice = helperInt();

        switch (choice)
        {
        case 1:
            createPerson();
            break;
        case 2:
            readPeople();
            break;
        case 3:
            updatePerson();
            break;
        case 4:
            deletePerson();
            break;
        case 5:
            printf("\nGoodbye!\n");
            return 0;
        default:
            printf("\nInvalid option, please try again.\n");
        }
    }

    return 0;
}