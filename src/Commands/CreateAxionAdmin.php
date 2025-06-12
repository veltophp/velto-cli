<?php

namespace Veltophp\VeltoCli\Commands;

use Veltophp\VeltoCli\Command;
use Veltophp\VeltoCli\Config\Helpers;
use PDO;

class CreateAxionAdmin extends Command
{
    public function handle(): void
    {
        $email = $this->ask("Input Email:");
        $password = $this->askHidden("Input Password:");
        $passwordConfirm = $this->askHidden("Confirm Password:");
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

        $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetchColumn() > 0) {
            $this->error("❌ User with this email already exists.");
            return;
        }

        $userId = Helpers::uvid(8);
        $name = explode('@', $email)[0]; // Default name from email
        $bio = "Hi, I'm an Axion admin.";
        $picture = null;
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $now = date('Y-m-d H:i:s');

        $sql = "INSERT INTO users (
            user_id, name, bio, picture, email, password, role, email_verified, created_at, updated_at
        ) VALUES (
            :user_id, :name, :bio, :picture, :email, :password, :role, :email_verified, :created_at, :updated_at
        )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':name' => $name,
            ':bio' => $bio,
            ':picture' => $picture,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => 'admin',
            ':email_verified' => 1,
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);

        $this->info("✅ Axion Admin created successfully!");
    }
}
