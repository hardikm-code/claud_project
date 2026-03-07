-- ============================================================
-- TilesCraft Pro - E-Commerce Database Schema
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Product categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL UNIQUE,
  `description` text DEFAULT NULL,
  `sort_order` int(5) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products table
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL UNIQUE,
  `description` text DEFAULT NULL,
  `features` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sale_price` decimal(10,2) DEFAULT 0.00,
  `sku` varchar(100) DEFAULT NULL,
  `stock` int(11) DEFAULT 100,
  `size` varchar(50) DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `finish` varchar(100) DEFAULT NULL,
  `color` varchar(100) DEFAULT NULL,
  `thickness` varchar(30) DEFAULT NULL,
  `coverage` varchar(50) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `best_seller` tinyint(1) DEFAULT 0,
  `new_arrival` tinyint(1) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders table
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL UNIQUE,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(200) NOT NULL,
  `customer_email` varchar(200) NOT NULL,
  `customer_phone` varchar(30) DEFAULT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `zip` varchar(20) NOT NULL,
  `country` varchar(100) DEFAULT 'United States',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT 'cod',
  `notes` text DEFAULT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order items table
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_sku` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reviews table
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reviewer_name` varchar(100) NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 5,
  `review` text DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Contact messages
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- Seed Data
-- ============================================================

-- Admin user (password: admin123)
INSERT IGNORE INTO `users` (`name`, `email`, `password`, `role`) VALUES
('Admin', 'admin@tilescraft.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Categories
INSERT IGNORE INTO `categories` (`id`, `name`, `slug`, `description`, `sort_order`) VALUES
(1, 'Floor Tiles', 'floor-tiles', 'Durable and stylish tiles designed for flooring applications', 1),
(2, 'Wall Tiles', 'wall-tiles', 'Decorative tiles perfect for walls and backsplashes', 2),
(3, 'Outdoor Tiles', 'outdoor-tiles', 'Weather-resistant tiles for patios, driveways and outdoor areas', 3),
(4, 'Mosaic Tiles', 'mosaic-tiles', 'Artistic mosaic tiles for unique decorative accents', 4),
(5, 'Bathroom Tiles', 'bathroom-tiles', 'Water-resistant tiles crafted for bathrooms and wet areas', 5),
(6, 'Kitchen Tiles', 'kitchen-tiles', 'Easy-clean tiles perfect for kitchen floors and splashbacks', 6);

-- Products - Floor Tiles
INSERT IGNORE INTO `products` (`id`,`category_id`,`name`,`slug`,`description`,`features`,`price`,`sale_price`,`sku`,`stock`,`size`,`material`,`finish`,`color`,`thickness`,`coverage`,`featured`,`best_seller`,`new_arrival`) VALUES
(1, 1, 'Travertine Classic Beige', 'travertine-classic-beige', 'Timeless travertine-effect floor tile with natural stone appearance. Perfect for living rooms, hallways and commercial spaces.', 'Slip-resistant surface|Frost resistant|Easy maintenance|Suitable for underfloor heating', 89.99, 74.99, 'FL-TRV-001', 250, '60x60cm', 'Porcelain', 'Matt', 'Beige/Cream', '10mm', '1.08 m² per box', 1, 1, 0),
(2, 1, 'Slate Dark Anthracite', 'slate-dark-anthracite', 'Bold slate-effect porcelain tile in deep anthracite. Creates a dramatic modern statement in any space.', 'R11 anti-slip rating|Frost resistant|Indoor/Outdoor use|Low maintenance', 95.00, 0, 'FL-SLT-002', 180, '60x60cm', 'Porcelain', 'Textured Matt', 'Dark Grey/Anthracite', '10mm', '1.08 m² per box', 1, 0, 0),
(3, 1, 'Marble White Carrara', 'marble-white-carrara', 'Luxurious Carrara marble-effect porcelain. The white background with subtle grey veining adds elegance to any interior.', 'Polished finish|Suitable for living areas|Large format|Rectified edges', 125.00, 99.00, 'FL-MRB-003', 150, '80x80cm', 'Porcelain', 'Polished', 'White/Grey Veins', '9.5mm', '0.96 m² per box', 1, 1, 0),
(4, 1, 'Terracotta Rustic Red', 'terracotta-rustic-red', 'Traditional terracotta-inspired tile with earthy warmth. Ideal for Mediterranean or rustic interior styles.', 'Aged effect finish|Suitable for living areas|Thermal properties|Unique natural look', 65.00, 0, 'FL-TRC-004', 300, '45x45cm', 'Ceramic', 'Matt', 'Terracotta/Rust', '8.5mm', '1.44 m² per box', 0, 0, 1),
(5, 1, 'Concrete Grey Large Format', 'concrete-grey-large-format', 'Industrial-style concrete-effect tile. Pairs beautifully with modern minimalist interiors and polished furniture.', 'Industrial aesthetic|Anti-scratch surface|Rectified edges|Easy to clean', 110.00, 88.00, 'FL-CON-005', 120, '120x60cm', 'Porcelain', 'Matt', 'Mid Grey', '10mm', '0.72 m² per box', 0, 1, 1),
(6, 1, 'Herringbone Oak Wood Effect', 'herringbone-oak-wood-effect', 'Classic herringbone-pattern wood-effect tile. Get the warmth of timber with the durability of porcelain.', 'Realistic wood grain|Waterproof|Scratch resistant|Suitable for all rooms', 78.50, 0, 'FL-WD-006', 200, '60x15cm', 'Porcelain', 'Satin', 'Oak/Warm Brown', '9mm', '1.08 m² per box', 0, 0, 1);

-- Products - Wall Tiles
INSERT IGNORE INTO `products` (`id`,`category_id`,`name`,`slug`,`description`,`features`,`price`,`sale_price`,`sku`,`stock`,`size`,`material`,`finish`,`color`,`thickness`,`coverage`,`featured`,`best_seller`,`new_arrival`) VALUES
(7, 2, 'Metro White Gloss', 'metro-white-gloss', 'Iconic metro brick tile in brilliant white gloss. A timeless choice for kitchens, bathrooms and feature walls.', 'High gloss finish|Easy to clean|Classic subway style|Suitable for wet areas', 28.50, 0, 'WL-MTR-001', 500, '20x10cm', 'Ceramic', 'Gloss', 'Brilliant White', '6.5mm', '2.0 m² per box', 1, 1, 0),
(8, 2, 'Hexagon White Matt', 'hexagon-white-matt', 'Trendy hexagonal wall tile in soft white matt. Perfect for creating geometric feature walls in contemporary bathrooms.', 'Geometric design|Matt finish|Feature wall tile|Suitable for wet areas', 45.00, 38.00, 'WL-HEX-002', 350, '17.5x20cm', 'Ceramic', 'Matt', 'Off-White', '7mm', '1.5 m² per box', 1, 0, 1),
(9, 2, 'Zellige Teal Handmade', 'zellige-teal-handmade', 'Authentic Moroccan-inspired zellige tile in rich teal. Each tile is unique with natural colour variation for a handcrafted look.', 'Handcrafted appearance|Natural variation|Vibrant colour|Artisanal finish', 72.00, 0, 'WL-ZLG-003', 180, '10x10cm', 'Ceramic', 'Gloss', 'Teal/Turquoise', '8mm', '1.0 m² per box', 0, 0, 1),
(10, 2, 'Marble Effect Calacatta Gold', 'marble-calacatta-gold', 'Premium Calacatta Gold marble-effect wall tile. The dramatic gold veining creates a luxurious statement in any bathroom or kitchen.', 'Rectified edges|Suitable for feature walls|Luxurious appearance|Easy to maintain', 145.00, 115.00, 'WL-MRB-004', 100, '60x120cm', 'Porcelain', 'Polished', 'White/Gold Veins', '9mm', '0.72 m² per box', 1, 1, 0),
(11, 2, 'Fish Scale Blue', 'fish-scale-blue', 'Playful fish scale (scallop) tiles in ocean blue. Creates a stunning feature wall with a nautical or Art Nouveau feel.', 'Unique scallop shape|Bold colour|Easy to clean|Suitable for bathrooms', 52.00, 44.00, 'WL-FSH-005', 280, '10x11.5cm', 'Ceramic', 'Gloss', 'Ocean Blue', '7mm', '1.2 m² per box', 0, 0, 1);

-- Products - Outdoor Tiles
INSERT IGNORE INTO `products` (`id`,`category_id`,`name`,`slug`,`description`,`features`,`price`,`sale_price`,`sku`,`stock`,`size`,`material`,`finish`,`color`,`thickness`,`coverage`,`featured`,`best_seller`,`new_arrival`) VALUES
(12, 3, 'Sandstone Patio Beige', 'sandstone-patio-beige', 'Natural sandstone-effect outdoor porcelain tile. Durable enough for heavy foot traffic and resistant to all weather conditions.', 'R12 anti-slip rating|Frost resistant|UV resistant|20mm thick for outdoor use', 82.00, 0, 'OD-SND-001', 400, '60x60cm', 'Porcelain', 'Textured', 'Sandy Beige', '20mm', '1.08 m² per box', 1, 1, 0),
(13, 3, 'Brushed Concrete Outdoor', 'brushed-concrete-outdoor', 'Industrial brushed concrete effect outdoor tile. Perfect for modern patios, pool surrounds and garden paths.', 'Slip resistant|Frost resistant|Suitable for pool areas|Contemporary look', 98.00, 80.00, 'OD-CON-002', 220, '60x60cm', 'Porcelain', 'Brushed', 'Light Grey', '20mm', '1.08 m² per box', 0, 0, 0),
(14, 3, 'Indian Blue Limestone', 'indian-blue-limestone', 'Natural blue limestone effect tile with beautiful natural movement. Brings character to outdoor living areas.', 'Natural stone effect|Weather resistant|Aged appearance|Suitable for driveways', 75.00, 0, 'OD-LMS-003', 160, '45x45cm', 'Porcelain', 'Matt', 'Blue/Grey', '20mm', '1.44 m² per box', 0, 1, 0);

-- Products - Mosaic Tiles
INSERT IGNORE INTO `products` (`id`,`category_id`,`name`,`slug`,`description`,`features`,`price`,`sale_price`,`sku`,`stock`,`size`,`material`,`finish`,`color`,`thickness`,`coverage`,`featured`,`best_seller`,`new_arrival`) VALUES
(15, 4, 'Glass Mosaic Ocean Blue', 'glass-mosaic-ocean-blue', 'Shimmering glass mosaic tile in ocean blue tones. Creates a stunning feature in swimming pools, showers and feature walls.', 'Glass material|Highly reflective|Waterproof|Pool safe', 120.00, 96.00, 'MS-GLS-001', 300, '30x30cm (2.5cm chips)', 'Glass', 'Glossy', 'Blue/Aqua Mix', '4mm', '0.09 m² per sheet', 1, 1, 0),
(16, 4, 'Penny Round White Gloss', 'penny-round-white-gloss', 'Classic penny round mosaic in brilliant white. A versatile tile that works in both traditional and contemporary schemes.', 'Classic round shape|Gloss finish|Suitable for floors and walls|Easy to install with mesh backing', 65.00, 0, 'MS-PNY-002', 250, '30x30cm (2.5cm rounds)', 'Ceramic', 'Gloss', 'Brilliant White', '5mm', '0.09 m² per sheet', 0, 0, 1),
(17, 4, 'Arabesque Marble Mosaic', 'arabesque-marble-mosaic', 'Intricate arabesque-shaped marble mosaic. Each piece hand-finished to create a truly stunning decorative feature.', 'Handcrafted|Natural marble|Unique pattern|Ideal for feature walls', 185.00, 155.00, 'MS-ARB-003', 80, '30x30cm sheet', 'Natural Marble', 'Honed', 'White/Grey', '10mm', '0.09 m² per sheet', 1, 0, 0);

-- Products - Bathroom Tiles
INSERT IGNORE INTO `products` (`id`,`category_id`,`name`,`slug`,`description`,`features`,`price`,`sale_price`,`sku`,`stock`,`size`,`material`,`finish`,`color`,`thickness`,`coverage`,`featured`,`best_seller`,`new_arrival`) VALUES
(18, 5, 'Travertine Spa Ivory', 'travertine-spa-ivory', 'Luxurious travertine-effect bathroom tile in warm ivory. Creates a spa-like atmosphere in any bathroom.', 'Non-slip surface|Suitable for floors and walls|Warm ivory tone|Easy maintenance', 105.00, 84.00, 'BT-TRV-001', 200, '60x30cm', 'Porcelain', 'Matt', 'Ivory/Cream', '9mm', '1.08 m² per box', 1, 1, 0),
(19, 5, 'Nordic White Large Plank', 'nordic-white-large-plank', 'Minimalist Scandinavian-inspired large plank tile. The elongated format makes bathrooms appear larger and more elegant.', 'Minimalist design|Large format|Suitable for modern bathrooms|Light reflective', 88.00, 0, 'BT-NRD-002', 175, '120x30cm', 'Porcelain', 'Matt', 'Pure White', '9mm', '0.72 m² per box', 0, 0, 1),
(20, 5, 'Sage Green Metro', 'sage-green-metro', 'Calming sage green metro tile for a fresh, botanical bathroom feel. Pairs beautifully with natural wood and brass fixtures.', 'Trending colour|Classic metro shape|Suitable for wet areas|Earthy tones', 38.00, 0, 'BT-SGN-003', 400, '20x10cm', 'Ceramic', 'Gloss', 'Sage Green', '6.5mm', '2.0 m² per box', 0, 1, 0);

-- Products - Kitchen Tiles
INSERT IGNORE INTO `products` (`id`,`category_id`,`name`,`slug`,`description`,`features`,`price`,`sale_price`,`sku`,`stock`,`size`,`material`,`finish`,`color`,`thickness`,`coverage`,`featured`,`best_seller`,`new_arrival`) VALUES
(21, 6, 'Terracotta Kitchen Floor', 'terracotta-kitchen-floor', 'Classic terracotta kitchen floor tile. The warm earthy tones bring a rustic Mediterranean charm to any kitchen.', 'Anti-slip|Easy to clean|Hygienic surface|Suitable for kitchen floors', 58.00, 48.00, 'KT-TRC-001', 280, '30x30cm', 'Ceramic', 'Matt', 'Terracotta', '8mm', '1.44 m² per box', 0, 1, 0),
(22, 6, 'Moroccan Pattern Encaustic', 'moroccan-pattern-encaustic', 'Stunning encaustic cement-effect tile with intricate Moroccan pattern. A statement floor tile for bohemian kitchen designs.', 'Eye-catching pattern|Unique design|Focal point tile|Suitable for walls too', 92.00, 75.00, 'KT-MRC-002', 150, '20x20cm', 'Ceramic', 'Matt', 'Multi-colour', '9mm', '1.0 m² per box', 1, 0, 1),
(23, 6, 'Subway Splashback Cream', 'subway-splashback-cream', 'Versatile cream subway tile perfect for kitchen splashbacks. The slightly off-white tone adds warmth without sacrificing style.', 'Easy to clean|Suitable for wet areas|Timeless design|Grease resistant', 32.00, 0, 'KT-SBW-003', 600, '30x10cm', 'Ceramic', 'Gloss', 'Cream/Off-White', '7mm', '1.5 m² per box', 0, 1, 0);

-- Sample reviews
INSERT IGNORE INTO `reviews` (`product_id`,`user_id`,`reviewer_name`,`rating`,`review`) VALUES
(1, NULL, 'Sarah M.', 5, 'Absolutely stunning tiles! The travertine effect is incredibly realistic and they transformed my living room completely.'),
(1, NULL, 'James T.', 5, 'Great quality and fast delivery. The colour matched perfectly with my existing decor.'),
(3, NULL, 'Elena R.', 5, 'The Carrara marble effect is breathtaking. My clients cannot believe these are porcelain and not real marble.'),
(7, NULL, 'Michael B.', 5, 'Classic metro tiles at a great price. Very easy to lay and look fantastic in my kitchen.'),
(12, NULL, 'David K.', 4, 'Excellent outdoor tiles. Very slip-resistant even when wet. My patio looks amazing.'),
(15, NULL, 'Anna L.', 5, 'The glass mosaic tiles in my shower are absolutely beautiful. They catch the light perfectly.');
