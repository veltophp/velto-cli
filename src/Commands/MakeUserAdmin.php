<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;
use Veltophp\VeltoCli\Config\Helpers;
use PDO;
use PDOException;

class MakeUserAdmin extends Command
{
    public function handle(): void
    {
        $pdo = Helpers::getPdoConnection(BASE_PATH);

        if (!$pdo) {
            $this->error("❌ No database connection available. Command aborted.");
            return;
        }

        $email = $this->ask("\033[36m Input Email:\033[0m");
        $password = $this->askHidden("\033[36m Input Password:\033[0m");
        $passwordConfirm = $this->askHidden("\033[36m Confirm Password:\033[0m");

        if ($password !== $passwordConfirm) {
            $this->error("❌ Passwords do not match.");
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error("❌ Invalid email format.");
            return;
        }

        if (strlen($password) < 6) {
            $this->error("❌ Password must be at least 6 characters.");
            return;
        }

        $pdo = Helpers::getPdoConnection(BASE_PATH);

        if (!$pdo) {
            $this->error("❌ Could not connect to the database.");
            return;
        }

        try {
            $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $check->execute([$email]);
            if ($check->fetchColumn() > 0) {
                $this->error("❌ User with this email already exists.");
                return;
            }
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $now = date('Y-m-d H:i:s');

            // Hitung jumlah admin yang sudah ada
            $countSql = "SELECT COUNT(*) FROM users WHERE role = 'admin'";
            $stmt = $pdo->query($countSql);
            $adminCount = (int) $stmt->fetchColumn(); // misalnya 0 untuk pertama kali

            $nextNumber = str_pad($adminCount + 1, 2, '0', STR_PAD_LEFT); // 01, 02, 03, ...
            $name = 'VeltoPHP';
            $username = 'veltoadmin' . $nextNumber;

            $sql = "INSERT INTO users (name, username, email, password, role, email_verified, created_at, updated_at) 
                    VALUES (:name, :username, :email, :password, :role, :email_verified, :created_at, :updated_at)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashedPassword,
                ':role' => 'admin',
                ':email_verified' => 1,
                ':created_at' => $now,
                ':updated_at' => $now,
            ]);


            $this->success("✅ User Admin created successfully!");
        } catch (PDOException $e) {
            $this->error("❌ Failed to create admin: " . $e->getMessage());
        }
    }
}
