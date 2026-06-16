#include <stdio.h>
#include <stdlib.h>
#include "helpers/helperInput.h"
#include "helpers/helperString.h"

int main()
{
    int VM_SIZE;
    char command[256];
    char VM_NAME[100];

    printf("Enter Virtual Machine Size (GB): ");
    VM_SIZE = helperInt();

    printf("Enter Virtual Machine Name: ");
    helperString(VM_NAME, 100);

    snprintf(
        command,
        sizeof(command),
        "qemu-img create -f qcow2 images/%s.qcow2 %dG",
        VM_NAME,
        VM_SIZE
    );

    printf("Executing: %s\n", command);

    system(command);

    return 0;
}