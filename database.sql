-- ============================================================
-- Bella Vista Restaurant Database Schema
-- Run this via setup.php or import manually into phpMyAdmin
-- Default Admin: username=admin  password=admin123
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ── Drop existing tables ──────────────────────────────────
DROP TABLE IF EXISTS `menu_items`;
DROP TABLE IF EXISTS `menu_categories`;
DROP TABLE IF EXISTS `reservations`;
DROP TABLE IF EXISTS `admin_users`;
DROP TABLE IF EXISTS `contact_messages`;

-- ── Menu Categories ───────────────────────────────────────
CREATE TABLE `menu_categories` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(100) NOT NULL,
  `description`   VARCHAR(255) DEFAULT NULL,
  `display_order` TINYINT UNSIGNED DEFAULT 0,
  `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Menu Items ────────────────────────────────────────────
CREATE TABLE `menu_items` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id`  INT UNSIGNED NOT NULL,
  `name`         VARCHAR(150) NOT NULL,
  `description`  TEXT DEFAULT NULL,
  `price`        DECIMAL(8,2) NOT NULL,
  `image_url`    VARCHAR(500) DEFAULT '',
  `is_featured`  TINYINT(1) DEFAULT 0,
  `is_available` TINYINT(1) DEFAULT 1,
  `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_category` (`category_id`),
  CONSTRAINT `fk_menu_category` FOREIGN KEY (`category_id`) REFERENCES `menu_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Reservations ─────────────────────────────────────────
CREATE TABLE `reservations` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(150) NOT NULL,
  `email`            VARCHAR(255) NOT NULL,
  `phone`            VARCHAR(30)  NOT NULL,
  `date`             DATE         NOT NULL,
  `time`             TIME         NOT NULL,
  `guests`           TINYINT UNSIGNED NOT NULL DEFAULT 2,
  `special_requests` TEXT DEFAULT NULL,
  `status`           ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_date` (`date`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Admin Users ───────────────────────────────────────────
CREATE TABLE `admin_users` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`      VARCHAR(80) NOT NULL UNIQUE,
  `email`         VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Contact Messages ──────────────────────────────────────
CREATE TABLE `contact_messages` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(150) NOT NULL,
  `email`      VARCHAR(255) NOT NULL,
  `subject`    VARCHAR(255) DEFAULT NULL,
  `message`    TEXT NOT NULL,
  `is_read`    TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- SAMPLE DATA
-- ────────────────────────────────────────────────────────────

-- Categories
INSERT INTO `menu_categories` (`name`, `description`, `display_order`) VALUES
('Starters',       'Begin your culinary journey with our exquisite appetizers',   1),
('Soups & Salads', 'Fresh, vibrant, and bursting with seasonal flavors',           2),
('Main Course',    'Expertly crafted entrées that define fine dining',              3),
('Grilled & BBQ',  'Perfectly grilled to bring out natural, smoky flavors',        4),
('Pasta & Pizza',  'Handmade pasta and wood-fired pizzas crafted with care',        5),
('Desserts',       'Sweet endings to complement a perfect dining experience',       6),
('Beverages',      'Curated drinks, cocktails, and fine wines',                     7);

-- ── Starters (category_id = 1) ───────────────────────────
INSERT INTO `menu_items` (`category_id`,`name`,`description`,`price`,`image_url`,`is_featured`,`is_available`) VALUES
(1,'Bruschetta al Pomodoro','Toasted sourdough topped with fresh tomatoes, basil, garlic, and a drizzle of extra virgin olive oil',9.50,'https://images.unsplash.com/photo-1572695157366-5e585ab2b69f?w=400&h=280&fit=crop',1,1),
(1,'Burrata Caprese','Creamy burrata with heirloom tomatoes, fresh basil, and aged balsamic glaze',14.00,'https://images.unsplash.com/photo-1608897013039-887f21d8c804?w=400&h=280&fit=crop',0,1),
(1,'Crispy Calamari','Lightly breaded and fried squid rings served with zesty marinara and lemon aioli',12.50,'https://images.unsplash.com/photo-1599487488170-d11ec9c172f0?w=400&h=280&fit=crop',0,1),
(1,'Shrimp Cocktail','Chilled jumbo shrimp with house-made cocktail sauce and fresh lemon wedges',16.00,'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=400&h=280&fit=crop',1,1);

-- ── Soups & Salads (category_id = 2) ─────────────────────
INSERT INTO `menu_items` (`category_id`,`name`,`description`,`price`,`image_url`,`is_featured`,`is_available`) VALUES
(2,'French Onion Soup','Slow-cooked caramelized onion broth topped with a crouton and melted Gruyère cheese',11.00,'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=400&h=280&fit=crop',0,1),
(2,'Classic Caesar Salad','Crisp romaine hearts, house Caesar dressing, shaved Parmesan, and herb croutons',13.50,'https://images.unsplash.com/photo-1546793665-c74683f339c1?w=400&h=280&fit=crop',0,1),
(2,'Mediterranean Salad','Mixed greens with olives, cucumber, feta cheese, cherry tomatoes, and lemon vinaigrette',12.00,'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=400&h=280&fit=crop',0,1),
(2,'Lobster Bisque','Rich, velvety soup made with whole Maine lobster, cream, and a hint of brandy',16.00,'https://images.unsplash.com/photo-1476718406336-bb5a9690ee2a?w=400&h=280&fit=crop',0,1);

-- ── Main Course (category_id = 3) ────────────────────────
INSERT INTO `menu_items` (`category_id`,`name`,`description`,`price`,`image_url`,`is_featured`,`is_available`) VALUES
(3,'Pan-Seared Salmon','Atlantic salmon with lemon butter sauce, asparagus, and wild mushroom risotto',32.00,'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=400&h=280&fit=crop',1,1),
(3,'Beef Tenderloin','8 oz center-cut filet with truffle butter, garlic mashed potato, and seasonal vegetables',48.00,'https://images.unsplash.com/photo-1546964124-0cce460f38ef?w=400&h=280&fit=crop',1,1),
(3,'Roasted Duck Breast','Confit duck leg and seared breast with cherry reduction, polenta, and haricots verts',38.00,'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=400&h=280&fit=crop',0,1),
(3,'Chicken Marsala','Free-range chicken breast sautéed in Marsala wine with mushrooms and garlic herb butter',28.00,'https://images.unsplash.com/photo-1598103442097-8b74394b95c3?w=400&h=280&fit=crop',0,1);

-- ── Grilled & BBQ (category_id = 4) ──────────────────────
INSERT INTO `menu_items` (`category_id`,`name`,`description`,`price`,`image_url`,`is_featured`,`is_available`) VALUES
(4,'Ribeye Steak','16 oz prime dry-aged ribeye with chimichurri, roasted garlic, and truffle fries',55.00,'https://images.unsplash.com/photo-1558030006-450675393462?w=400&h=280&fit=crop',1,1),
(4,'BBQ Lamb Chops','New Zealand lamb chops marinated in herbs, grilled and served with minted pea purée',44.00,'https://images.unsplash.com/photo-1529193591184-b1d58069ecdd?w=400&h=280&fit=crop',0,1),
(4,'Grilled Sea Bass','Whole Mediterranean sea bass with olive tapenade, roasted tomatoes, and lemon-caper butter',42.00,'https://images.unsplash.com/photo-1476224203421-9ac39bcb3327?w=400&h=280&fit=crop',0,1),
(4,'Mixed Grill Platter','Assorted grilled meats – chicken, beef, lamb – served with three dipping sauces and pita',36.00,'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=400&h=280&fit=crop',0,1);

-- ── Pasta & Pizza (category_id = 5) ──────────────────────
INSERT INTO `menu_items` (`category_id`,`name`,`description`,`price`,`image_url`,`is_featured`,`is_available`) VALUES
(5,'Lobster Linguine','Fresh linguine tossed with butter-poached lobster, cherry tomatoes, and white wine sauce',36.00,'https://images.unsplash.com/photo-1621996346565-e3dbc353d2e5?w=400&h=280&fit=crop',1,1),
(5,'Truffle Tagliatelle','Hand-rolled egg pasta with black truffle cream, wild mushrooms, and shaved Parmesan',28.00,'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?w=400&h=280&fit=crop',0,1),
(5,'Margherita Pizza','Wood-fired pizza with San Marzano tomato, buffalo mozzarella, and fresh basil',18.00,'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?w=400&h=280&fit=crop',0,1),
(5,'Four Cheese Pizza','Mozzarella, gorgonzola, fontina, and aged Parmesan on a thin crispy crust',21.00,'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&h=280&fit=crop',0,1);

-- ── Desserts (category_id = 6) ───────────────────────────
INSERT INTO `menu_items` (`category_id`,`name`,`description`,`price`,`image_url`,`is_featured`,`is_available`) VALUES
(6,'Crème Brûlée','Classic French vanilla custard with caramelized sugar crust and seasonal berries',10.00,'https://images.unsplash.com/photo-1470124182917-cc6e71b22ecc?w=400&h=280&fit=crop',0,1),
(6,'Chocolate Fondant','Warm dark chocolate lava cake with vanilla bean ice cream and raspberry coulis',12.00,'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=400&h=280&fit=crop',1,1),
(6,'Tiramisu','Authentic Italian tiramisu with espresso-soaked ladyfingers, mascarpone, and cocoa',11.00,'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=400&h=280&fit=crop',0,1),
(6,'Panna Cotta','Silky Italian cream dessert with mixed berry compote and mint',9.50,'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=400&h=280&fit=crop',0,1);

-- ── Beverages (category_id = 7) ──────────────────────────
INSERT INTO `menu_items` (`category_id`,`name`,`description`,`price`,`image_url`,`is_featured`,`is_available`) VALUES
(7,'Aperol Spritz','Aperol, Prosecco, soda water, and a slice of orange over ice',12.00,'https://images.unsplash.com/photo-1536935338788-846bb9981813?w=400&h=280&fit=crop',0,1),
(7,'Negroni','Gin, Campari, and sweet vermouth stirred with a twist of orange peel',13.00,'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?w=400&h=280&fit=crop',0,1),
(7,'Sparkling Water','Acqua Panna or San Pellegrino – still or sparkling',4.00,'https://images.unsplash.com/photo-1548839140-29a749e1cf4d?w=400&h=280&fit=crop',0,1),
(7,'Fresh Lemonade','House-squeezed lemonade with mint and a touch of honey',6.00,'https://images.unsplash.com/photo-1621263764928-df1444c5e859?w=400&h=280&fit=crop',0,1);

SET FOREIGN_KEY_CHECKS = 1;
