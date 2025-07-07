<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;
use PDO;
use Veltophp\VeltoCli\Config\Helpers;

class Migrate extends Command
{
    const MIGRATION_PATH = BASE_PATH . '/storage/database/migrations';

    public function handle(): void
    {
        $db = Helpers::getPdoConnection(BASE_PATH);

        if (!$db) {
            $this->warning("⚠️  No database connection. Skipping migration.\n");
            return;
        }

        $db->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                name VARCHAR(255) PRIMARY KEY
            )
        ");

        $migrated = $db->query("SELECT name FROM migrations")->fetchAll(PDO::FETCH_COLUMN);

        $files = glob(self::MIGRATION_PATH . '/*.php');

        $newMigrations = 0;

        foreach ($files as $file) {
            $filename = basename($file, '.php');

            if (in_array($filename, $migrated)) {
                $this->info("⏩ Already migrated: {$filename}");
                continue;
            }

            $className = $this->getMigrationClassName($file);

            if (!$className) {
                $this->error("❌ Could not determine class name from {$filename}.php");
                continue;
            }

            require_once $file;

            if (!class_exists($className)) {
                $this->error("❌ Class {$className} not found in {$filename}.php");
                continue;
            }

            $migration = new $className();

            if (method_exists($migration, 'up')) {
                $migration->up();
                $db->prepare("INSERT INTO migrations (name) VALUES (?)")->execute([$filename]);
                $this->info("✅ Migrated: {$filename}");
                $newMigrations++;
            } else {
                $this->warning("⚠️ Method 'up' not found in class {$className}");
            }
        }

        if ($newMigrations === 0) {
            $this->info("✅ Nothing to migrate.");
        }
    }

    private function getMigrationClassName(string $file): ?string
    {
        $content = file_get_contents($file);
        if (preg_match('/class\s+([a-zA-Z0-9_]+)/', $content, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
