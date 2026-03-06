<?php require_once 'config.php'; ?>
<?php
// Fetch 3 featured menu items (chef's picks)
$featured = [];
try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT mi.*, mc.name AS category FROM menu_items mi JOIN menu_categories mc ON mi.category_id = mc.id WHERE mi.badge IN ('Chef\\'s Pick','Signature','Must Try') AND mi.is_available = 1 ORDER BY RAND() LIMIT 3");
    $featured = $stmt->fetchAll();
} catch (Exception $e) {}

$emojis = ['🍝','🥩','🍤','🥗','🍷','🍮','🥂','🫕'];
function randomEmoji($emojis) { return $emojis[array_rand($emojis)]; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> — Authentic Italian Cuisine</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="nav-inner">
        <a class="nav-logo" href="index.php">La Bella <span>Cucina</span></a>
        <ul class="nav-links">
            <li><a href="index.php" class="active">Home</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="reservations.php">Reservations</a></li>
            <li><a href="reservations.php" class="nav-reserve">Book a Table</a></li>
        </ul>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-tag">Est. 1987 &bull; Since Three Generations</div>
        <h1>A Taste of <em>Authentic Italy</em></h1>
        <p>Handcrafted recipes passed down through generations, made with the finest<br>imported ingredients — right in the heart of the city.</p>
        <div class="hero-btns">
            <a href="menu.php" class="btn btn-gold">Explore Our Menu</a>
            <a href="reservations.php" class="btn btn-outline">Reserve a Table</a>
        </div>
    </div>
</section>

<!-- FEATURES STRIP -->
<div class="features">
    <div class="features-grid">
        <div class="feature-item">
            <div class="feature-icon">🫒</div>
            <strong>Imported Ingredients</strong>
            <span>Direct from Italy every week</span>
        </div>
        <div class="feature-item">
            <div class="feature-icon">👨‍🍳</div>
            <strong>Master Chefs</strong>
            <span>Trained in Naples & Rome</span>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🍷</div>
            <strong>Curated Wine List</strong>
            <span>Over 80 Italian labels</span>
        </div>
        <div class="feature-item">
            <div class="feature-icon">⭐</div>
            <strong>Award Winning</strong>
            <span>Best Italian 2023 & 2024</span>
        </div>
    </div>
</div>

<!-- ABOUT -->
<section class="section section-light">
    <div class="container">
        <div class="about-grid">
            <div class="about-img-wrap">🍝</div>
            <div class="about-text">
                <h2>Our Story, <em>Our Passion</em></h2>
                <p>Founded in 1987 by the Conti family, La Bella Cucina began as a small trattoria with a single mission: to bring the authentic flavours of Northern Italy to every plate.</p>
                <p>Three decades later, we remain a family-run restaurant committed to the same values — fresh, seasonal produce, slow-cooked sauces, and hand-rolled pasta made every morning.</p>
                <p>Our head chef, Marco Conti, trained under his grandfather in Bologna before earning his stripes in Michelin-starred kitchens across Europe. Every dish tells a story.</p>
                <div class="about-stats">
                    <div class="stat">
                        <div class="num">37+</div>
                        <div class="label">Years Open</div>
                    </div>
                    <div class="stat">
                        <div class="num">50k+</div>
                        <div class="label">Happy Guests</div>
                    </div>
                    <div class="stat">
                        <div class="num">40+</div>
                        <div class="label">Menu Items</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED MENU -->
<section class="section section-cream">
    <div class="container">
        <div class="section-title">
            <h2>Chef's Recommendations</h2>
            <p>Handpicked favourites from our kitchen to your table</p>
        </div>
        <div class="featured-grid">
            <?php if (!empty($featured)): ?>
                <?php foreach ($featured as $item):
                    $badgeClass = 'badge-gold';
                    if ($item['badge'] === 'Popular') $badgeClass = 'badge-red';
                    if ($item['badge'] === 'Vegan')   $badgeClass = 'badge-green';
                    if ($item['badge'] === 'Signature') $badgeClass = 'badge-blue';
                ?>
                <div class="menu-card">
                    <div class="menu-card-img"><?= randomEmoji($emojis) ?></div>
                    <div class="menu-card-body">
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <p><?= htmlspecialchars(mb_substr($item['description'], 0, 100)) ?>...</p>
                        <div class="menu-card-footer">
                            <span class="price">$<?= number_format($item['price'], 2) ?></span>
                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($item['badge']) ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php
                $fallback = [
                    ['🍝', 'Tagliatelle al Ragù', 'Fresh egg tagliatelle with slow-cooked Bolognese.', '$19.00', 'Classic', 'badge-gold'],
                    ['🥩', 'Bistecca alla Fiorentina', 'Grilled T-bone steak with roasted potatoes.', '$52.00', 'Premium', 'badge-blue'],
                    ['🍮', 'Tiramisù Classico', 'Our legendary mascarpone & espresso tiramisù.', '$9.50', 'Must Try', 'badge-red'],
                ];
                foreach ($fallback as $f):
                ?>
                <div class="menu-card">
                    <div class="menu-card-img"><?= $f[0] ?></div>
                    <div class="menu-card-body">
                        <h3><?= $f[1] ?></h3>
                        <p><?= $f[2] ?></p>
                        <div class="menu-card-footer">
                            <span class="price"><?= $f[3] ?></span>
                            <span class="badge <?= $f[5] ?>"><?= $f[4] ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="text-center mt-20">
            <a href="menu.php" class="btn btn-gold">View Full Menu</a>
        </div>
    </div>
</section>

<!-- TESTIMONIALS -->
<section class="section section-light">
    <div class="container">
        <div class="section-title">
            <h2>What Our Guests Say</h2>
            <p>Stories from the tables we love to serve</p>
        </div>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <blockquote>"The tiramisù alone is worth the trip. We've been coming here every anniversary for twelve years and it never disappoints."</blockquote>
                <div class="testimonial-author">
                    <strong>Marco &amp; Elena Rossi</strong>
                    <span>Regular Guests since 2012</span>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <blockquote>"Hands down the best osso buco outside of Milan. The atmosphere is warm, the service is impeccable, and the wine list is exceptional."</blockquote>
                <div class="testimonial-author">
                    <strong>James Anderson</strong>
                    <span>Food Critic, The City Table</span>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <blockquote>"We held our daughter's wedding dinner here and everything was absolutely perfect. The team went above and beyond. Highly recommended!"</blockquote>
                <div class="testimonial-author">
                    <strong>The Chen Family</strong>
                    <span>Private Event, 2024</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HOURS -->
<section class="section" style="background:var(--dark);">
    <div class="container">
        <div class="section-title">
            <h2 style="color:#fff;">Opening Hours</h2>
            <p>We look forward to welcoming you</p>
        </div>
        <div class="hours-grid">
            <div class="hours-card">
                <h3>Lunch</h3>
                <div class="days">Tuesday – Friday</div>
                <div class="time">12:00 pm – 3:00 pm</div>
            </div>
            <div class="hours-card">
                <h3>Dinner</h3>
                <div class="days">Tuesday – Sunday</div>
                <div class="time">6:00 pm – 10:30 pm</div>
            </div>
            <div class="hours-card">
                <h3>Weekend Brunch</h3>
                <div class="days">Saturday &amp; Sunday</div>
                <div class="time">11:00 am – 3:00 pm</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<div class="cta-bar">
    <h2>Ready to Reserve Your Table?</h2>
    <p>Join us for an unforgettable dining experience. Bookings recommended.</p>
    <a href="reservations.php" class="btn btn-dark">Make a Reservation</a>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-grid">
        <div class="footer-brand">
            <h3>La Bella Cucina</h3>
            <p>Authentic Italian cuisine served with passion since 1987. Family-owned, chef-driven, and always made from scratch.</p>
        </div>
        <div>
            <h4>Navigate</h4>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="menu.php">Our Menu</a></li>
                <li><a href="reservations.php">Reservations</a></li>
            </ul>
        </div>
        <div>
            <h4>Contact</h4>
            <ul>
                <li><a href="#">123 Via Roma, City</a></li>
                <li><a href="#">+1 (555) 867-5309</a></li>
                <li><a href="#">info@labelacucina.com</a></li>
            </ul>
        </div>
        <div>
            <h4>Follow Us</h4>
            <ul>
                <li><a href="#">Instagram</a></li>
                <li><a href="#">Facebook</a></li>
                <li><a href="#">TripAdvisor</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <span>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</span>
        <span><a href="admin_login.php" style="color:#555;">Staff Login</a></span>
    </div>
</footer>

</body>
</html>
