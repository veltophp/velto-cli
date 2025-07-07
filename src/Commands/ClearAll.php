<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class ClearAll extends Command
{
    public function handle(): void
    {
        $this->clear("storage/log", "log");
        $this->clear("storage/sessions", "session");
        $this->clear("resources/cache/views", "view cache");
    }

    private function clear(string $relativePath, string $label): void
    {
        $dir = BASE_PATH . '/' . $relativePath;

        if (!is_dir($dir)) {
            $this->warning("⚠️  Directory not found: {$relativePath}");
            return;
        }

        $count = 0;
        foreach (glob("{$dir}/*") as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            }
        }

        $this->info("🧹 Cleared {$count} {$label} file(s) from {$relativePath}");
    }
}
