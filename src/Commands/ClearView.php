<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class ClearView extends Command
{
    public function handle(): void
    {
        $viewCachePath = BASE_PATH . '/resources/cache/views';
        $this->clearFilesIn($viewCachePath, 'view cache');
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
