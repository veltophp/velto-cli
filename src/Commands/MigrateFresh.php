<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;
use Veltophp\VeltoCli\Config\Helpers;
use PDO;
use Exception;

class MigrateFresh extends Command
{
    protected string $basePath;
    protected string $migrationPath;
    protected ?PDO $db;


    public function __construct()
    {
        $this->basePath = defined('BASE_PATH') ? BASE_PATH : getcwd();
        $this->migrationPath = $this->basePath . '/storage/database/migrations';
        $this->db = Helpers::getPdoConnection($this->basePath);

        if (!$this->db) {
            echo "⚠️  No database connection available. Migration skipped.\n";
        }
    }

    public function handle(): void
    {
        if (!$this->db) {
            echo "ℹ️  Skipping migration: no database driver set or Axion not published.\n";
            return;
        }

        $this->ensureMigrationTable();
        $this->dropAllTables();
        $this->clearSessions();
        $migrated = $this->runMigrations();
        $this->recordMigrations($migrated);

        echo "✅ All migrations have been reset and applied successfully.\n";
    }

    protected function ensureMigrationTable(): void
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS migrations (name TEXT PRIMARY KEY)");
    }

    protected function dropAllTables(): void
    {
        $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'sqlite') {
            $tables = $this->db->query(
                "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'"
            )->fetchAll(PDO::FETCH_COLUMN);
        } elseif ($driver === 'mysql') {
            $tables = $this->db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        } else {
            throw new Exception("Unsupported database driver: $driver");
        }

        foreach ($tables as $table) {
            if ($table === 'migrations') continue;
            $this->db->exec("DROP TABLE IF EXISTS `$table`");
            echo "🗑️ Dropped table: {$table}\n";
        }
    }

    protected function clearSessions(): void
    {
        $sessionPath = $this->basePath . '/storage/sessions';

        if (!is_dir($sessionPath)) {
            echo "ℹ️ Session path not found: {$sessionPath}\n";
            return;
        }

        $files = glob($sessionPath . '/*');
        $deleted = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $deleted++;
            }
        }

        echo "🧹 Cleared {$deleted} session file(s) from storage/sessions\n";
    }
    protected function runMigrations(): array
    {
        $migrated = [];
        $files = glob($this->migrationPath . '/*.php');

        foreach ($files as $file) {
            require_once $file;

            $content = file_get_contents($file);
            if (!preg_match('/class\s+(\w+)/', $content, $matches)) {
                echo "❌ No class found in " . basename($file) . "\n";
                continue;
            }

            $className = $matches[1];

            if (!class_exists($className)) {
                echo "❌ Class {$className} not found after requiring file.\n";
                continue;
            }

            $migration = new $className();

            if (!method_exists($migration, 'up')) {
                echo "⚠️ Method 'up' not found in {$className}\n";
                continue;
            }

            try {
                $migration->up();
                echo "✅ Migrated: {$className}\n";
                $migrated[] = $className;
            } catch (Exception $e) {
                echo "❌ Failed migrating {$className}: " . $e->getMessage() . "\n";
            }
        }

        return $migrated;
    }
    protected function recordMigrations(array $migrated): void
    {
        $this->db->exec("DELETE FROM migrations");
        $stmt = $this->db->prepare("INSERT INTO migrations (name) VALUES (?)");

        foreach ($migrated as $className) {
            $stmt->execute([$className]);
        }
    }
}
