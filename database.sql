-- WanderWorld Travels Database
CREATE DATABASE IF NOT EXISTS claud_project CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE claud_project;

-- Packages Table
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    destination VARCHAR(200) NOT NULL,
    category ENUM('Beach','Adventure','Cultural','Wildlife','Mountain','Luxury') NOT NULL,
    duration INT NOT NULL COMMENT 'Days',
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2) DEFAULT NULL,
    max_people INT DEFAULT 20,
    image_url VARCHAR(500),
    gallery TEXT COMMENT 'JSON array of image URLs',
    short_desc TEXT,
    description LONGTEXT,
    itinerary LONGTEXT COMMENT 'JSON array of day-wise itinerary',
    includes TEXT COMMENT 'What is included',
    excludes TEXT COMMENT 'What is excluded',
    rating DECIMAL(2,1) DEFAULT 4.5,
    reviews_count INT DEFAULT 0,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_ref VARCHAR(20) UNIQUE NOT NULL,
    package_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(200) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    travel_date DATE NOT NULL,
    num_adults INT NOT NULL DEFAULT 1,
    num_children INT DEFAULT 0,
    special_requests TEXT,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('Pending','Confirmed','Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE
);

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(200) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(300),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Sample Packages
INSERT INTO packages (name, destination, category, duration, price, original_price, max_people, image_url, short_desc, description, includes, excludes, rating, reviews_count, is_featured) VALUES

('Maldives Paradise Escape', 'Maldives', 'Beach', 7, 85000.00, 99000.00, 10,
'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=800&q=80',
'Crystal clear waters, overwater bungalows and white sandy beaches await you.',
'Experience the ultimate tropical paradise in the Maldives. Stay in luxurious overwater bungalows surrounded by the turquoise Indian Ocean. Enjoy world-class snorkeling, diving, and water sports. Indulge in fresh seafood and breathtaking sunset views every evening.',
'Return flights, 6 nights overwater bungalow, All meals (breakfast/lunch/dinner), Airport transfers, Snorkeling equipment, Guided reef tour, Sunset cruise',
'Travel insurance, Personal expenses, Alcoholic beverages, Optional water sports',
4.9, 128, 1),

('Bali Cultural Journey', 'Bali, Indonesia', 'Cultural', 6, 45000.00, 52000.00, 15,
'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800&q=80',
'Discover ancient temples, lush rice terraces, and vibrant Balinese culture.',
'Immerse yourself in the magical island of Bali. Visit ancient Hindu temples perched on clifftops, walk through emerald green rice terraces, and witness traditional Kecak fire dance performances. Explore art villages and learn traditional crafts from local artisans.',
'Return flights, 5 nights hotel (3-star), Daily breakfast, All temple entrance fees, Guided tours, Cultural show tickets, Airport transfers',
'Lunch and dinner, Travel insurance, Personal shopping, Tips',
4.7, 94, 1),

('Swiss Alps Adventure', 'Switzerland', 'Mountain', 8, 120000.00, 135000.00, 12,
'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&q=80',
'Snow-capped peaks, scenic train rides and charming alpine villages.',
'Conquer the majestic Swiss Alps on this thrilling adventure. Take the famous Glacier Express, visit Jungfraujoch - the Top of Europe, ski on world-class slopes, and stay in cozy alpine chalets. Experience the pristine natural beauty of Switzerland in all its glory.',
'Return flights, 7 nights chalet accommodation, Daily breakfast, Swiss Travel Pass (unlimited trains/buses/boats), Guided glacier tour, Cable car passes',
'Ski equipment rental, Ski lessons, Dinner and lunch, Travel insurance',
4.8, 76, 1),

('Rajasthan Royal Tour', 'Rajasthan, India', 'Cultural', 9, 32000.00, 38000.00, 20,
'https://images.unsplash.com/photo-1599661046289-e31897846e41?w=800&q=80',
'Majestic forts, colorful bazaars and the magic of the golden desert.',
'Journey through the land of kings and discover the royal heritage of Rajasthan. Visit the magnificent forts of Jaipur, Jodhpur, and Udaipur. Ride camels in the Thar Desert, witness the magical sunset over sand dunes, and experience a traditional folk dance evening.',
'AC bus/train transport, 8 nights hotel (3-star), Daily breakfast, All monument entry fees, Camel safari, Folk dance evening, Local guide',
'Flights to/from Jaipur, Lunch and dinner, Shopping, Travel insurance',
4.6, 210, 1),

('Amazon Rainforest Expedition', 'Brazil', 'Adventure', 10, 95000.00, NULL, 8,
'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?w=800&q=80',
'Explore the world\'s largest tropical rainforest and its incredible biodiversity.',
'Venture deep into the Amazon rainforest on this once-in-a-lifetime expedition. Trek through dense jungle paths, spot exotic wildlife including pink dolphins, macaws and anacondas. Stay in eco-lodges built in the treetops and learn from indigenous communities.',
'Return flights to Manaus, 9 nights eco-lodge, All meals, Expert naturalist guide, River boat tours, Wildlife watching, Jungle treks, Piranha fishing',
'Travel insurance, Vaccinations, Personal gear, Tips',
4.8, 45, 1),

('Santorini Luxury Retreat', 'Greece', 'Luxury', 7, 110000.00, 125000.00, 8,
'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=800&q=80',
'Iconic blue-domed churches, cliff-top infinity pools and Mediterranean sunsets.',
'Escape to the most romantic island in the world. Stay in a luxurious cave house with private infinity pool overlooking the caldera. Explore the charming villages of Oia and Fira, taste exquisite local wines, and cruise around the volcanic islands.',
'Return flights, 6 nights luxury cave hotel, Daily breakfast, Wine tasting tour, Catamaran sunset cruise, Guided village tours, Airport transfers',
'Dinner and lunch, Personal expenses, Travel insurance',
4.9, 67, 0),

