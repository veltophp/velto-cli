<?php

namespace Veltophp\VeltoCli;

use Veltophp\VeltoCli\Commands;

class Velto
{
    protected $commands = [];

    public function __construct()
    {
        $this->commands = [
            'help' => new Commands\Help(),
            'clear:log' => new Commands\ClearLog(),
            'clear:session' => new Commands\ClearSession(),
            'clear:view' => new Commands\ClearView(),
            'clear:all' => new Commands\ClearAll(),
            'migrate' => new Commands\Migrate(),
            'migrate:fresh' => new Commands\MigrateFresh(),
            'migrate:rollback' => new Commands\MigrateRollback(),
            'migrate:status' => new Commands\MigrateStatus(),
            'make:module' => new Commands\MakeModule(),
            'make:controller' => new Commands\MakeController(),
            'make:model' => new Commands\MakeModel(),
            'make:user:admin' => new Commands\MakeUserAdmin(),
            'list:modules' => new Commands\ListModules(),
            'remove:module' => new Commands\RemoveModule(),
            'start' => new Commands\StartCommand(),
            '-version' => new Commands\Version(),
            '-v' => new Commands\Version(),
        ];
    }

    public function run()
    {
        global $argv;

        if (count($argv) < 2) {
            echo "\n";
            echo " Velto-CLI Version 2.0 | VeltoPHP \n";
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
            echo " Velto-CLI Version 2.0 | VeltoPHP \n";
            echo "\n";
            echo " ==>  Command Not Found! <==\n";
            echo "\n";
            echo " Use : `php velto help` to show all commands | visit veltophp.com for all information.\n";
            echo "\n";
            return;
        }
    }
}
