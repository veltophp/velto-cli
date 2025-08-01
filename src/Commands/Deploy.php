<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;

class Deploy extends Command
{
    public function handle(array $args = []): void
    {
        // Temukan root project (tempat composer.json berada)
        $basePath = $this->findProjectRoot();

        $targetDirName = $args['--link-to'] ?? 'public_html'; // default: public_html
        $targetSymlink = $basePath . '/../' . $targetDirName;
        $sourcePublic  = $basePath . '/public';

        $envSource = $basePath . '/.env.example';
        $envTarget = $basePath . '/.env';

        // ===== 1. SYMLINK =====
        if (!is_dir($sourcePublic)) {
            $this->warning("❌ Public folder not found: $sourcePublic");
        } elseif (is_link($targetSymlink)) {
            $this->info("🔁 Symlink already exists: $targetSymlink");
        } elseif (file_exists($targetSymlink)) {
            $this->warning("⚠️  $targetDirName already exists as a real folder. Please remove or rename it first.");
        } elseif (symlink($sourcePublic, $targetSymlink)) {
            $this->info("✅ Symlink created successfully:");
            $this->info("   $targetSymlink → $sourcePublic");
        } else {
            $this->warning("❌ Failed to create symlink. Check permissions or path.");
        }

        // ===== 2. COPY .env (optional) =====
        if (!file_exists($envSource)) {
            $this->warning("❌ .env.example not found.");
            return;
        }

        if (file_exists($envTarget)) {
            $this->info("📄 .env already exists. Skipping.");
            return;
        }

        echo "❓ Copy .env.example to .env? (y/n): ";
        $answer = strtolower(trim(fgets(STDIN)));

        if ($answer === 'y') {
            if (copy($envSource, $envTarget)) {
                $this->info("✅ .env copied from .env.example");
            } else {
                $this->warning("❌ Failed to copy .env.example");
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
