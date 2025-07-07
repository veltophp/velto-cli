<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class RemoveModule extends Command
{
    public function handle(array $arguments = []): void
    {
        $moduleName = $arguments[0] ?? null;

        if (!$moduleName) {
            $this->error("❌ Please provide the module name to remove.");
            return;
        }

        $moduleSlug = strtolower($moduleName);
        $modulePath = (defined('BASE_PATH') ? BASE_PATH : getcwd()) . "/modules/{$moduleSlug}";

        if (!is_dir($modulePath)) {
            $this->error("❌ Module '{$moduleName}' does not exist.");
            return;
        }

        $this->deleteDirectory($modulePath);
        $this->info("🗑️  Module '{$moduleName}' has been removed.");
    }

    private function deleteDirectory(string $dir): void
    {
        $items = array_diff(scandir($dir), ['.', '..']);

        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($dir);
    }
}
