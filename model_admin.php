<?php
class AdminModel {

    public static function findByUsername(string $username): ?array {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    public static function verifyLogin(string $username, string $password): ?array {
        $user = self::findByUsername($username);
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return null;
    }

    public static function getById(int $id): ?array {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM admin_users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function updatePassword(int $id, string $newPassword): bool {
        $pdo  = getDB();
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }
}
