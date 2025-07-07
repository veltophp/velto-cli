<?php

namespace Veltophp\VeltoCli\Commands;

class Version
{

    public function handle(array $args = []) {
        echo "\n";
        echo "Velto-CLI Version 1.5 | VeltoPHP \n";
        echo "\n";
        echo "Use : `php velto help` to show all commands | visit veltophp.com for all information.\n";
        echo "\n";
        return;

    }

}