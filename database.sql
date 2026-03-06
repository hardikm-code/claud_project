-- La Bella Cucina Restaurant Database
-- Import this file via phpMyAdmin or MySQL CLI

CREATE TABLE IF NOT EXISTS `menu_categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `display_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(8,2) NOT NULL,
  `badge` VARCHAR(50) DEFAULT NULL,
  `is_available` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `menu_categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `reservations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `date` DATE NOT NULL,
  `time` TIME NOT NULL,
  `guests` INT NOT NULL,
  `message` TEXT,
  `status` ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Menu Categories
INSERT INTO `menu_categories` (`name`, `slug`, `display_order`) VALUES
('Starters', 'starters', 1),
('Main Course', 'mains', 2),
('Pasta & Risotto', 'pasta', 3),
('Desserts', 'desserts', 4),
('Beverages', 'beverages', 5);

-- Starters
INSERT INTO `menu_items` (`category_id`, `name`, `description`, `price`, `badge`) VALUES
(1, 'Bruschetta al Pomodoro', 'Toasted sourdough topped with fresh tomatoes, basil, garlic, and extra virgin olive oil.', 8.50, 'Chef''s Pick'),
(1, 'Burrata e Prosciutto', 'Creamy burrata cheese with San Daniele prosciutto, arugula, and aged balsamic glaze.', 14.00, NULL),
(1, 'Calamari Fritti', 'Crispy fried calamari rings served with lemon aioli and marinara sauce.', 12.50, 'Popular'),
(1, 'Zuppa di Giorno', 'Soup of the day made with seasonal vegetables. Ask your server for today''s selection.', 9.00, NULL),
(1, 'Antipasto Misto', 'Curated selection of cured meats, artisan cheeses, marinated olives, and roasted peppers.', 18.00, 'Sharing');

-- Main Course
INSERT INTO `menu_items` (`category_id`, `name`, `description`, `price`, `badge`) VALUES
(2, 'Branzino al Forno', 'Whole roasted sea bass with lemon, capers, cherry tomatoes, and white wine sauce.', 32.00, 'Chef''s Pick'),
(2, 'Osso Buco alla Milanese', 'Slow-braised veal shank with gremolata, saffron risotto, and red wine jus.', 38.00, 'Signature'),
(2, 'Pollo alla Cacciatora', 'Free-range chicken braised with tomatoes, olives, capers, rosemary, and white wine.', 26.00, NULL),
(2, 'Bistecca alla Fiorentina', 'Grilled T-bone steak, 600g, with rosemary roasted potatoes and house salad.', 52.00, 'Premium'),
(2, 'Melanzane alla Parmigiana', 'Classic baked eggplant with layers of tomato sauce, mozzarella, and Parmesan.', 22.00, 'Vegetarian');

-- Pasta & Risotto
INSERT INTO `menu_items` (`category_id`, `name`, `description`, `price`, `badge`) VALUES
(3, 'Tagliatelle al Ragù', 'Fresh egg tagliatelle with slow-cooked beef and pork Bolognese sauce.', 19.00, 'Classic'),
(3, 'Spaghetti alle Vongole', 'Spaghetti with fresh clams, white wine, garlic, chili, and parsley.', 23.00, 'Popular'),
(3, 'Penne all''Arrabbiata', 'Penne with spicy tomato sauce, garlic, and fresh basil. Vegan option available.', 16.00, 'Vegan'),
(3, 'Risotto ai Funghi Porcini', 'Arborio rice with dried and fresh porcini mushrooms, white wine, and aged Parmesan.', 21.00, 'Chef''s Pick'),
(3, 'Gnocchi al Gorgonzola', 'Hand-rolled potato gnocchi with a creamy Gorgonzola and walnut sauce.', 18.00, NULL);

-- Desserts
INSERT INTO `menu_items` (`category_id`, `name`, `description`, `price`, `badge`) VALUES
(4, 'Tiramisù Classico', 'Our legendary tiramisù with savoiardi, mascarpone cream, espresso, and cocoa.', 9.50, 'Must Try'),
(4, 'Panna Cotta al Caramello', 'Silky vanilla panna cotta with salted caramel sauce and toasted almonds.', 8.50, NULL),
(4, 'Cannolo Siciliano', 'Crispy pastry shell filled with sweet ricotta, candied orange peel, and pistachios.', 8.00, NULL),
(4, 'Gelato Artigianale', 'Three scoops of house-made gelato. Ask for today''s flavours.', 7.50, 'Daily'),
(4, 'Torta al Cioccolato', 'Warm dark chocolate fondant with vanilla gelato and berry coulis.', 10.00, 'Popular');

-- Beverages
INSERT INTO `menu_items` (`category_id`, `name`, `description`, `price`, `badge`) VALUES
(5, 'Acqua Minerale', 'Still or sparkling mineral water. 500ml.', 3.50, NULL),
(5, 'Limonata Artigianale', 'House-made lemonade with fresh mint and a hint of elderflower.', 5.50, NULL),
(5, 'Espresso / Doppio', 'Single or double shot of our premium Italian espresso blend.', 3.00, NULL),
(5, 'Vino della Casa', 'House red or white wine. Glass 175ml.', 7.50, NULL),
(5, 'Aperol Spritz', 'Aperol, Prosecco, and soda water with a slice of orange.', 9.50, 'Popular');

-- Sample Reservations
INSERT INTO `reservations` (`name`, `email`, `phone`, `date`, `time`, `guests`, `message`, `status`) VALUES
('Marco Rossi', 'marco@example.com', '+1 555-0101', '2026-03-10', '19:00:00', 2, 'Anniversary dinner, please prepare a small surprise.', 'confirmed'),
('Sofia Bianchi', 'sofia@example.com', '+1 555-0102', '2026-03-11', '20:00:00', 4, '', 'pending'),
('James Anderson', 'james@example.com', '+1 555-0103', '2026-03-12', '18:30:00', 6, 'Birthday celebration, we have a guest with a nut allergy.', 'confirmed');
