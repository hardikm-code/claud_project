<?php
require_once 'config.php';

// Fetch featured packages
$featured = $conn->query("SELECT * FROM packages WHERE is_featured = 1 AND is_active = 1 ORDER BY rating DESC LIMIT 6");

// Fetch stats
$total_packages = $conn->query("SELECT COUNT(*) as c FROM packages")->fetch_assoc()['c'];
$total_bookings  = $conn->query("SELECT COUNT(*) as c FROM bookings")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Explore The World</title>
    <link rel="stylesheet" href="style.css">
    <meta name="description" content="WanderWorld Travels - Handcrafted travel experiences across the globe. Explore tour packages for beaches, mountains, wildlife safaris and more.">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
    <div class="navbar-inner">
        <a href="index.php" class="logo">Wander<span>World</span></a>
        <ul class="nav-links" id="navLinks">
            <li><a href="index.php" class="active">Home</a></li>
            <li><a href="packages.php">Packages</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="booking.php" class="nav-btn">Book Now</a></li>
        </ul>
        <div class="menu-toggle" id="menuToggle">
            <span></span><span></span><span></span>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-badge">&#9992; Trusted Travel Partner Since 2015</div>
        <h1>Discover Your Next <span>Dream Destination</span></h1>
        <p>Handcrafted travel experiences across the globe. From tropical beaches to icy mountain peaks &mdash; your perfect journey awaits.</p>
        <div class="hero-btns">
            <a href="packages.php" class="btn-accent">Explore Packages</a>
            <a href="contact.php" class="btn-outline">Talk to an Expert</a>
        </div>
    </div>
</section>

<!-- SEARCH BAR -->
<div class="search-section">
    <form class="search-form" action="packages.php" method="GET">
        <select name="category">
            <option value="">All Categories</option>
            <option value="Beach">Beach</option>
            <option value="Adventure">Adventure</option>
            <option value="Cultural">Cultural</option>
            <option value="Wildlife">Wildlife</option>
            <option value="Mountain">Mountain</option>
            <option value="Luxury">Luxury</option>
        </select>
        <input type="text" name="destination" placeholder="Where do you want to go?">
        <select name="duration">
            <option value="">Any Duration</option>
            <option value="7">Up to 7 Days</option>
            <option value="10">Up to 10 Days</option>
            <option value="14">Up to 14 Days</option>
        </select>
        <select name="budget">
            <option value="">Any Budget</option>
            <option value="50000">Under &#8377;50,000</option>
            <option value="100000">Under &#8377;1,00,000</option>
            <option value="150000">Under &#8377;1,50,000</option>
        </select>
        <button type="submit">&#128269; Search</button>
    </form>
</div>

<!-- STATS BAR -->
<div class="stats-bar">
    <div class="stat-item">
        <div class="num"><?= $total_packages ?>+</div>
        <div class="label">Tour Packages</div>
    </div>
    <div class="stat-item">
        <div class="num">50+</div>
        <div class="label">Destinations</div>
    </div>
    <div class="stat-item">
        <div class="num"><?= $total_bookings + 2500 ?>+</div>
        <div class="label">Happy Travelers</div>
    </div>
    <div class="stat-item">
        <div class="num">10+</div>
        <div class="label">Years Experience</div>
    </div>
    <div class="stat-item">
        <div class="num">4.8 &#11088;</div>
        <div class="label">Average Rating</div>
    </div>
</div>

