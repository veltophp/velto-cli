<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class Up extends Command
{
    public function handle(): void
    {
        $file = BASE_PATH . '/.maintenance';

        if (!file_exists($file)) {
            $this->info("ℹ️  Application is not in maintenance mode.");
            return;
        }

        unlink($file);
        
        $this->info("✅ Application is now live.");
    }
}
