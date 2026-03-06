-- La Bella Cucina Restaurant Database
-- Run this SQL to set up the database tables

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

-- Seed menu categories
INSERT INTO `menu_categories` (`name`, `display_order`) VALUES
('Starters', 1),
('Main Course', 2),
('Pasta & Risotto', 3),
('Desserts', 4),
('Drinks', 5)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Seed menu items
INSERT INTO `menu_items` (`category_id`, `name`, `description`, `price`, `is_featured`) VALUES
(1, 'Bruschetta al Pomodoro', 'Toasted bread topped with fresh tomatoes, garlic, basil and olive oil', 8.50, 1),
(1, 'Caprese Salad', 'Fresh mozzarella, heirloom tomatoes, basil and balsamic glaze', 12.00, 0),
(1, 'Calamari Fritti', 'Crispy fried calamari rings served with marinara sauce', 14.00, 1),
(1, 'Antipasto Platter', 'Selection of cured meats, cheeses, olives and pickled vegetables', 18.00, 0),
(2, 'Bistecca alla Fiorentina', 'Grilled T-bone steak with rosemary, garlic and lemon (16oz)', 52.00, 1),
(2, 'Pollo al Limone', 'Pan-seared chicken breast with lemon-caper butter sauce and seasonal vegetables', 28.00, 0),
(2, 'Branzino al Forno', 'Oven-roasted sea bass with capers, olives and cherry tomatoes', 36.00, 1),
(2, 'Osso Buco', 'Braised veal shank with gremolata and saffron risotto', 44.00, 1),
(3, 'Tagliatelle al Ragù', 'Hand-cut pasta with slow-cooked Bolognese meat sauce', 22.00, 1),
(3, 'Penne all\'Arrabbiata', 'Penne pasta with spicy tomato sauce, garlic and fresh chili', 18.00, 0),
(3, 'Risotto ai Funghi Porcini', 'Creamy Arborio rice with wild porcini mushrooms and Parmigiano', 24.00, 0),
(3, 'Gnocchi al Gorgonzola', 'Handmade potato gnocchi with creamy Gorgonzola and walnut sauce', 20.00, 0),
(4, 'Tiramisu', 'Classic Italian dessert with mascarpone, espresso and ladyfingers', 9.00, 1),
(4, 'Panna Cotta', 'Vanilla panna cotta with seasonal berry coulis', 8.00, 0),
(4, 'Cannoli Siciliani', 'Two crispy shells filled with sweetened ricotta and chocolate chips', 8.50, 0),
(4, 'Gelato del Giorno', 'Three scoops of house-made gelato (ask your server for today\'s flavors)', 7.00, 0),
(5, 'San Pellegrino', 'Still or sparkling mineral water (750ml)', 4.00, 0),
(5, 'Italian Sodas', 'Limonata, Aranciata or Chinotto (330ml)', 4.50, 0),
(5, 'Espresso', 'Single or double shot espresso', 3.50, 0),
(5, 'House Wine', 'Glass of red or white Italian house wine', 9.00, 0);
