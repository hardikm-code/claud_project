<?php
// ============================================================
// m_tour.php - Tour Model
// ============================================================

require_once __DIR__ . '/db.php';

class TourModel {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    public function getAll(int $limit = 100, int $offset = 0): array {
        $stmt = $this->db->prepare('SELECT t.*, d.name AS destination_name, d.country FROM tours t LEFT JOIN destinations d ON t.destination_id = d.id ORDER BY t.featured DESC, t.created_at DESC LIMIT ? OFFSET ?');
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function getFeatured(int $limit = 6): array {
        $stmt = $this->db->prepare('SELECT t.*, d.name AS destination_name, d.country FROM tours t LEFT JOIN destinations d ON t.destination_id = d.id WHERE t.featured = 1 AND t.status = "active" ORDER BY t.created_at DESC LIMIT ?');
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT t.*, d.name AS destination_name, d.country, d.slug AS destination_slug FROM tours t LEFT JOIN destinations d ON t.destination_id = d.id WHERE t.id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getBySlug(string $slug): ?array {
        $stmt = $this->db->prepare('SELECT t.*, d.name AS destination_name, d.country, d.slug AS destination_slug FROM tours t LEFT JOIN destinations d ON t.destination_id = d.id WHERE t.slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function getByDestination(int $destId, int $excludeId = 0): array {
        $stmt = $this->db->prepare('SELECT t.*, d.name AS destination_name FROM tours t LEFT JOIN destinations d ON t.destination_id = d.id WHERE t.destination_id = ? AND t.id != ? AND t.status = "active" LIMIT 4');
        $stmt->execute([$destId, $excludeId]);
        return $stmt->fetchAll();
    }

    public function count(): int {
        return (int) $this->db->query('SELECT COUNT(*) FROM tours')->fetchColumn();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare('INSERT INTO tours (destination_id, name, slug, description, short_desc, image, duration_days, price, max_persons, tour_type, includes, excludes, featured, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([
            $data['destination_id'],
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['short_desc'] ?? null,
            $data['image'] ?? null,
            $data['duration_days'] ?? 1,
            $data['price'],
            $data['max_persons'] ?? 10,
            $data['tour_type'] ?? 'Adventure',
            $data['includes'] ?? null,
            $data['excludes'] ?? null,
            $data['featured'] ?? 0,
            $data['status'] ?? 'active',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare('UPDATE tours SET destination_id=?, name=?, slug=?, description=?, short_desc=?, image=?, duration_days=?, price=?, max_persons=?, tour_type=?, includes=?, excludes=?, featured=?, status=? WHERE id=?');
        return $stmt->execute([
            $data['destination_id'],
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['short_desc'] ?? null,
            $data['image'] ?? null,
            $data['duration_days'] ?? 1,
            $data['price'],
            $data['max_persons'] ?? 10,
            $data['tour_type'] ?? 'Adventure',
            $data['includes'] ?? null,
            $data['excludes'] ?? null,
            $data['featured'] ?? 0,
            $data['status'] ?? 'active',
            $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM tours WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function search(string $q, string $type = '', float $minPrice = 0, float $maxPrice = 99999): array {
        $sql = 'SELECT t.*, d.name AS destination_name, d.country FROM tours t LEFT JOIN destinations d ON t.destination_id = d.id WHERE t.status = "active" AND (t.name LIKE ? OR d.name LIKE ? OR d.country LIKE ?)';
        $params = ['%'.$q.'%', '%'.$q.'%', '%'.$q.'%'];
        if ($type) {
            $sql .= ' AND t.tour_type = ?';
            $params[] = $type;
        }
        $sql .= ' AND t.price BETWEEN ? AND ?';
        $params[] = $minPrice;
        $params[] = $maxPrice;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getTourTypes(): array {
        $stmt = $this->db->query('SELECT DISTINCT tour_type FROM tours ORDER BY tour_type');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
