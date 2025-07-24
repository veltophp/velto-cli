<?php

namespace Veltophp\VeltoCli\Commands;

class Help
{
    public function handle(array $args = [])
    {
        echo " __    __   ______     __         ______   ______" . PHP_EOL;
        echo "/\\ \\  / /  /\\  ___\\   /\\ \\       /\\__  _\\ /\\  __ \\" . PHP_EOL;
        echo "\\ \\ \\/ /   \\ \\  __\\   \\ \\ \\____  \\/_/\\ \\/ \\ \\ \\/\\ \\" . PHP_EOL;
        echo " \\ \\__/     \\ \\_____\\  \\ \\_____\\    \\ \\_\\  \\ \\_____\\ " . PHP_EOL;
        echo "  \\/_/       \\/_____/   \\/_____/     \\/_/   \\/_____/ " . PHP_EOL;
        echo "\n";
        echo "\033[31mVeltoPHP - The Lightweight PHP Framework\033[0m\n\n";
        echo "Visit \033[4;34mhttps://veltophp.com\033[0m for more updates about VeltoPHP\n\n";
        echo "Usage:\n";
        echo "  php velto <command>\n\n";

        echo "\033[36mAvailable Commands:\033[0m\n\n";

        echo "  \033[36mhelp\033[0m                                         Show this help message\n";
        echo "  \033[36m-v / -version\033[0m                                Show VeltoCLI version\n";
        echo "\n";

        echo "  \033[36mstart\033[0m                                        Start local development server (auto port + local IP shown)\n";
        echo "  \033[36mstart:watch\033[0m                                  Start dev server with auto view reload\n";
        echo "\n";

        echo "  \033[36mmake:module (Modul)\033[0m                          Create a new module with controllers, models, views, routes, and migration\n";
        echo "  \033[36mmake:controller -(Module) -(Controller)\033[0m      Create a new controller inside an existing module\n";
        echo "  \033[36mmake:model -(Module) -(Model) -m\033[0m             Create a new model inside a module with optional migration\n";
        echo "  \033[36mmake:user:admin \033[0m                             Create an admin user for role admin\n";

        echo "\n";

        echo "  \033[36mlist:modules\033[0m                                 List all registered modules\n";
        echo "  \033[36mremove:module (Modul)\033[0m                        Remove a module and its contents\n";
        echo "\n";

        echo "  \033[36mmigrate\033[0m                                      Run all pending database migrations\n";
        echo "  \033[36mmigrate:fresh\033[0m                                Drop all tables and re-run all migrations\n";
        echo "  \033[36mmigrate:status\033[0m                               Show migration status (migrated / pending)\n";
        echo "  \033[36mmigrate:rollback\033[0m                             Rollback the last run migration\n";
        echo "\n";

        echo "  \033[36mclear:log\033[0m                                    Clear log files (storage/log)\n";
        echo "  \033[36mclear:session\033[0m                                Clear session files (storage/sessions)\n";
        echo "  \033[36mclear:view\033[0m                                   Clear compiled views (resources/cache/views)\n";
        echo "  \033[36mclear:all\033[0m                                    Clear log, session, and view cache files\n";
        echo "\n";

    }
}
