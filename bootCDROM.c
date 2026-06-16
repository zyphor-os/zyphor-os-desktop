#include <stdio.h>
#include <stdlib.h>
#include "helpers/helperInput.h"
#include "helpers/helperString.h"

int main()
{
    char command[256];
    char VM_NAME[100];
    int RAM_SIZE;

    printf("Enter Virtual Machine Name: ");
    helperString(VM_NAME, 100);

    printf("RAM Size: ");
    RAM_SIZE = helperInt();

    snprintf(
        command,
        sizeof(command),
        "qemu-system-x86_64 --enable-kvm --cdrom ./images/*.iso -m %d --hda images/%s.qcow2 --boot d",
        RAM_SIZE,
        VM_NAME
    );

    printf("Executing: %s\n", command);

    system(command);

    return 0;
}