<!-- FEATURED PACKAGES -->
<section class="section">
    <div class="section-header">
        <span class="section-tag">Our Best Picks</span>
        <h2>Featured Tour Packages</h2>
        <p>Discover our most popular handcrafted travel experiences, loved by thousands of travellers.</p>
        <div class="divider"></div>
    </div>

    <div class="packages-grid">
        <?php while ($pkg = $featured->fetch_assoc()):
            $discount = $pkg['original_price'] ? round((($pkg['original_price'] - $pkg['price']) / $pkg['original_price']) * 100) : 0;
            $cat_lower = strtolower($pkg['category']);
        ?>
        <div class="package-card">
            <div class="card-img-wrap">
                <img src="<?= htmlspecialchars($pkg['image_url']) ?>" alt="<?= htmlspecialchars($pkg['name']) ?>" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=800&q=80'">
                <span class="card-badge <?= $cat_lower ?>"><?= $pkg['category'] ?></span>
                <?php if ($discount > 0): ?>
                    <span class="card-discount">-<?= $discount ?>% OFF</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="card-destination"><?= htmlspecialchars($pkg['destination']) ?></div>
                <h3><?= htmlspecialchars($pkg['name']) ?></h3>
                <p class="card-desc"><?= htmlspecialchars(substr($pkg['short_desc'], 0, 90)) ?>...</p>
                <div class="card-meta">
                    <span>&#128197; <?= $pkg['duration'] ?> Days</span>
                    <span>&#128101; Max <?= $pkg['max_people'] ?> People</span>
                </div>
                <div class="card-rating">
                    <span class="stars"><?= str_repeat('&#9733;', floor($pkg['rating'])) ?></span>
                    <span class="rating-num"><?= $pkg['rating'] ?></span>
                    <span class="rating-count">(<?= $pkg['reviews_count'] ?> reviews)</span>
                </div>
            </div>
            <div class="card-footer">
                <div class="price-wrap">
                    <?php if ($pkg['original_price']): ?>
                        <span class="old-price">&#8377;<?= number_format($pkg['original_price']) ?></span>
                    <?php endif; ?>
                    <span class="price">&#8377;<?= number_format($pkg['price']) ?></span>
                    <span class="per">/person</span>
                </div>
                <div class="card-actions">
                    <a href="package_detail.php?id=<?= $pkg['id'] ?>" class="btn-sm outline">Details</a>
                    <a href="booking.php?id=<?= $pkg['id'] ?>" class="btn-sm accent">Book Now</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <div style="text-align:center; margin-top:40px;">
        <a href="packages.php" class="btn-primary">View All Packages &rarr;</a>
    </div>
</section>

<!-- WHY CHOOSE US -->
<section class="section section-alt">
    <div class="section-header">
        <span class="section-tag">Why Travel With Us</span>
        <h2>The WanderWorld Difference</h2>
        <p>We go beyond booking flights and hotels. We craft memories that last a lifetime.</p>
        <div class="divider"></div>
    </div>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">&#127758;</div>
            <h3>50+ Destinations</h3>
            <p>From exotic islands to snow-capped mountains, we cover all corners of the globe.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">&#128176;</div>
            <h3>Best Price Guarantee</h3>
            <p>We promise the best value for money with no hidden fees or surprise charges.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">&#128737;</div>
            <h3>Fully Insured Travel</h3>
            <p>All our packages come with comprehensive travel insurance for complete peace of mind.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">&#128084;</div>
            <h3>Expert Guides</h3>
            <p>Our experienced local guides ensure you get an authentic, immersive experience.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">&#128222;</div>
            <h3>24/7 Support</h3>
            <p>Round-the-clock customer support so you are never alone on your journey.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">&#9999;</div>
            <h3>Custom Itineraries</h3>
            <p>Can't find your perfect trip? We'll design a bespoke itinerary just for you.</p>
        </div>
    </div>
</section>

<!-- POPULAR DESTINATIONS -->
<section class="section">
    <div class="section-header">
        <span class="section-tag">Top Picks</span>
        <h2>Popular Destinations</h2>
        <div class="divider"></div>
    </div>
    <div class="destinations-grid">
        <?php
        $destinations = [
            ['name'=>'Maldives','icon'=>'&#127965;','tag'=>'Beach Paradise'],
            ['name'=>'Switzerland','icon'=>'&#9968;','tag'=>'Mountain Magic'],
            ['name'=>'Japan','icon'=>'&#127800;','tag'=>'Cultural Gem'],
            ['name'=>'Kenya','icon'=>'&#129409;','tag'=>'Wildlife Safari'],
            ['name'=>'Santorini','icon'=>'&#127963;','tag'=>'Romantic Escape'],
            ['name'=>'Bali','icon'=>'&#127796;','tag'=>'Tropical Bliss'],
        ];
        foreach ($destinations as $dest):
        ?>
        <a href="packages.php?destination=<?= urlencode($dest['name']) ?>" class="dest-card">
            <span class="dest-icon"><?= $dest['icon'] ?></span>
            <span class="dest-name"><?= $dest['name'] ?></span>
            <span class="dest-tag"><?= $dest['tag'] ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="section section-alt">
    <div class="section-header">
        <span class="section-tag">Customer Stories</span>
        <h2>What Our Travelers Say</h2>
        <div class="divider"></div>
    </div>
    <div class="testimonials-grid">
        <div class="testimonial-card">
            <p class="testimonial-text">The Maldives trip was absolutely magical! Every detail was perfectly arranged &mdash; from the overwater bungalow to the sunset cruise. WanderWorld made our honeymoon unforgettable!</p>
            <div class="testimonial-author">
                <div class="author-avatar">RS</div>
                <div>
                    <div class="author-name">Rohit &amp; Sneha</div>
                    <div class="author-trip">&#11088;&#11088;&#11088;&#11088;&#11088; Maldives Honeymoon</div>
                </div>
            </div>
        </div>
        <div class="testimonial-card">
            <p class="testimonial-text">Our family Safari in Kenya was beyond our wildest dreams. We saw all the Big Five on the very first day! The guide was knowledgeable and the tented camp was luxurious.</p>
            <div class="testimonial-author">
                <div class="author-avatar">AP</div>
                <div>
                    <div class="author-name">Arjun &amp; Family</div>
                    <div class="author-trip">&#11088;&#11088;&#11088;&#11088;&#11088; Kenya Safari</div>
                </div>
            </div>
        </div>
        <div class="testimonial-card">
            <p class="testimonial-text">Japan during cherry blossom season was like being in a dream. WanderWorld's itinerary was perfectly paced with a wonderful mix of culture, food and natural beauty.</p>
            <div class="testimonial-author">
                <div class="author-avatar">MK</div>
                <div>
                    <div class="author-name">Meera Krishnan</div>
                    <div class="author-trip">&#11088;&#11088;&#11088;&#11088;&#11088; Japan Sakura Tour</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- NEWSLETTER -->
