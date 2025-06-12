<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class ClearLog extends Command
{
    protected string $basePath;

    public function __construct()
    {
        $this->basePath = defined('BASE_PATH') ? BASE_PATH : getcwd();
    }

    public function handle(): void
    {
        $logFile = $this->basePath . '/storage/log/velto.log';

        if (!file_exists($logFile)) {
            $this->error("Log file not found: $logFile");
            return;
        }

        if (!is_writable($logFile)) {
            $this->error("Log file is not writable: $logFile");
            return;
        }

        if (unlink($logFile)) {
            $this->info("✅ Log file cleared: $logFile");
        } else {
            $this->error("Failed to clear log file: $logFile");
        }
    }
}
