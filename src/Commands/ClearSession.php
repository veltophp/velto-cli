<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class ClearSession extends Command
{
    protected string $basePath;

    public function __construct()
    {
        $this->basePath = defined('BASE_PATH') ? BASE_PATH : getcwd();
    }

    public function handle(): void
    {
        $sessionPath = $this->basePath . '/storage/sessions';

        if (!is_dir($sessionPath)) {
            $this->error("Session directory not found: $sessionPath");
            return;
        }

        $files = glob($sessionPath . '/*');

        if (empty($files)) {
            $this->info("No session files to clear in: $sessionPath");
            return;
        }

        $deletedCount = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                if (unlink($file)) {
                    $deletedCount++;
                } else {
                    $this->error("Failed to delete session file: $file");
                }
            }
        }

        $this->info("✅ Cleared {$deletedCount} session file(s) in: $sessionPath");
    }
}