('Kenya Safari Adventure', 'Kenya, Africa', 'Wildlife', 8, 130000.00, 150000.00, 10,
'https://images.unsplash.com/photo-1547471080-7cc2caa01a7e?w=800&q=80',
'Witness the Great Migration and encounter the Big Five in their natural habitat.',
'Experience the ultimate African safari in Kenya\'s iconic national parks. Witness thousands of wildebeest crossing the Mara River, track lions, elephants, and leopards on game drives, and spend magical nights under the stars in luxury tented camps.',
'Return flights, 7 nights luxury tented camps, All meals, Daily game drives, Park fees, Expert safari guide, Maasai village visit, Hot air balloon ride',
'Travel insurance, Personal expenses, Tips, Vaccinations',
4.9, 89, 1),

('Thailand Beach & Culture', 'Thailand', 'Beach', 10, 55000.00, 65000.00, 20,
'https://images.unsplash.com/photo-1552465011-b4e21bf6e79a?w=800&q=80',
'Golden temples, street food paradise, and pristine island beaches.',
'Discover the best of Thailand from the bustling streets of Bangkok to the serene beaches of Phuket and Koh Samui. Visit ornate temples, take a traditional longtail boat through floating markets, feast on incredible street food, and relax on some of Asia\'s most beautiful beaches.',
'Return flights, 9 nights hotels, Daily breakfast, Bangkok city tour, Temple tours, Island hopping, Airport transfers, Travel sim card',
'Lunch and dinner, Personal expenses, Travel insurance, Optional activities',
4.7, 156, 0),

('Himalayan Trekking Expedition', 'Nepal', 'Adventure', 14, 75000.00, NULL, 12,
'https://images.unsplash.com/photo-1533130061792-64b345e4a833?w=800&q=80',
'Trek to Everest Base Camp through breathtaking Himalayan scenery.',
'Embark on the ultimate trekking adventure to Everest Base Camp. Trek through stunning Sherpa villages, ancient monasteries and high-altitude valleys. Stand at 5,364m and gaze up at the world\'s highest peak. Experience warm Nepali hospitality at every teahouse along the route.',
'Kathmandu-Lukla flights, 13 nights teahouse/guesthouse, All meals on trek, Experienced trekking guide, Porters, Sagarmatha National Park permit, TIMS card',
'Travel insurance, International flights, Sleeping bag, Personal gear, Tips',
4.8, 52, 0),

('Japan Cherry Blossom Tour', 'Japan', 'Cultural', 11, 145000.00, 160000.00, 15,
'https://images.unsplash.com/photo-1545569341-9eb8b30979d9?w=800&q=80',
'Experience Japan in full bloom — sakura season magic across Tokyo, Kyoto and Osaka.',
'Visit Japan during the magical cherry blossom season. Witness stunning sakura in famous parks, explore ancient temples and shrines in Kyoto, experience the futuristic energy of Tokyo, and taste incredible Japanese cuisine from sushi to ramen.',
'Return flights, 10 nights hotels (mix of traditional ryokan and modern), Daily breakfast, JR Pass (bullet trains), Tea ceremony, Temple tours, Sumo show tickets',
'Dinner and lunch, Personal shopping, Travel insurance',
4.9, 73, 1),

('Dubai Desert & City Glamour', 'Dubai, UAE', 'Luxury', 5, 60000.00, 72000.00, 20,
'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&q=80',
'Soaring skyscrapers, desert safaris, and world-class luxury in the City of Gold.',
'Experience the ultimate luxury destination. Stay in iconic 5-star hotels, visit the Burj Khalifa, shop in the world\'s largest malls, and experience a thrilling desert safari with dune bashing and camel riding. Dubai offers an unmatched blend of modern luxury and Arabian heritage.',
'Return flights, 4 nights 5-star hotel, Daily breakfast, Desert safari with BBQ dinner, City tour, Burj Khalifa tickets, Dubai Frame, Airport transfers',
'Lunch, Personal shopping, Travel insurance, Optional activities',
4.7, 134, 0),

('Patagonia End of the World', 'Argentina & Chile', 'Adventure', 12, 185000.00, NULL, 8,
'https://images.unsplash.com/photo-1501854140801-50d01698950b?w=800&q=80',
'Trek through pristine wilderness at the southern tip of South America.',
'Journey to the end of the world and discover the raw, untamed beauty of Patagonia. Trek in Torres del Paine National Park, witness massive glaciers calving into turquoise lakes, spot penguins and condors, and marvel at the dramatic Fitz Roy mountain range.',
'Return flights to Buenos Aires + internal flights, 11 nights hotels/lodges, Daily breakfast, Park fees, Guided glacier walk, Expert naturalist guide, Boat tours',
'Trekking gear, Travel insurance, Most meals, Personal expenses',
4.8, 38, 0);

-- Insert Sample Bookings
INSERT INTO bookings (booking_ref, package_id, first_name, last_name, email, phone, travel_date, num_adults, num_children, total_price, status) VALUES
('WW-2024-001', 1, 'Rahul', 'Sharma', 'rahul@example.com', '9876543210', '2024-12-15', 2, 0, 170000.00, 'Confirmed'),
('WW-2024-002', 4, 'Priya', 'Patel', 'priya@example.com', '9876543211', '2024-11-20', 4, 2, 160000.00, 'Confirmed'),
('WW-2024-003', 7, 'Amit', 'Kumar', 'amit@example.com', '9876543212', '2025-01-10', 2, 0, 260000.00, 'Pending');
