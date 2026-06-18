#include <stdio.h>
#include <string.h>
#include <time.h>
#include "../helpers/headers/helperCommand.h"

void logCommand(int id, int argc, char *argv[])
{
    FILE *file = fopen("./zylearn_history.txt", "a");
    if (!file) {
        printf("zylearn: failed to write history\n");
        return;
    }

    fprintf(file, "commandId: %d\n", id);

    fprintf(file, "command: ");
    for (int i = 0; i < argc; i++) {
        fprintf(file, "%s ", argv[i]);
    }

    fprintf(file, "\n---\n");

    fclose(file);
}

int main(int argc, char *argv[])
{
    char _GITPATH[100] = "/bin/git";
    const char *_HISTORYPATH = "./zylearn_history.txt";

    int commandId = 1;

    logCommand(commandId++, argc, argv);

    if (argc < 3) {
        printf("zylearn: missing arguments\n");
        printf("usage: zylearn setup <template>\n");
        return 1;
    }

    // SETUP

    if (strcmp(argv[1], "setup") == 0) {

        // SKELETON

        if (strcmp(argv[2], "skeleton") == 0) {

            helperCommand(
                _GITPATH,
                "clone",
                "https://github.com/zyphor-os/zyphor-os-minimal",
                NULL
            );

        } else {
            printf("zylearn: unknown '%s'\n", argv[2]);
            return 1;
        }

    } 

    // SHOW

    else if (strcmp(argv[1], "show") == 0) {

        // LOGS

        if (strcmp(argv[2], "logs") == 0) {

            FILE *file = fopen(_HISTORYPATH, "r");

            if (!file) {
                printf("zylearn: no logs found\n");
                return 1;
            }

            char line[1024];

            while (fgets(line, sizeof(line), file)) {
                printf("%s", line);
            }

            fclose(file);

        }

        else {
            printf("zylearn: unknown '%s'\n", argv[2]);
            return 1;
        }

    }

    // CLEAR

    else if (strcmp(argv[1], "clear") == 0) {

        // LOGS

        if (strcmp(argv[2], "logs") == 0) {

            printf("zylearn history cleared.\n");

            helperCommand(
                "/bin/rm",
                "-rf",
                _HISTORYPATH,
                NULL
            );
        }

        else {
            printf("zylearn: unknown '%s'\n", argv[2]);
            return 1;
        }

    }
    
    // COMMAND ERROR

    else {
        printf("zylearn: unknown command '%s'\n", argv[1]);
        return 1;
    }

    return 0;
}