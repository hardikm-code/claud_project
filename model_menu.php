<?php
class MenuModel {

    // ── Categories ────────────────────────────────────────────

    public static function getAllCategories(): array {
        $pdo  = getDB();
        $stmt = $pdo->query("SELECT * FROM menu_categories ORDER BY display_order, id");
        return $stmt->fetchAll();
    }

    public static function getCategoryById(int $id): ?array {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT * FROM menu_categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    // ── Menu Items ────────────────────────────────────────────

    public static function getAllItems(): array {
        $pdo  = getDB();
        $stmt = $pdo->query(
            "SELECT mi.*, mc.name AS category_name
             FROM menu_items mi
             JOIN menu_categories mc ON mi.category_id = mc.id
             ORDER BY mc.display_order, mi.name"
        );
        return $stmt->fetchAll();
    }

    public static function getAvailableItems(): array {
        $pdo  = getDB();
        $stmt = $pdo->query(
            "SELECT mi.*, mc.name AS category_name
             FROM menu_items mi
             JOIN menu_categories mc ON mi.category_id = mc.id
             WHERE mi.is_available = 1
             ORDER BY mc.display_order, mi.name"
        );
        return $stmt->fetchAll();
    }

    public static function getFeaturedItems(int $limit = 6): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "SELECT mi.*, mc.name AS category_name
             FROM menu_items mi
             JOIN menu_categories mc ON mi.category_id = mc.id
             WHERE mi.is_featured = 1 AND mi.is_available = 1
             ORDER BY mi.id DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public static function getItemsByCategory(int $categoryId): array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "SELECT mi.*, mc.name AS category_name
             FROM menu_items mi
             JOIN menu_categories mc ON mi.category_id = mc.id
             WHERE mi.category_id = ? AND mi.is_available = 1
             ORDER BY mi.name"
        );
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    public static function getItemById(int $id): ?array {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "SELECT mi.*, mc.name AS category_name
             FROM menu_items mi
             JOIN menu_categories mc ON mi.category_id = mc.id
             WHERE mi.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function countItems(): int {
        return (int) getDB()->query("SELECT COUNT(*) FROM menu_items")->fetchColumn();
    }

    // ── CRUD for Admin ────────────────────────────────────────

    public static function createItem(array $data): bool {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "INSERT INTO menu_items (category_id, name, description, price, image_url, is_featured, is_available)
             VALUES (:category_id, :name, :description, :price, :image_url, :is_featured, :is_available)"
        );
        return $stmt->execute([
            'category_id'  => $data['category_id'],
            'name'         => $data['name'],
            'description'  => $data['description'],
            'price'        => $data['price'],
            'image_url'    => $data['image_url'] ?? '',
            'is_featured'  => $data['is_featured'] ?? 0,
            'is_available' => $data['is_available'] ?? 1,
        ]);
    }

    public static function updateItem(int $id, array $data): bool {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "UPDATE menu_items
             SET category_id=:category_id, name=:name, description=:description,
                 price=:price, image_url=:image_url, is_featured=:is_featured, is_available=:is_available
             WHERE id=:id"
        );
        return $stmt->execute([
            'id'           => $id,
            'category_id'  => $data['category_id'],
            'name'         => $data['name'],
            'description'  => $data['description'],
            'price'        => $data['price'],
            'image_url'    => $data['image_url'] ?? '',
            'is_featured'  => $data['is_featured'] ?? 0,
            'is_available' => $data['is_available'] ?? 1,
        ]);
    }

    public static function deleteItem(int $id): bool {
        $pdo  = getDB();
        $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
