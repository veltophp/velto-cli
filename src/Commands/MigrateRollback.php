<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;
use PDO;
use Veltophp\VeltoCli\Config\Helpers;

class MigrateRollback extends Command
{
    const MIGRATION_PATH = BASE_PATH . '/axion/database/migrations';

    public function handle(): void
    {
        $db = Helpers::getPdoConnection(BASE_PATH);

        if (!$db) {
            $this->warning("⚠️  No database connection. Cannot perform rollback.");
            return;
        }

        $db->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                name VARCHAR(255) PRIMARY KEY
            )
        ");

        $lastMigration = $db->query("SELECT name FROM migrations ORDER BY rowid DESC LIMIT 1")
                            ->fetchColumn();

        if (!$lastMigration) {
            $this->info("ℹ️  No migrations to rollback.");
            return;
        }

        $filePath = self::MIGRATION_PATH . "/{$lastMigration}.php";

        if (!file_exists($filePath)) {
            $this->error("❌ Migration file {$lastMigration}.php not found.");
            return;
        }

        require_once $filePath;

        $className = $this->getMigrationClassName($filePath);

        if (!$className || !class_exists($className)) {
            $this->error("❌ Class {$className} not found in {$lastMigration}.php");
            return;
        }

        $migration = new $className();

        if (method_exists($migration, 'down')) {
            $migration->down();

            $stmt = $db->prepare("DELETE FROM migrations WHERE name = ?");
            $stmt->execute([$lastMigration]);

            $this->info("↩️ Rolled back: {$lastMigration}");
        } else {
            $this->warning("⚠️ Method 'down' not found in class {$className}");
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
