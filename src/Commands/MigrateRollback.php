<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;
use PDO;
use Veltophp\VeltoCli\Config\Helpers;

class MigrateRollback extends Command
{
    const MIGRATION_PATH = BASE_PATH . '/storage/database/migrations';

    public function handle(): void
    {
        $db = Helpers::getPdoConnection(BASE_PATH);

        if (!$db) {
            $this->warning("⚠️  No database connection. Cannot rollback.\n");
            return;
        }

        $db->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                name VARCHAR(255) PRIMARY KEY
            )
        ");

        $lastMigration = $db->query("SELECT name FROM migrations ORDER BY name DESC LIMIT 1")->fetchColumn();

        if (!$lastMigration) {
            $this->info("✅ No migration to rollback.");
            return;
        }

        $file = self::MIGRATION_PATH . "/{$lastMigration}.php";

        if (!file_exists($file)) {
            $this->error("❌ Migration file not found: {$lastMigration}.php");
            return;
        }

        $className = $this->getMigrationClassName($file);

        require_once $file;

        if (!class_exists($className)) {
            $this->error("❌ Migration class {$className} not found.");
            return;
        }

        $migration = new $className();

        if (method_exists($migration, 'down')) {
            $migration->down();
            $db->prepare("DELETE FROM migrations WHERE name = ?")->execute([$lastMigration]);
            $this->info("↩️  Rolled back: {$lastMigration}");
        } else {
            $this->warning("⚠️ Method 'down' not found in {$className}");
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
