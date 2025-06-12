<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class Down extends Command
{
    public function handle(): void
    {
        $file = BASE_PATH . '/.maintenance';

        if (file_exists($file)) {
            $this->info("⚠️  Maintenance mode is already active.");
            return;
        }

        file_put_contents($file, "Maintenance mode enabled at " . date('Y-m-d H:i:s'));
        
        $this->info("🛠️  Application is now in maintenance mode.");
    }
}
