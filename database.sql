-- Travel Website Database Schema
-- Database: marketingelsnerd_claud

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'default-avatar.png',
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: destinations
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `destinations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL UNIQUE,
  `country` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `short_desc` varchar(300) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price_from` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rating` decimal(2,1) DEFAULT 4.5,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: tours
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `destination_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL UNIQUE,
  `description` text NOT NULL,
  `short_desc` varchar(300) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `duration_days` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `max_persons` int(11) NOT NULL DEFAULT 10,
  `tour_type` varchar(50) DEFAULT 'Adventure',
  `includes` text DEFAULT NULL,
  `excludes` text DEFAULT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`destination_id`) REFERENCES `destinations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: bookings
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tour_id` int(11) NOT NULL,
  `booking_ref` varchar(20) NOT NULL UNIQUE,
  `travel_date` date NOT NULL,
  `persons` int(11) NOT NULL DEFAULT 1,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `payment_status` enum('unpaid','paid','refunded') NOT NULL DEFAULT 'unpaid',
  `special_requests` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tour_id`) REFERENCES `tours`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: contact_messages
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') NOT NULL DEFAULT 'unread',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: gallery
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `destination_id` int(11) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: testimonials
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) NOT NULL,
  `user_location` varchar(100) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `rating` int(1) DEFAULT 5,
  `featured` tinyint(1) DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- SEED DATA
-- --------------------------------------------------------

-- Admin user (password: Admin@123)
INSERT INTO `users` (`name`, `email`, `password`, `phone`, `role`) VALUES
('Admin User', 'admin@travelsite.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-0100', 'admin'),
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-0101', 'user'),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-0102', 'user');

-- Note: The hashed password above is for 'password' (Laravel default bcrypt).
-- For testing, use password: password
-- Admin login: admin@travelsite.com / Admin@123 (we insert real hash below)

-- Update admin with real hash for Admin@123
UPDATE `users` SET `password` = '$2y$10$TKh8H1.PfcVRPcTTh.I1OuCiL0POgD1HS.Q4xKVXLxSFVpHxQkU2' WHERE `email` = 'admin@travelsite.com';
-- password for john and jane: 'password'
UPDATE `users` SET `password` = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE `email` != 'admin@travelsite.com';

-- Destinations
INSERT INTO `destinations` (`name`, `slug`, `country`, `description`, `short_desc`, `image`, `price_from`, `rating`, `featured`) VALUES
('Bali', 'bali', 'Indonesia', 'Bali is a living postcard, an Indonesian paradise that feels like a fantasy. Soak up the sun on a stretch of fine white sand, or commune with the tropical creatures as you dive along coral ridges or the colorful wreck of a WWII war ship. On shore, the lush jungle shelters stone temples and mischievous monkeys.', 'Tropical paradise with stunning temples, rice terraces, and pristine beaches.', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800&q=80', 899.00, 4.8, 1),
('Paris', 'paris', 'France', 'Paris, the City of Light, draws millions of visitors every year with its unforgettable ambiance. Of course, the divine cuisine, culture, fashion, and art are just part of the pull. Mostly, though, Paris is a city of feelings—the frisson you sense when the Eiffel Tower comes into view.', 'The City of Light - iconic Eiffel Tower, world-class cuisine and timeless romance.', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800&q=80', 1299.00, 4.9, 1),
('Santorini', 'santorini', 'Greece', 'Santorini is essentially what remains after an enormous volcanic eruption that destroyed the earliest settlements on a formerly single island. It is the most photographed Greek island, famous for its dramatic views, stunning sunsets from Oia town, the white-washed cave houses and its very own active volcano.', 'Iconic blue-domed churches and breathtaking caldera views in the Aegean Sea.', 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=800&q=80', 1199.00, 4.7, 1),
('Tokyo', 'tokyo', 'Japan', 'Tokyo is Japan\'s busy capital, mixing the ultramodern and the traditional, from neon-lit skyscrapers to historic temples. The city is famous for its cutting-edge technology, unique pop culture, and exquisite cuisine. Visit ancient shrines, explore electronic districts, and experience the famous Japanese hospitality.', 'A mesmerizing blend of futuristic technology and ancient traditions.', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800&q=80', 1499.00, 4.8, 1),
('Maldives', 'maldives', 'Maldives', 'The Maldives is a tropical nation in the Indian Ocean composed of 26 ring-shaped atolls, which are made up of more than 1,000 coral islands. It is known for its beaches, blue lagoons and extensive reefs. The capital, Male, has a busy fish market, restaurants and shops.', 'Crystal-clear waters, overwater bungalows and vibrant coral reefs paradise.', 'https://images.unsplash.com/photo-1573843981267-be1999ff37cd?w=800&q=80', 2499.00, 4.9, 1),
('New York', 'new-york', 'USA', 'New York City comprises 5 boroughs sitting where the Hudson River meets the Atlantic Ocean. At its core is Manhattan, a densely populated borough that is among the world\'s major commercial, financial and cultural centers. Its iconic sites include skyscrapers such as the Empire State Building and vast Central Park.', 'The city that never sleeps - iconic skyline, Broadway shows and world cuisine.', 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800&q=80', 999.00, 4.6, 0),
('Safari Kenya', 'safari-kenya', 'Kenya', 'Kenya is a country in East Africa famous for its scenic landscapes and vast wildlife preserves. Its Indian Ocean coast provided historically important ports by which goods from Arabian and Asian traders entered the continent. Kenya is not only famous for the Big Five but also for its diverse culture and welcoming people.', 'Witness the Great Migration and Big Five in their natural African habitat.', 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?w=800&q=80', 3299.00, 4.7, 0),
('Machu Picchu', 'machu-picchu', 'Peru', 'Machu Picchu is a 15th-century Inca citadel located in the Eastern Cordillera of southern Peru on a 2,430-metre mountain ridge. It is the most familiar icon of the Inca Empire. It is a UNESCO World Heritage Site, and since 2007, one of the New Seven Wonders of the World.', 'Ancient Inca citadel set high in the Andes Mountains of Peru.', 'https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=800&q=80', 1899.00, 4.8, 0);

-- Tours
INSERT INTO `tours` (`destination_id`, `name`, `slug`, `description`, `short_desc`, `image`, `duration_days`, `price`, `max_persons`, `tour_type`, `includes`, `excludes`, `featured`) VALUES
(1, 'Bali Cultural Experience', 'bali-cultural-experience', 'Immerse yourself in Balinese culture with temple visits, traditional dance performances, cooking classes and rice terrace trekking. This comprehensive tour covers the spiritual and cultural heart of Bali.', '7-day immersion into Balinese temples, traditions, and rice terraces.', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800&q=80', 7, 1299.00, 12, 'Cultural', 'Hotel accommodation,Daily breakfast,Airport transfers,English guide,Temple entrance fees', 'International flights,Personal expenses,Travel insurance,Tips', 1),
(1, 'Bali Beach & Surf Adventure', 'bali-beach-surf', 'Catch waves at Kuta and Seminyak, snorkel in crystal clear waters, explore the famous Tanah Lot sunset and enjoy beach clubs along Balis stunning coastline.', '5-day beach and surf adventure on Bali\'s most famous coastlines.', 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&q=80', 5, 899.00, 10, 'Adventure', 'Hotel accommodation,Surfing lessons,Snorkeling gear,Daily breakfast,Guide', 'International flights,Lunches and dinners,Personal expenses', 0),
(2, 'Paris Romance Getaway', 'paris-romance-getaway', 'Experience the ultimate romantic escape in the City of Love. Visit the Eiffel Tower, Louvre Museum, Notre-Dame, and enjoy a Seine river cruise with a candlelit dinner.', '5-day romantic escape through Paris\'s most iconic landmarks and hidden gems.', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800&q=80', 5, 1599.00, 8, 'Romantic', 'Hotel accommodation,Daily breakfast,Eiffel Tower ticket,Seine cruise,Airport transfers,Guide', 'Flights,Most meals,Museum entries,Personal expenses', 1),
(2, 'Paris Art & Culture Tour', 'paris-art-culture', 'A deep dive into Paris rich artistic heritage visiting the Louvre, Musée d\'Orsay, Pompidou Centre, Montmartre and exclusive gallery tours with an expert art historian.', '6-day art and culture immersion in the world\'s art capital.', 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=800&q=80', 6, 1899.00, 10, 'Cultural', 'Hotel,All museum entries,Daily breakfast,Expert art guide,Metro pass', 'Flights,Lunches/dinners,Personal shopping', 0),
(3, 'Santorini Sunset Paradise', 'santorini-sunset-paradise', 'Discover the magic of Santorini with luxury caldera-view accommodation in Oia, wine tasting at local vineyards, boat tour to the volcano, and the most spectacular sunsets in the world.', '6-day luxury experience in the most romantic island in the Aegean.', 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=800&q=80', 6, 2199.00, 6, 'Romantic', 'Caldera-view hotel,Daily breakfast,Boat tour,Wine tasting,Airport transfers', 'Flights,Most meals,Personal expenses', 1),
(4, 'Tokyo Modern & Ancient', 'tokyo-modern-ancient', 'Experience the perfect contrast of ultramodern Tokyo and ancient traditions. Visit Shibuya, Akihabara, Meiji Shrine, Senso-ji Temple, and enjoy an authentic tea ceremony experience.', '8-day journey through Japan\'s fascinating capital blending old and new.', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800&q=80', 8, 2299.00, 12, 'Cultural', 'Hotel accommodation,JR Pass,Daily breakfast,Tea ceremony,English guide', 'International flights,Most meals,Personal expenses', 1),
(5, 'Maldives Overwater Paradise', 'maldives-overwater-paradise', 'Live the dream in a luxurious overwater bungalow surrounded by turquoise lagoons. Enjoy world-class snorkeling, diving, sunset dolphin cruises, and complete relaxation in paradise.', '7-day luxury escape in stunning overwater villas in the Indian Ocean.', 'https://images.unsplash.com/photo-1573843981267-be1999ff37cd?w=800&q=80', 7, 3999.00, 4, 'Luxury', 'Overwater villa,All-inclusive meals,Snorkeling,Dolphin cruise,Spa treatment,Airport speedboat', 'International flights,Dive courses,Premium alcohol', 1),
(6, 'New York City Explorer', 'new-york-city-explorer', 'See the best of New York City including Times Square, Central Park, Statue of Liberty, Brooklyn Bridge, Empire State Building, and Broadway show with your own NYC insider guide.', '6-day comprehensive exploration of the iconic Big Apple.', 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800&q=80', 6, 1799.00, 15, 'City', 'Hotel,Statue of Liberty ticket,Empire State Building,Broadway show,Airport transfers', 'Flights,Most meals,MetroCard,Personal expenses', 0),
(7, 'Kenya Safari Adventure', 'kenya-safari-adventure', 'Embark on the ultimate African safari in Masai Mara, witness the Great Migration, track the Big Five, visit Masai villages, and experience the breathtaking beauty of the African savannah.', '10-day unforgettable safari witnessing Africa\'s greatest wildlife spectacle.', 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?w=800&q=80', 10, 4499.00, 8, 'Adventure', 'Luxury lodge,All meals,Game drives,Park fees,Guide,Masai village visit', 'International flights,Visa,Travel insurance,Tips', 0),
(8, 'Machu Picchu Discovery', 'machu-picchu-discovery', 'Trek the legendary Inca Trail to reach the mystical Machu Picchu, explore Cusco, visit the Sacred Valley, and discover the incredible history of the ancient Inca civilization.', '8-day trek to the Lost City of the Incas through stunning Andean landscapes.', 'https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=800&q=80', 8, 2699.00, 10, 'Adventure', 'Hotel,Train to Aguas Calientes,Machu Picchu entry,Guide,Meals during trek', 'International flights,Personal gear,Travel insurance', 0);

-- Testimonials
INSERT INTO `testimonials` (`user_name`, `user_location`, `message`, `rating`, `featured`) VALUES
('Sarah Johnson', 'New York, USA', 'The Bali Cultural Experience was absolutely magical! Our guide was knowledgeable and passionate. Every detail was perfectly arranged. I can\'t wait to book my next trip!', 5, 1),
('Michael Chen', 'London, UK', 'The Paris Romance Getaway exceeded all expectations. The Eiffel Tower at night, the Seine cruise - pure perfection. WanderLux made our anniversary truly unforgettable.', 5, 1),
('Emma Williams', 'Sydney, Australia', 'Santorini Sunset Paradise was everything and more. The caldera views from our hotel were breathtaking. The sunset in Oia literally brought tears to my eyes!', 5, 1),
('David Rodriguez', 'Toronto, Canada', 'The Kenya Safari Adventure was life-changing. Seeing lions, elephants and the wildebeest migration up close was surreal. The lodge was luxurious and the guides exceptional.', 5, 1),
('Priya Patel', 'Mumbai, India', 'Maldives Overwater Paradise - worth every penny! Waking up over the turquoise water, snorkeling with manta rays... this is what dreams are made of. Highly recommend!', 5, 1),
('James Wilson', 'Chicago, USA', 'The Tokyo Modern & Ancient tour perfectly balanced tradition and modernity. The tea ceremony was a highlight. WanderLux team was incredibly professional and helpful.', 4, 1);

-- Gallery images
INSERT INTO `gallery` (`destination_id`, `title`, `image_url`) VALUES
(1, 'Bali Rice Terraces', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&q=80'),
(1, 'Bali Temple', 'https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=600&q=80'),
(2, 'Eiffel Tower', 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=600&q=80'),
(2, 'Paris Streets', 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?w=600&q=80'),
(3, 'Santorini Blue Domes', 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=600&q=80'),
(4, 'Tokyo Skyline', 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=600&q=80'),
(5, 'Maldives Bungalows', 'https://images.unsplash.com/photo-1573843981267-be1999ff37cd?w=600&q=80'),
(6, 'NYC Times Square', 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=600&q=80'),
(7, 'Kenya Wildlife', 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?w=600&q=80'),
(8, 'Machu Picchu', 'https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=600&q=80'),
(NULL, 'Mountain Trek', 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=600&q=80'),
(NULL, 'Ocean Sunset', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80');
