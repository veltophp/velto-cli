<?php
namespace Veltophp\VeltoCli\Config;

class Helpers
{
    protected static array $env = [];

    public static function loadEnv(string $basePath): void
    {
        $envFile = $basePath . '/.env';
        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;

            if (preg_match('/^\s*([A-Z0-9_]+)\s*=\s*(.*)?\s*$/i', $line, $matches)) {
                $key = $matches[1];
                $value = $matches[2] ?? '';
                $value = trim($value);
                if (strlen($value) > 1 && ($value[0] === '"' || $value[0] === "'")) {
                    $value = substr($value, 1, -1);
                }
                self::$env[$key] = $value;
            }
        }
    }

    public static function env(string $key, $default = null)
    {
        return self::$env[$key] ?? getenv($key) ?: $default;
    }

    public static function getPdoConnection(string $basePath): ?\PDO
    {
        self::loadEnv($basePath);
    
        $driver = self::env('DB_CONNECTION');
    
        if (!$driver) {
            echo "\n";
            echo "ℹ️  No database driver set. Skipping DB connection...\n";
            echo "\n";
            return null;
        }
    
        if ($driver === 'sqlite') {
            $path = self::env('DB_DATABASE');
    
            if (!$path) {
                echo "⚠️  No SQLite database path specified.\n";
                return null;
            }
    
            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
    
            if (!file_exists($path)) {
                touch($path);
            }
    
            return new \PDO('sqlite:' . $path);
        }
    
        if ($driver === 'mysql') {
            $host = self::env('DB_HOST');
            $port = self::env('DB_PORT');
            $db = self::env('DB_DATABASE');
            $user = self::env('DB_USERNAME');
            $pass = self::env('DB_PASSWORD');
            $charset = self::env('DB_CHARSET', 'utf8mb4');
    
            try {
                $pdo = new \PDO("mysql:host=$host;port=$port;charset=$charset", $user, $pass);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    
                $stmt = $pdo->query("SHOW DATABASES LIKE " . $pdo->quote($db));
                if ($stmt->rowCount() === 0) {
                    $pdo->exec("CREATE DATABASE `$db` CHARACTER SET $charset COLLATE {$charset}_unicode_ci");
                }
    
                $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
                $pdo = new \PDO($dsn, $user, $pass);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    
                return $pdo;
            } catch (\PDOException $e) {
                echo "❌ Failed to connect to MySQL: " . $e->getMessage() . "\n";
                exit(1);
            }
        }
    
        echo "\n";
        echo "❌ Please enable a database driver in your .env file.\n";
        echo "\n";
        exit(1);
    }
    
    public static function uvid(int $length = 32): string
    {
        if (!in_array($length, [6, 8, 12, 32])) {
            throw new \InvalidArgumentException("uvid() only supports lengths: 6, 8, 12, or 32.");
        }

        $bytes = random_bytes($length / 2);
        $hex = bin2hex($bytes);

        $interval = ($length === 6 || $length === 8) ? 2 : 4;
        $parts = str_split($hex, $interval);

        return implode('-', $parts);
    }

}
