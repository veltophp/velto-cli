<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class Deploy extends Command
{
    public function handle(): void
    {
        // Dapatkan root project (misalnya tempat composer.json berada)
        $basePath = $this->findProjectRoot();

        $target     = $basePath . '/public';
        $link       = $basePath . '/public_html';
        $envSource  = $basePath . '/.env.example';
        $envTarget  = $basePath . '/.env';

        // ===== 1. SYMLINK =====
        if (!is_dir($target)) {
            $this->warning("❌ Target directory not found: $target");
        } elseif (is_link($link)) {
            $this->info("🔁 Symlink already exists: $link");
        } elseif (file_exists($link)) {
            $this->warning("⚠️  public_html already exists as a real folder. Please remove or rename it first.");
        } elseif (symlink($target, $link)) {
            $this->info("✅ Symlink created successfully:");
            $this->info("   $link → $target");
        } else {
            $this->warning("❌ Failed to create symlink. Check permissions or path.");
        }

        // ===== 2. ASK TO COPY .env =====
        if (!file_exists($envSource)) {
            $this->warning("❌ .env.example file not found at root.");
            return;
        }

        if (file_exists($envTarget)) {
            $this->info("📄 .env file already exists. Skipping copy.");
            return;
        }

        echo "❓ Do you want to copy .env.example to .env? (y/n): ";
        $answer = strtolower(trim(fgets(STDIN)));

        if ($answer === 'y') {
            if (copy($envSource, $envTarget)) {
                $this->info("✅ .env file created from .env.example");
            } else {
                $this->warning("❌ Failed to copy .env.example to .env");
            }
        } else {
            $this->info("ℹ️  Skipped .env copy.");
        }
    }

    /**
     * Menemukan root project berdasarkan keberadaan composer.json
     */
    protected function findProjectRoot(): string
    {
        $dir = getcwd();

        while ($dir !== '/' && !file_exists($dir . '/composer.json')) {
            $dir = dirname($dir);
        }

        return $dir;
    }
}