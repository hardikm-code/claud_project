<?php
require_once 'config.php';
$pageTitle = 'Home';
$pageDesc = 'La Bella Cucina - Authentic Italian cuisine with a fine dining experience in the heart of New York.';
include 'header.php';

// Fetch featured menu items
$featured = $conn->query("
    SELECT mi.*, mc.name AS category_name
    FROM menu_items mi
    JOIN menu_categories mc ON mi.category_id = mc.id
    WHERE mi.is_featured = 1 AND mi.is_available = 1
    LIMIT 6
");

// Handle contact form
$contactMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name    = sanitize($_POST['contact_name'] ?? '');
    $email   = sanitize($_POST['contact_email'] ?? '');
    $message = sanitize($_POST['contact_message'] ?? '');

    if ($name && $email && $message && filter_var($_POST['contact_email'], FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);
        if ($stmt->execute()) {
            $contactMsg = '<div class="alert alert-success">Thank you! Your message has been sent.</div>';
        } else {
            $contactMsg = '<div class="alert alert-error">Sorry, something went wrong. Please try again.</div>';
        }
        $stmt->close();
    } else {
        $contactMsg = '<div class="alert alert-error">Please fill in all fields with a valid email address.</div>';
    }
}
?>

<!-- Hero -->
<section class="hero">
    <div class="hero-content">
        <span class="hero-eyebrow">Est. 1998 &bull; New York City</span>
        <h1>Authentic Italian<br>Fine Dining</h1>
        <p>Experience the passion, tradition, and flavors of Italy — crafted from the finest seasonal ingredients.</p>
        <div class="hero-btns">
            <a href="reservations.php" class="btn btn-primary">Reserve a Table</a>
            <a href="menu.php" class="btn btn-outline">View Our Menu</a>
        </div>
    </div>
</section>

<!-- About -->
<section class="about">
    <div class="container">
        <div class="about-grid">
            <div class="about-img">
                <img src="https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?w=800&q=80" alt="Our restaurant interior" loading="lazy">
            </div>
            <div class="about-text">
                <span class="section-eyebrow">Our Story</span>
                <h2>A Tradition of Italian Excellence</h2>
                <p>La Bella Cucina was born from a deep love for the culinary traditions of Italy. Since 1998, our family has welcomed guests into a warm, elegant dining room where every meal is a celebration.</p>
                <p>Our head chef, Marco Bianchi, trained in Rome and Florence before bringing his expertise to New York. Every recipe honors his grandmother's handwritten cookbook while embracing the finest local produce.</p>
                <p>From hand-rolled pasta to slow-braised meats and house-made gelato — every dish is a labor of love.</p>
                <div class="about-stats">
                    <div>
                        <span class="stat-num">25+</span>
                        <span class="stat-label">Years of Service</span>
                    </div>
                    <div>
                        <span class="stat-num">60+</span>
                        <span class="stat-label">Menu Items</span>
                    </div>
                    <div>
                        <span class="stat-num">4.9</span>
                        <span class="stat-label">Star Rating</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Menu -->
<section class="featured-menu">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">From Our Kitchen</span>
            <h2>Chef's Favourites</h2>
            <div class="section-divider"></div>
            <p>A selection of our most beloved dishes, prepared with seasonal ingredients and time-honoured recipes.</p>
        </div>

        <?php if ($featured && $featured->num_rows > 0): ?>
        <div class="menu-grid">
            <?php while ($item = $featured->fetch_assoc()): ?>
            <div class="menu-card">
                <div class="menu-card-body">
                    <div class="menu-card-category"><?php echo htmlspecialchars($item['category_name']); ?></div>
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                    <div class="menu-card-footer">
                        <span class="menu-price">$<?php echo number_format($item['price'], 2); ?></span>
                        <span class="badge-featured">Featured</span>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

        <div style="text-align:center; margin-top:48px;">
            <a href="menu.php" class="btn btn-primary">View Full Menu</a>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="testimonials">
    <div class="container">
        <div class="section-header">
            <span class="section-eyebrow">Guest Reviews</span>
            <h2>What Our Guests Say</h2>
            <div class="section-divider"></div>
            <p>Hear from the people who matter most — our guests.</p>
        </div>

        <div class="testimonial-grid">
            <div class="testimonial-card">
                <div class="stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p>"The Osso Buco was absolutely divine. Every bite transported me straight to Milan. The service was impeccable and the ambience is stunning."</p>
                <span class="testimonial-author">— Sarah K., New York</span>
            </div>
            <div class="testimonial-card">
                <div class="stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p>"We celebrated our anniversary here and it was perfect in every way. The tagliatelle al ragù is hands-down the best pasta I've ever had."</p>
                <span class="testimonial-author">— James & Emily T.</span>
            </div>
            <div class="testimonial-card">
                <div class="stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                <p>"Chef Marco truly cooks from the heart. The tiramisu alone is worth the trip. La Bella Cucina is now our go-to for special occasions."</p>
                <span class="testimonial-author">— Maria L., Brooklyn</span>
            </div>
        </div>
    </div>
</section>

<!-- Reservation CTA -->
<section style="background: var(--cream); padding: 80px 24px; text-align: center;">
    <div class="container">
        <span class="section-eyebrow">Join Us</span>
        <h2 style="font-size: clamp(1.8rem, 4vw, 2.8rem); margin: 16px 0;">Ready for an Unforgettable Evening?</h2>
        <div class="section-divider" style="margin-bottom: 20px;"></div>
        <p style="color: var(--gray); max-width: 500px; margin: 0 auto 40px;">Reserve your table today and let us create a memorable dining experience just for you.</p>
        <a href="reservations.php" class="btn btn-primary" style="font-size:1.05rem; padding: 16px 40px;">Make a Reservation</a>
    </div>
</section>

<!-- Contact -->
<section style="background: var(--white); padding: 80px 24px;">
    <div class="container" style="max-width: 640px;">
        <div class="section-header">
            <span class="section-eyebrow">Get In Touch</span>
            <h2>Contact Us</h2>
            <div class="section-divider"></div>
        </div>

        <?php echo $contactMsg; ?>

        <div class="form-card">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Your Name</label>
                        <input type="text" name="contact_name" placeholder="John Smith" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="contact_email" placeholder="john@example.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="contact_message" placeholder="How can we help you?" required></textarea>
                </div>
                <button type="submit" name="contact_submit" class="btn btn-primary" style="width:100%;">Send Message</button>
            </form>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
