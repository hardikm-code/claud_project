<?php
// ============================================================
// m_destination.php - Destination Model
// ============================================================

require_once __DIR__ . '/db.php';

class DestinationModel {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    public function getAll(int $limit = 100, int $offset = 0): array {
        $stmt = $this->db->prepare('SELECT * FROM destinations ORDER BY featured DESC, created_at DESC LIMIT ? OFFSET ?');
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function getFeatured(int $limit = 6): array {
        $stmt = $this->db->prepare('SELECT * FROM destinations WHERE featured = 1 ORDER BY created_at DESC LIMIT ?');
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM destinations WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getBySlug(string $slug): ?array {
        $stmt = $this->db->prepare('SELECT * FROM destinations WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function count(): int {
        return (int) $this->db->query('SELECT COUNT(*) FROM destinations')->fetchColumn();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare('INSERT INTO destinations (name, slug, country, description, short_desc, image, price_from, rating, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['country'],
            $data['description'],
            $data['short_desc'] ?? null,
            $data['image'] ?? null,
            $data['price_from'] ?? 0,
            $data['rating'] ?? 4.5,
            $data['featured'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare('UPDATE destinations SET name=?, slug=?, country=?, description=?, short_desc=?, image=?, price_from=?, rating=?, featured=? WHERE id=?');
        return $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['country'],
            $data['description'],
            $data['short_desc'] ?? null,
            $data['image'] ?? null,
            $data['price_from'] ?? 0,
            $data['rating'] ?? 4.5,
            $data['featured'] ?? 0,
            $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM destinations WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function search(string $q): array {
        $q = '%' . $q . '%';
        $stmt = $this->db->prepare('SELECT * FROM destinations WHERE name LIKE ? OR country LIKE ? OR description LIKE ?');
        $stmt->execute([$q, $q, $q]);
        return $stmt->fetchAll();
    }
}
