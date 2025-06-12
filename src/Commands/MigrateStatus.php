<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;
use Veltophp\VeltoCli\Config\Helpers;
use PDO;

class MigrateStatus extends Command
{
    const MIGRATION_PATH = BASE_PATH . '/axion/database/migrations';

    public function handle(): void
    {
        $db = Helpers::getPdoConnection(BASE_PATH);

        // Pastikan tabel migrations ada
        $db->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                name VARCHAR(255) PRIMARY KEY
            )
        ");

        // Ambil data yang sudah dimigrasikan
        $migrated = $db->query("SELECT name FROM migrations")->fetchAll(PDO::FETCH_COLUMN);

        // Ambil semua file migrasi
        $files = glob(self::MIGRATION_PATH . '/*.php');
        $totalFiles = count($files);
        $migratedCount = 0;

        echo "\n";
        echo "Migration Status:\n";
        echo "\n";

        foreach ($files as $file) {
            $filename = basename($file, '.php');
            $isMigrated = in_array($filename, $migrated);

            echo "\033[36m => {$filename}\033[0m\n";

            if ($isMigrated) {
                $migratedCount++;
            }
        }

        echo "\nMigrated: {$migratedCount}\n";
    }
}
