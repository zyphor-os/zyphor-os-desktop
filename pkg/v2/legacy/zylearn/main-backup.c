#include <stdio.h>
#include <string.h>
#include <pwd.h>
#include <unistd.h>
#include "../helpers/headers/helperCommand.h"

void logCommand(int id, int argc, char *argv[])
{
    FILE *file = fopen("/tmp/zylearn_history.txt", "a");
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
    char _GITBINPATH[100] = "/bin/git";
    char _REMOVEBINPATH[100] = "/bin/rm";
    char _GITHUBREPOLINK[100] = "https://github.com/zyphor-os/zyphor-os-minimal";

    const char *_HISTORYPATH = "/tmp/zylearn_history.txt";

    int commandId = 1;

    logCommand(commandId++, argc, argv);

    if (argc < 2) {
        printf("zylearn: missing arguments\n");
        printf("usage: zylearn setup <template>\n");
        return 1;
    }

    if (argc >= 2 && strcmp(argv[1], "--version") == 0) {
        printf("zylearn version v1.0.0-r1-beta-release\n");
        return 0;
    }

    if (argc >= 2 && strcmp(argv[1], "--help") == 0) {

        printf("zylearn v1.0.0-r1-beta-release\n\n");

        printf("Usage:\n");
        printf("  zylearn <command> [options]\n\n");

        printf("Commands:\n");
        printf("  setup skeleton      Install the Zyphor OS learning skeleton environment.\n");
        printf("  show logs           Display learning activity logs.\n");
        printf("  clear logs          Remove all learning activity logs.\n\n");

        printf("Options:\n");
        printf("  --help              Show this help message.\n");
        printf("  --version           Show version information.\n\n");

        printf("Examples:\n");
        printf("  zylearn setup skeleton\n");
        printf("  zylearn show logs\n");
        printf("  zylearn clear logs\n\n");

        printf("Learn Zyphor OS from the ground up.\n");

        return 0;
    }

    // SETUP

    if (strcmp(argv[1], "setup") == 0) {

        // SKELETON

        if (strcmp(argv[2], "skeleton") == 0) {
            printf(
                "\nInitializing the installation of the Zyphor Minimal\n"
                "Learning skeleton operating system environment.\n"
                "Please wait while the required components are being configured...\n\n"
            );
            helperCommand(_GITBINPATH, "clone", _GITHUBREPOLINK, NULL);

        } else {
            printf("zylearn: unknown '%s'. Type zylearn --help for more information.\n", argv[2]);
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
            printf("zylearn: unknown '%s'. Type zylearn --help for more information.\n", argv[2]);
            return 1;
        }

    }

    // CLEAR

    else if (strcmp(argv[1], "clear") == 0) {

        // LOGS

        if (strcmp(argv[2], "logs") == 0) {

            printf("zylearn history cleared.\n");

            helperCommand(_REMOVEBINPATH, "-rf", _HISTORYPATH, NULL);
        }

        else {
            printf("zylearn: unknown '%s'. Type zylearn --help for more information.\n", argv[2]);
            return 1;
        }

    }

    // TEST

    else if (strcmp(argv[1], "test") == 0) {

        if (strcmp(argv[2], "username") == 0) {
            struct passwd *pw = getpwuid(getuid());

            if (pw) {
                printf("%s\n", pw->pw_name);
            } else {
                printf("\nError\n");
            }
        }

        else {
            printf("zylearn: unknown '%s'. Type zylearn --help for more information.\n", argv[2]);
            return 1;
        }

    }
    
    // COMMAND ERROR

    else {
        printf("zylearn: unknown command '%s'. Type zylearn --help for more information.\n", argv[1]);
        return 1;
    }

    return 0;
}

// usage ===

// zylearn --version
// zylearn setup skeleton
// zylearn show logs
// zylearn clear logs