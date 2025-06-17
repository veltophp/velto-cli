<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class ClearLogSeeder extends Command
{
    protected string $basePath;

    public function __construct()
    {
        $this->basePath = defined('BASE_PATH') ? BASE_PATH : getcwd();
    }

    public function handle(): void
    {
        $logFile = $this->basePath . '/axion/database/seeders/log/seeder-log.json';

        if (!file_exists($logFile)) {
            $this->error("❌ Seeder log file not found: $logFile");
            return;
        }

        if (!is_writable($logFile)) {
            $this->error("❌ Seeder log file is not writable: $logFile");
            return;
        }

        if (unlink($logFile)) {
            $this->success("✅ Seeder log file cleared: $logFile");
        } else {
            $this->error("❌ Failed to clear seeder log file: $logFile");
        }
    }
}
