<?php
/**
 * La Bella Cucina - Database Setup Script
 * Visit this file once to create tables and seed data.
 * DELETE this file after setup is complete!
 */
define('DB_HOST', 'localhost');
define('DB_USER', 'marketingelsnerd_claud');
define('DB_PASS', 'K-QDs43-C~purMBe');
define('DB_NAME', 'marketingelsnerd_claud');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("<p style='color:red;font-family:sans-serif;padding:20px;'>Connection failed: " . $conn->connect_error . "</p>");
}
$conn->set_charset("utf8mb4");

$sql = "
CREATE TABLE IF NOT EXISTS `menu_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`category_id`) REFERENCES `menu_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `guests` int(11) NOT NULL,
  `special_requests` text,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$errors  = [];
$success = [];

// Execute table creation statements one by one
foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
    if (empty($stmt)) continue;
    if ($conn->query($stmt) === TRUE) {
        $success[] = "OK: " . substr($stmt, 0, 60) . "...";
    } else {
        $errors[] = "Error: " . $conn->error . " in: " . substr($stmt, 0, 80);
    }
}

// Check if menu_categories is empty, then seed data
$count = $conn->query("SELECT COUNT(*) as c FROM menu_categories")->fetch_assoc()['c'];

if ($count == 0) {
    $seeds = [
        "INSERT INTO menu_categories (name, display_order) VALUES ('Starters',1),('Main Course',2),('Pasta & Risotto',3),('Desserts',4),('Drinks',5)",
    ];

    $itemSeeds = [
        [1, 'Bruschetta al Pomodoro', 'Toasted bread with fresh tomatoes, garlic, basil and olive oil', 8.50, 1],
        [1, 'Caprese Salad', 'Fresh mozzarella, heirloom tomatoes, basil and balsamic glaze', 12.00, 0],
        [1, 'Calamari Fritti', 'Crispy fried calamari rings served with marinara sauce', 14.00, 1],
        [1, 'Antipasto Platter', 'Selection of cured meats, cheeses, olives and pickled vegetables', 18.00, 0],
        [2, 'Bistecca alla Fiorentina', 'Grilled T-bone steak with rosemary, garlic and lemon (16oz)', 52.00, 1],
        [2, 'Pollo al Limone', 'Pan-seared chicken breast with lemon-caper butter sauce', 28.00, 0],
        [2, 'Branzino al Forno', 'Oven-roasted sea bass with capers, olives and cherry tomatoes', 36.00, 1],
        [2, 'Osso Buco', 'Braised veal shank with gremolata and saffron risotto', 44.00, 1],
        [3, 'Tagliatelle al Ragù', 'Hand-cut pasta with slow-cooked Bolognese meat sauce', 22.00, 1],
        [3, 'Penne all\'Arrabbiata', 'Penne pasta with spicy tomato sauce, garlic and fresh chili', 18.00, 0],
        [3, 'Risotto ai Funghi Porcini', 'Creamy Arborio rice with wild porcini mushrooms and Parmigiano', 24.00, 0],
        [3, 'Gnocchi al Gorgonzola', 'Handmade potato gnocchi with creamy Gorgonzola and walnut sauce', 20.00, 0],
        [4, 'Tiramisu', 'Classic Italian dessert with mascarpone, espresso and ladyfingers', 9.00, 1],
        [4, 'Panna Cotta', 'Vanilla panna cotta with seasonal berry coulis', 8.00, 0],
        [4, 'Cannoli Siciliani', 'Two crispy shells filled with sweetened ricotta and chocolate chips', 8.50, 0],
        [4, 'Gelato del Giorno', 'Three scoops of house-made gelato (ask your server for flavors)', 7.00, 0],
        [5, 'San Pellegrino', 'Still or sparkling mineral water (750ml)', 4.00, 0],
        [5, 'Italian Sodas', 'Limonata, Aranciata or Chinotto (330ml)', 4.50, 0],
        [5, 'Espresso', 'Single or double shot espresso', 3.50, 0],
        [5, 'House Wine', 'Glass of red or white Italian house wine', 9.00, 0],
    ];

    foreach ($seeds as $s) {
        if ($conn->query($s)) {
            $success[] = "Seeded: menu_categories";
        } else {
            $errors[] = "Seed error: " . $conn->error;
        }
    }

    $stmt = $conn->prepare("INSERT INTO menu_items (category_id, name, description, price, is_featured) VALUES (?, ?, ?, ?, ?)");
    foreach ($itemSeeds as $item) {
        $stmt->bind_param("issdi", $item[0], $item[1], $item[2], $item[3], $item[4]);
        if ($stmt->execute()) {
            $success[] = "Seeded item: " . $item[1];
        } else {
            $errors[] = "Item seed error: " . $conn->error;
        }
    }
    $stmt->close();
} else {
    $success[] = "Data already exists — skipped seeding ($count categories found).";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Setup</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; max-width: 700px; margin: 40px auto; padding: 0 20px; color: #1a1a1a; }
        h1 { color: #8B1A1A; }
        .ok  { color: #065f46; background: #d1fae5; padding: 6px 12px; border-radius: 6px; margin: 4px 0; font-size: 0.85rem; }
        .err { color: #991b1b; background: #fee2e2; padding: 6px 12px; border-radius: 6px; margin: 4px 0; font-size: 0.85rem; }
        .cta { display: inline-block; margin-top: 24px; padding: 12px 28px; background: #8B1A1A; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .warn { background: #fef3c7; border: 1px solid #fbbf24; padding: 14px 18px; border-radius: 8px; margin-top: 24px; }
    </style>
</head>
<body>
    <h1>La Bella Cucina - Database Setup</h1>

    <?php if (empty($errors)): ?>
    <p style="color:#065f46; font-weight:600;">Setup completed successfully!</p>
    <?php else: ?>
    <p style="color:#991b1b; font-weight:600;"><?php echo count($errors); ?> error(s) occurred.</p>
    <?php endif; ?>

    <h3>Results:</h3>
    <?php foreach ($success as $s): ?>
    <div class="ok"><?php echo htmlspecialchars($s); ?></div>
    <?php endforeach; ?>
    <?php foreach ($errors as $e): ?>
    <div class="err"><?php echo htmlspecialchars($e); ?></div>
    <?php endforeach; ?>

    <div class="warn">
        <strong>Security Notice:</strong> Delete <code>setup.php</code> after setup is complete to prevent unauthorized re-runs.
    </div>

    <a href="index.php" class="cta">Go to Website</a>
    &nbsp;
    <a href="admin.php" class="cta" style="background:#2D2D2D;">Admin Panel</a>
</body>
</html>
