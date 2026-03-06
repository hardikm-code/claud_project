<?php
class ReservationModel {

    public static function create(array $data): bool {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "INSERT INTO reservations (name, email, phone, date, time, guests, special_requests)
             VALUES (:name, :email, :phone, :date, :time, :guests, :special_requests)"
        );
        return $stmt->execute([
            'name'             => $data['name'],
            'email'            => $data['email'],
            'phone'            => $data['phone'],
            'date'             => $data['date'],
            'time'             => $data['time'],
            'guests'           => $data['guests'],
            'special_requests' => $data['special_requests'] ?? '',
        ]);
    }

    public static function getAll(?string $status = null): array {
        $pdo = getDB();
        if ($status && in_array($status, ['pending','confirmed','cancelled'])) {
            $stmt = $pdo->prepare("SELECT * FROM reservations WHERE status = ? ORDER BY date DESC, time DESC");
            $stmt->execute([$status]);
        } else {
            $stmt = $pdo->query("SELECT * FROM reservations ORDER BY date DESC, time DESC");
        }
        return $stmt->fetchAll();
    }

    public static function getById(int $id): ?array {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function updateStatus(int $id, string $status): bool {
        if (!in_array($status, ['pending','confirmed','cancelled'])) return false;
        $pdo  = getDB();
        $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public static function delete(int $id): bool {
        $pdo  = getDB();
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function countByStatus(string $status): int {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE status = ?");
        $stmt->execute([$status]);
        return (int) $stmt->fetchColumn();
    }

    public static function countTotal(): int {
        return (int) getDB()->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
    }

    public static function getTodayReservations(): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE date = CURDATE() ORDER BY time ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getRecentReservations(int $limit = 10): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT * FROM reservations ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
