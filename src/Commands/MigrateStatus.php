<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;
use PDO;
use Veltophp\VeltoCli\Config\Helpers;

class MigrateStatus extends Command
{
    const MIGRATION_PATH = BASE_PATH . '/storage/database/migrations';

    public function handle(): void
    {
        $db = Helpers::getPdoConnection(BASE_PATH);

        if (!$db) {
            $this->warning("⚠️  No database connection. Cannot check status.\n");
            return;
        }

        $db->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                name VARCHAR(255) PRIMARY KEY
            )
        ");

        $migrated = $db->query("SELECT name FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
        $files = glob(self::MIGRATION_PATH . '/*.php');

        echo "📋 Migration Status:\n\n";
        echo str_pad("Status", 12) . " | Migration File\n";
        echo str_repeat("-", 40) . "\n";

        foreach ($files as $file) {
            $filename = basename($file, '.php');
            $status = in_array($filename, $migrated) ? "\033[32m✔ Migrated\033[0m" : "\033[33m⏳ Pending\033[0m";
            echo str_pad($status, 12) . " | {$filename}\n";
        }

        echo "\n";
    }
}
