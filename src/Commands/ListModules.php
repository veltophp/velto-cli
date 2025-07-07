<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class ListModules extends Command
{
    public function handle(array $arguments = []): void
    {
        $modulesPath = defined('BASE_PATH') ? BASE_PATH . '/modules' : getcwd() . '/modules';

        if (!is_dir($modulesPath)) {
            $this->info("📦 No modules directory found.");
            return;
        }

        $modules = array_filter(scandir($modulesPath), function ($item) use ($modulesPath) {
            return $item !== '.' && $item !== '..' && is_dir("{$modulesPath}/{$item}");
        });

        if (empty($modules)) {
            $this->info("📦 No modules installed.");
            return;
        }

        $this->info("📦 Installed Modules:");
        foreach ($modules as $module) {
            $this->line(" - " . ucfirst($module));
        }
    }
}
