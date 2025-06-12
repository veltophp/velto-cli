<?php

namespace Veltophp\VeltoCli\Commands;

use Velto\Axion\Commands\Publisher;
use Veltophp\VeltoCli\Command;

class AxionPublish extends Command
{
    public function handle(): void
    {
        Publisher::publish();
    }

    public static function basePath(string $path = ''): string
    {
        return defined('BASE_PATH') ? BASE_PATH . '/' . ltrim($path, '/') : getcwd() . '/' . ltrim($path, '/');
    }
}
