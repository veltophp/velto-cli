<?php

namespace Veltophp\VeltoCli\Commands;

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
        $hostOptions = ['localhost', '127.0.0.1'];
        $localIP = $this->getLocalIP();
        if ($localIP) {
            $hostOptions[] = $localIP;
        }

        $port = 8000;
        while ($this->isAnyPortUsed($hostOptions, $port)) {
            $port++;
        }

        $projectRoot = getcwd();
        $publicPath = realpath($projectRoot . '/public');

        $this->info("Starting Velto development server...\n");
        $this->printTable($hostOptions, $port);

        if ($localIP) {
            $this->printSmallQr("http://$localIP:$port");
        }

        echo "\n🧭 Press Ctrl+C to stop the server\n\n";

        exec("php -S 0.0.0.0:$port -t \"$publicPath\"");
    }

    private function isAnyPortUsed(array $hosts, int $port): bool
    {
        foreach ($hosts as $host) {
            if ($this->isPortUsed($host, $port)) {
                return true;
            }
        }
        return false;
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

    private function getLocalIP(): ?string
    {
        $output = [];
        exec("ipconfig getifaddr en0 2>/dev/null", $output); // Mac
        if (!empty($output[0])) return trim($output[0]);

        exec("hostname -I 2>/dev/null", $output); // Linux
        return trim(explode(' ', $output[0] ?? '')[0]) ?: null;
    }

    private function printTable(array $hosts, int $port): void
    {
        $this->line("🌐 Access URLs:");
        $this->line("┌───────────────┬──────────────────────────────┐");
        $this->line("│   Interface   │           Address            │");
        $this->line("├───────────────┼──────────────────────────────┤");

        foreach ($hosts as $host) {
            $label = match ($host) {
                'localhost'     => 'Localhost',
                '127.0.0.1'     => 'Loopback',
                default         => 'Local Network',
            };

            $this->line(sprintf("│ %-13s │ http://%-21s │", $label, "$host:$port"));
        }

        $this->line("└───────────────┴──────────────────────────────┘\n");
    }

    private function printSmallQr(string $url): void
    {
        if (shell_exec('which qrencode')) {
            echo "\n📸 QR code (LAN access):\n\n";
            system("qrencode -t ANSI256 -l L -v 1 -s 1 '$url'");
        } else {
            echo "\n⚠️  QR code not available (qrencode not installed)\n";
            echo "🔗 Access manually: $url\n";
        }
    }
}
