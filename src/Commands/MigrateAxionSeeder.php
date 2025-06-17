<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;
use Velto\Core\Env;

class MigrateAxionSeeder extends Command
{
    protected string $basePath;
    protected string $seederPath;

    public function __construct()
    {
        $this->basePath = defined('BASE_PATH') ? BASE_PATH : getcwd();
        $this->seederPath = $this->basePath . '/axion/database/seeders/';
    }

    public function handle(array $arguments = []): void
    {
        // Load env
        Env::load($this->basePath . '/.env');

        $dbname = Env::get('DB_DATABASE');
        if (!str_starts_with($dbname, '/')) {
            $dbname = $this->basePath . '/' . ltrim($dbname, '/');
        }

        // Validasi database
        if (!file_exists($dbname)) {
            $this->error("❌ Database file does not exist at: $dbname");
            return;
        }

        if (!is_readable($dbname)) {
            $this->error("❌ Database file is not readable.");
            return;
        }

        if (!is_writable($dbname)) {
            $this->warning("⚠️ Database file is not writable.");
        }

        // Validasi folder seeder
        if (!is_dir($this->seederPath)) {
            $this->warning("⚠️ Seeder folder not found: {$this->seederPath}");
            return;
        }

        $seederFiles = glob($this->seederPath . '*.php');
        if (empty($seederFiles)) {
            $this->warning("⚠️ No seeders found to run.");
            return;
        }

        // Siapkan log
        $logDir = $this->seederPath . 'log/';
        $logFile = $logDir . 'seeder-log.json';
        $ranSeeders = [];

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        if (!file_exists($logFile)) {
            file_put_contents($logFile, json_encode([]));
        }

        $ranSeeders = json_decode(file_get_contents($logFile), true);
        if (!is_array($ranSeeders)) {
            $ranSeeders = [];
        }

        // Jalankan semua seeder
        foreach ($seederFiles as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);
            require_once $file;

            if (!class_exists($className)) {
                $this->error("❌ Class $className not found in $file.");
                continue;
            }

            if (in_array($className, $ranSeeders)) {
                $this->warning("⚠️ Seeder $className already executed. Skipping...");
                continue;
            }

            $seeder = new $className();

            if (!method_exists($seeder, 'run')) {
                $this->error("⚠️ Method 'run' not found in $className.");
                continue;
            }

            try {
                $seeder->run();
                $ranSeeders[] = $className;
                file_put_contents($logFile, json_encode($ranSeeders, JSON_PRETTY_PRINT));
            } catch (\Throwable $e) {
                $this->error("❌ Error running $className: " . $e->getMessage());
            }

            $this->success("🎉 $className seeding completed successfully.");
        }

    }
    
}
