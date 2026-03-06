<?php
// ============================================================
// m_contact.php - Contact Message Model
// ============================================================

require_once __DIR__ . '/db.php';

class ContactModel {
    private PDO $db;

    public function __construct() {
        $this->db = db();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['subject'],
            $data['message'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function getAll(int $limit = 100, int $offset = 0): array {
        $stmt = $this->db->prepare('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT ? OFFSET ?');
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM contact_messages WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function count(): int {
        return (int) $this->db->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn();
    }

    public function countUnread(): int {
        return (int) $this->db->query('SELECT COUNT(*) FROM contact_messages WHERE status = "unread"')->fetchColumn();
    }

    public function markRead(int $id): bool {
        $stmt = $this->db->prepare('UPDATE contact_messages SET status = "read" WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function markReplied(int $id): bool {
        $stmt = $this->db->prepare('UPDATE contact_messages SET status = "replied" WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM contact_messages WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
