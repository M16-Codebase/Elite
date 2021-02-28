#include <stdio.h>
#include <stdlib.h>
#include <sys/types.h>
#include <unistd.h>
#include <string.h>

int main(int argc, char *argv[])
{
    if (argc == 1){
        return 1;
    }
    unsigned long s_len = strlen("indexer ");
    for(int count = 1; count < argc; count++){
        s_len += strlen(argv[count]) + 1;
    }
    char *cmd = malloc(s_len);
    strcpy(cmd, "indexer ");
    for(int count = 1; count < argc; count++){
        strcat(cmd, argv[count]);
        strcat(cmd, " ");
    }
    setuid(0);
    system(cmd);
    free(cmd);
   return 0;
}
