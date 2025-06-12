<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;
use Veltophp\VeltoCli\Config\Helpers;
use PDO;
use PDOException;

class ShowAdminUser extends Command
{
    public function handle(): void
    {
        $pdo = Helpers::getPdoConnection(BASE_PATH);

        if (!$pdo) {
            $this->error("❌ Database connection not established.");
            return;
        }

        try {
            $stmt = $pdo->prepare("SELECT user_id, name, email, role, created_at FROM users WHERE role = 'admin'");
            $stmt->execute();
            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->error("❌ Query failed: " . $e->getMessage());
            return;
        }

        if (empty($admins)) {
            $this->warning("⚠️  No admin users found.");
            return;
        }

        $this->info("📋 List of Admin Users:\n");

        // Header
        $header = sprintf(
            "%-12s | %-15s | %-25s | %-8s | %-19s",
            'User ID', 'Name', 'Email', 'Role', 'Registered at'
        );
        $this->line($header);
        $this->line(str_repeat('-', strlen($header)));

        // Data rows
        foreach ($admins as $admin) {
            $row = sprintf(
                "%-12s | %-15s | %-25s | %-8s | %-19s",
                $admin['user_id'],
                $admin['name'],
                $admin['email'],
                $admin['role'],
                $admin['created_at']
            );
            $this->line($row);
        }

        $this->success("\n✅ Admin list displayed successfully.");
    }
}
