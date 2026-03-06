<?php
// ============================================================
// m_booking.php - Booking Model
// ============================================================

require_once __DIR__ . '/db.php';

class BookingModel {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    public function create(array $data): int {
        $ref = generateRef();
        $stmt = $this->db->prepare('INSERT INTO bookings (user_id, tour_id, booking_ref, travel_date, persons, total_price, status, payment_status, special_requests) VALUES (?,?,?,?,?,?,?,?,?)');
        $stmt->execute([
            $data['user_id'],
            $data['tour_id'],
            $ref,
            $data['travel_date'],
            $data['persons'],
            $data['total_price'],
            'pending',
            'unpaid',
            $data['special_requests'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT b.*, t.name AS tour_name, t.image AS tour_image, t.duration_days, d.name AS destination_name, u.name AS user_name, u.email AS user_email, u.phone AS user_phone FROM bookings b JOIN tours t ON b.tour_id = t.id JOIN destinations d ON t.destination_id = d.id JOIN users u ON b.user_id = u.id WHERE b.id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getByRef(string $ref): ?array {
        $stmt = $this->db->prepare('SELECT b.*, t.name AS tour_name, t.image AS tour_image, t.duration_days, d.name AS destination_name FROM bookings b JOIN tours t ON b.tour_id = t.id JOIN destinations d ON t.destination_id = d.id WHERE b.booking_ref = ? LIMIT 1');
        $stmt->execute([$ref]);
        return $stmt->fetch() ?: null;
    }

    public function getByUser(int $userId): array {
        $stmt = $this->db->prepare('SELECT b.*, t.name AS tour_name, t.image AS tour_image, t.duration_days, d.name AS destination_name FROM bookings b JOIN tours t ON b.tour_id = t.id JOIN destinations d ON t.destination_id = d.id WHERE b.user_id = ? ORDER BY b.created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAll(int $limit = 100, int $offset = 0): array {
        $stmt = $this->db->prepare('SELECT b.*, t.name AS tour_name, d.name AS destination_name, u.name AS user_name, u.email AS user_email FROM bookings b JOIN tours t ON b.tour_id = t.id JOIN destinations d ON t.destination_id = d.id JOIN users u ON b.user_id = u.id ORDER BY b.created_at DESC LIMIT ? OFFSET ?');
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function count(): int {
        return (int) $this->db->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
    }

    public function countPending(): int {
        return (int) $this->db->query('SELECT COUNT(*) FROM bookings WHERE status = "pending"')->fetchColumn();
    }

    public function totalRevenue(): float {
        return (float) $this->db->query('SELECT COALESCE(SUM(total_price),0) FROM bookings WHERE payment_status = "paid"')->fetchColumn();
    }

    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare('UPDATE bookings SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function updatePayment(int $id, string $paymentStatus): bool {
        $stmt = $this->db->prepare('UPDATE bookings SET payment_status = ? WHERE id = ?');
        return $stmt->execute([$paymentStatus, $id]);
    }

    public function cancel(int $id, int $userId): bool {
        $stmt = $this->db->prepare('UPDATE bookings SET status = "cancelled" WHERE id = ? AND user_id = ?');
        return $stmt->execute([$id, $userId]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM bookings WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
