<?php

namespace Veltophp\VeltoCli;

use Veltophp\VeltoCli\Commands;

class Velto
{
    protected $commands = [];

    public function __construct()
    {
        $this->commands = [
            'clear:cache' => new Commands\ClearCache(),
            'clear:session' => new Commands\ClearSession(),
            'clear:log' => new Commands\ClearLog(),
            'help' => new Commands\Help(),
            'migrate' => new Commands\Migrate(),
            'migrate:fresh' => new Commands\MigrateFresh(),
            'migrate:rollback' => new Commands\MigrateRollback(),
            'migrate:status' => new Commands\MigrateStatus(),
            'make:controller' => new Commands\MakeController(),
            'make:axion:controller' => new Commands\MakeAxionController(),
            'make:axion:model' => new Commands\MakeAxionModel(),
            'create:axion:admin' => new Commands\CreateAxionAdmin(),
            'show:axion:admin' => new Commands\ShowAdminUser(),
            'start' => new Commands\StartCommand(),
            'start:watch' => new Commands\StartCommand(),
            'publish:axion' => new Commands\AxionPublish(),
            '-version' => new Commands\Version(),
            '-v' => new Commands\Version(),
            'down' => new Commands\Down(),
            'up' => new Commands\Up(),


        ];
    }

    public function run()
    {
        global $argv;

        if (count($argv) < 2) {
            echo "\n";
            echo " Velto-CLI Version 1.0 | VeltoPHP \n";
            echo "\n";
            echo " ==>  No command given <==\n";
            echo "\n";
            echo " Use : `php velto help` to show all commands | visit veltophp.com for all information.\n";
            echo "\n";
            return;
        }

        $commandName = $argv[1];
        $args = array_slice($argv, 2);

        if (isset($this->commands[$commandName])) {
            $this->commands[$commandName]->handle($args);
        } else {
            echo "\n";
            echo " Velto-CLI Version 1.0 | VeltoPHP \n";
            echo "\n";
            echo " ==>  Command Not Found! <==\n";
            echo "\n";
            echo " Use : `php velto help` to show all commands | visit veltophp.com for all information.\n";
            echo "\n";
            return;
        }
    }
}