<div class="newsletter-section">
    <div class="newsletter-content">
        <h2>Get Exclusive Travel Deals</h2>
        <p>Subscribe to our newsletter and receive special offers, travel tips, and early bird discounts.</p>
        <form class="newsletter-form" onsubmit="handleNewsletter(event)">
            <input type="email" placeholder="Enter your email address" required>
            <button type="submit">Subscribe &#9993;</button>
        </form>
    </div>
</div>

<!-- FLOATING BUTTONS -->
<a href="https://wa.me/911800123456" class="whatsapp-btn" title="Chat on WhatsApp" target="_blank" rel="noopener">&#128172;</a>
<button class="scroll-top" id="scrollTop" title="Back to top" onclick="window.scrollTo({top:0,behavior:'smooth'})">&#8679;</button>

<!-- FOOTER -->
<footer>
    <div class="footer-grid">
        <div class="footer-brand">
            <span class="logo">Wander<span>World</span></span>
            <p>Creating extraordinary travel experiences since 2015. Let us take you on a journey you'll never forget.</p>
            <div class="social-links">
                <a href="#">f</a><a href="#">in</a><a href="#">tw</a><a href="#">yt</a>
            </div>
        </div>
        <div class="footer-col">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="packages.php">All Packages</a></li>
                <li><a href="packages.php?category=Beach">Beach Tours</a></li>
                <li><a href="packages.php?category=Adventure">Adventure</a></li>
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Destinations</h4>
            <ul>
                <li><a href="packages.php?destination=Maldives">Maldives</a></li>
                <li><a href="packages.php?destination=Bali">Bali</a></li>
                <li><a href="packages.php?destination=Switzerland">Switzerland</a></li>
                <li><a href="packages.php?destination=Japan">Japan</a></li>
                <li><a href="packages.php?destination=Kenya">Kenya</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Contact</h4>
            <ul>
                <li><a href="mailto:info@wanderworld.com">info@wanderworld.com</a></li>
                <li><a href="tel:+911800123456">+91 1800-123-456</a></li>
                <li><a href="#">Mumbai, India</a></li>
                <li><a href="#">Mon&ndash;Sat: 9am &ndash; 7pm</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <span>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</span>
        <span>Made with &#10084; for curious travelers</span>
    </div>
</footer>

<script>
const navbar = document.getElementById('navbar');
const scrollTopBtn = document.getElementById('scrollTop');
const menuToggle = document.getElementById('menuToggle');
const navLinks = document.getElementById('navLinks');

window.addEventListener('scroll', () => {
    const scrolled = window.scrollY > 50;
    navbar.classList.toggle('scrolled', scrolled);
    scrollTopBtn.classList.toggle('visible', window.scrollY > 400);
});

menuToggle.addEventListener('click', () => {
    navLinks.classList.toggle('open');
    menuToggle.classList.toggle('open');
});

// Close menu when a link is clicked
navLinks.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
        navLinks.classList.remove('open');
        menuToggle.classList.remove('open');
    });
});

function handleNewsletter(e) {
    e.preventDefault();
    e.target.innerHTML = '<p style="color:white;font-weight:700;font-size:1.1rem;">&#10003; Thank you for subscribing! Check your inbox soon.</p>';
}
</script>
</body>
</html>
