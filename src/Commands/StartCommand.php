<?php

namespace Veltophp\VeltoCli\Commands;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Veltophp\VeltoCli\Command;

class StartCommand extends Command
{
    protected array $argv;

    public function __construct()
    {
        $this->argv = $_SERVER['argv'] ?? [];
    }

    public function handle(): void
    {
        $useLocalIP = in_array('--local-ip', $this->argv);
        $watchMode = false;
        $watchAxion = false;

        $isWatchCommand = false;
        foreach ($this->argv as $arg) {
            if (str_starts_with($arg, 'start:watch')) {
                $isWatchCommand = true;
                break;
            }
        }
        $watchMode = $isWatchCommand;

        $watchAxion = in_array('--axion', $this->argv);

        $host = $useLocalIP ? $this->getLocalIP() : 'localhost';
        $port = $useLocalIP ? 8080 : 8000;

        while ($this->isPortUsed($host, $port)) {
            $port++;
        }

        $projectRoot = getcwd();
        $publicPath = realpath($projectRoot . '/public');
        $viewPath = $watchAxion
            ? realpath($projectRoot . '/axion/views')
            : realpath($projectRoot . '/views');
        $reloadPath = $publicPath . '/.reload';

        $url = "http://$host:$port";

        $this->info("🔧 Starting Velto development server at $url");
        $this->info("📂 Serving from: $publicPath");

        if ($watchMode) {
            $this->info("👀 Watching view folder: $viewPath\n");
        }

        if ($useLocalIP) {
            $this->info("🌐 Access from other devices at:\n👉 $url");
            $this->showQr($url);
        }

        if ($watchMode) {
            $pid = pcntl_fork();

            if ($pid === -1) {
                $this->error("❌ Fork failed!");
                return;
            } elseif ($pid > 0) {
                $this->monitorViews($viewPath, $reloadPath);
            } else {
                exec("php -S $host:$port -t \"$publicPath\"");
            }
        } else {
            exec("php -S $host:$port -t \"$publicPath\"");
        }
    }

    private function getLocalIP(): string
    {
        $output = [];
        exec("ipconfig getifaddr en0", $output);
        if (!empty($output[0])) {
            return trim($output[0]);
        }
        exec("hostname -I", $output);
        return trim(explode(' ', $output[0] ?? '127.0.0.1')[0]);
    }

    private function isPortUsed(string $host, int $port): bool
    {
        $sock = @fsockopen($host, $port, $errno, $errstr, 1);
        if ($sock) {
            fclose($sock);
            return true;
        }
        return false;
    }

    private function showQr(string $text): void
    {
        if (shell_exec('which qrencode')) {
            echo "\n📸 Scan QR code to open:\n\n";
            system("qrencode -t ANSI256 -l L -v 1 -s 1 '$text'");
            echo "\n\n";
        } else {
            echo "\n⚠️  QR code display not supported (qrencode not found).\n";
            echo "MacOS:  brew install qrencode\n";
            echo "Linux : sudo apt install qrencode\n";
            echo "🔗 Open this manually: $text\n";
        }
    }

    private function monitorViews(string $viewPath, string $reloadFile): void
    {
        $lastModified = [];

        while (true) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($viewPath)
            );

            $changed = false;

            foreach ($files as $file) {
                if ($file->isFile()) {
                    $path = $file->getRealPath();
                    $mod = filemtime($path);

                    if (!isset($lastModified[$path]) || $mod > $lastModified[$path]) {
                        $lastModified[$path] = $mod;
                        echo "🔄 File changed: $path\n";
                        $changed = true;
                    }
                }
            }

            if ($changed) {
                file_put_contents($reloadFile, time());
            }

            sleep(1);
        }
    }
}