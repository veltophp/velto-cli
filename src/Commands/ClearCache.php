<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class ClearCache extends Command
{
    protected string $basePath;

    public function __construct()
    {
        $this->basePath = defined('BASE_PATH') ? BASE_PATH : getcwd();
    }

    public function handle(): void
    {
        $cachePath = $this->basePath . '/storage/cache/views';

        if (!is_dir($cachePath)) {
            $this->error("Cache path not found: $cachePath");
            return;
        }

        $files = glob($cachePath . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $this->info("✅ Cache cleared in: $cachePath");
    }
}
