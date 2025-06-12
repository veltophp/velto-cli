<?php

namespace Veltophp\VeltoCli\Commands;

class Help
{
    public function handle(array $args = []){
        
        echo " __    __   ______     __         ______   ______" . PHP_EOL;
        echo "/\\ \\  / /  /\\  ___\\   /\\ \\       /\\__  _\\ /\\  __ \\" . PHP_EOL;
        echo "\\ \\ \\/ /   \\ \\  __\\   \\ \\ \\____  \\/_/\\ \\/ \\ \\ \\/\\ \\" . PHP_EOL;
        echo " \\ \\__/     \\ \\_____\\  \\ \\_____\\    \\ \\_\\  \\ \\_____\\" . PHP_EOL;
        echo "  \\/_/       \\/_____/   \\/_____/     \\/_/   \\/_____/" . PHP_EOL;
        echo "  \n";
        echo "  \n";
        echo "\033[31mVeltoPHP - The Lightweight PHP Framework\033[0m\n";
        echo "  \n";
        echo "Visit \033[4;34mhttps://veltophp.com\033[0m for more updates about VeltoPHP\n";
        echo "  \n";
        echo "Usage:\n";
        echo "  php velto <command>\n\n";
            
        echo "\033[36mAvailable Commands:\033[0m\n\n";

        echo "  \033[36mhelp\033[0m                                                 Show this help message\n";
        echo "  \033[36m-v / -version\033[0m                                        Show version of this Velto-CLI\n";
        
        echo "  \033[36mstart\033[0m                                                Start development server (localhost:8000)\n";
        echo "  \033[36mstart --local-ip\033[0m                                     Start development server with local IP access (port 8080+, auto port increment)\n";
        echo "  \033[36mstart:watch\033[0m                                          Start development server with auto reload watching views folder (localhost:8000)\n";
        echo "  \033[36mstart:watch --axion\033[0m                                  Start development server with auto reload watching views axion/views (localhost:8000)\n";
        echo "  \033[36mstart:watch --local-ip\033[0m                               Start development server with local IP and auto reload (port 8080+, watch views)\n";
        echo "  \033[36mstart:watch --local-ip --axion\033[0m                       Start development server with local IP, auto reload, and watch axion/views folder\n";
        
        echo "  \033[36mmake:controller <NameController>\033[0m                     Create an Velto controller with the given name\n";
        echo "  \033[36mmake:axion:controller <NameController>\033[0m               Create an Axion controller with the given name\n";
        echo "  \033[36mmake:axion:model <Name> -m\033[0m                           Create an Axion model and migration file with the given name\n";

        echo "  \033[36mcreate:axion:admin \033[0m                                  Create an Axion Admin user for Admin access\n";
        echo "  \033[36mshow:axion:admin \033[0m                                    Show an Axion Admin user for Admin access\n";


        
        echo "  \033[36mpublish:axion\033[0m                                        Publish Axion starter files into your project\n";
        
        echo "  \033[36mclear:cache\033[0m                                          Clear cached views\n";
        echo "  \033[36mclear:session\033[0m                                        Clear session data\n";
        echo "  \033[36mclear:log\033[0m                                            Clear log files\n";

        echo "  \033[36mmigrate\033[0m                                              Run database migrations\n";
        echo "  \033[36mmigrate:fresh\033[0m                                        Drop all tables and re-run all migrations\n";
        echo "  \033[36mmigrate:rollback\033[0m                                     Rollback the last database migration batch\n";
        echo "  \033[36mmigrate:status\033[0m                                       Show the status of each migration\n";
        
        echo "  \033[36mdown\033[0m                                                 Put the application into maintenance mode\n";
        echo "  \033[36mup\033[0m                                                   Bring the application out of maintenance mode\n";
        
        echo "\n";

    }
}
