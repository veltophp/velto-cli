<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class ClearLog extends Command
{
    public function handle(): void
    {
        $logPath = BASE_PATH . '/storage/log';
        $this->clearFilesIn($logPath, 'log');
    }

    private function clearFilesIn(string $dir, string $label): void
    {
        if (!is_dir($dir)) {
            $this->warning("⚠️  Directory not found: {$dir}");
            return;
        }

        $deleted = 0;
        foreach (glob("{$dir}/*") as $file) {
            if (is_file($file)) {
                unlink($file);
                $deleted++;
            }
        }

        $this->info("🧹 Cleared {$deleted} {$label} file(s).");
    }
}
