<?php
require_once 'config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { header('Location: packages.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM packages WHERE id = ? AND is_active = 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$pkg = $stmt->get_result()->fetch_assoc();

if (!$pkg) { header('Location: packages.php'); exit; }

$discount = $pkg['original_price'] ? round((($pkg['original_price'] - $pkg['price']) / $pkg['original_price']) * 100) : 0;

// Related packages
$related = $conn->query("SELECT * FROM packages WHERE category = '{$pkg['category']}' AND id != $id AND is_active = 1 LIMIT 3");

// Parse includes/excludes
$includes = array_filter(array_map('trim', explode(',', $pkg['includes'] ?? '')));
$excludes = array_filter(array_map('trim', explode(',', $pkg['excludes'] ?? '')));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pkg['name']) ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar scrolled" id="navbar">
    <div class="navbar-inner">
        <a href="index.php" class="logo">Wander<span>World</span></a>
        <ul class="nav-links" id="navLinks">
            <li><a href="index.php">Home</a></li>
            <li><a href="packages.php" class="active">Packages</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="booking.php?id=<?= $id ?>" class="nav-btn">Book Now</a></li>
        </ul>
        <div class="menu-toggle" id="menuToggle"><span></span><span></span><span></span></div>
    </div>
</nav>

<!-- PAGE HERO -->
<div class="page-hero" style="background:linear-gradient(135deg,rgba(10,30,50,0.8),rgba(10,124,110,0.7)),url('<?= htmlspecialchars($pkg['image_url']) ?>')center/cover no-repeat;">
    <div class="hero-badge"><?= $pkg['category'] ?> Tour</div>
    <h1><?= htmlspecialchars($pkg['name']) ?></h1>
    <p>&#128205; <?= htmlspecialchars($pkg['destination']) ?></p>
    <div class="breadcrumb">
        <a href="index.php">Home</a><span>/</span>
        <a href="packages.php">Packages</a><span>/</span>
        <span><?= htmlspecialchars($pkg['name']) ?></span>
    </div>
</div>

<!-- PACKAGE DETAIL -->
<section class="package-detail-section">
    <div class="detail-grid">
        <!-- LEFT: Main Content -->
        <div class="detail-main">
            <img src="<?= htmlspecialchars($pkg['image_url']) ?>" alt="<?= htmlspecialchars($pkg['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=1200&q=80'">

            <!-- Meta Bar -->
            <div class="detail-meta-bar">
                <div class="meta-item"><span>&#128197;</span><div><span>Duration</span><br><strong><?= $pkg['duration'] ?> Days</strong></div></div>
                <div class="meta-item"><span>&#128101;</span><div><span>Group Size</span><br><strong>Max <?= $pkg['max_people'] ?></strong></div></div>
                <div class="meta-item"><span>&#127959;</span><div><span>Category</span><br><strong><?= $pkg['category'] ?></strong></div></div>
                <div class="meta-item"><span>&#11088;</span><div><span>Rating</span><br><strong><?= $pkg['rating'] ?>/5 (<?= $pkg['reviews_count'] ?> reviews)</strong></div></div>
                <?php if ($discount > 0): ?>
                <div class="meta-item"><span>&#127991;</span><div><span>Discount</span><br><strong style="color:#e74c3c;"><?= $discount ?>% OFF</strong></div></div>
                <?php endif; ?>
            </div>

            <!-- Description -->
            <h3 class="detail-section-title">About This Tour</h3>
            <p class="detail-description"><?= nl2br(htmlspecialchars($pkg['description'])) ?></p>

            <!-- What's Included -->
            <h3 class="detail-section-title">What's Included &amp; Excluded</h3>
            <div class="includes-grid">
                <?php foreach ($includes as $inc): ?>
                <div class="include-item yes"><?= htmlspecialchars(trim($inc)) ?></div>
                <?php endforeach; ?>
                <?php foreach ($excludes as $exc): ?>
                <div class="include-item no"><?= htmlspecialchars(trim($exc)) ?></div>
                <?php endforeach; ?>
            </div>

            <!-- Highlights -->
            <h3 class="detail-section-title">Tour Highlights</h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin-bottom:30px;">
                <?php
                $highlights = [
                    'Expert local guide included',
                    'All entry fees covered',
                    'Comfortable accommodation',
                    'Airport pick-up & drop',
                    'Meals as per itinerary',
                    'Sightseeing tours'
                ];
                foreach ($highlights as $h):
                ?>
                <div style="display:flex;align-items:center;gap:8px;padding:12px;background:var(--light-gray);border-radius:8px;font-size:0.88rem;">
                    <span style="color:var(--primary);font-size:1rem;">&#10003;</span>
                    <?= $h ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Important Notes -->
            <div class="alert alert-info">
                <strong>&#128221; Important Notes:</strong> Prices are per person based on double sharing. Airfare subject to availability. Please carry valid photo ID. Tour operates subject to minimum 4 passengers.
            </div>
        </div>

        <!-- RIGHT: Booking Sidebar -->
        <div class="booking-sidebar">
            <div class="sidebar-price-header">
                <?php if ($pkg['original_price']): ?>
                    <div class="old">&#8377;<?= number_format($pkg['original_price']) ?> per person</div>
                <?php endif; ?>
                <div class="price">&#8377;<?= number_format($pkg['price']) ?></div>
                <div class="per">per person</div>
                <?php if ($discount > 0): ?>
                    <div style="background:rgba(231,76,60,0.3);color:#ff8a80;border-radius:20px;padding:4px 12px;font-size:0.82rem;font-weight:700;margin-top:8px;display:inline-block;">Save <?= $discount ?>% Today!</div>
                <?php endif; ?>
            </div>
            <div class="sidebar-body">
                <ul class="sidebar-features">
                    <li><?= $pkg['duration'] ?> Days / <?= ($pkg['duration']-1) ?> Nights</li>
                    <li>Group size: max <?= $pkg['max_people'] ?> people</li>
                    <li>Expert guide included</li>
                    <li>Instant booking confirmation</li>
                    <li>Free cancellation (7+ days prior)</li>
                    <li>24/7 support during tour</li>
                </ul>

                <a href="booking.php?id=<?= $pkg['id'] ?>" class="book-btn">
                    &#128197; Book This Package
                </a>

                <div style="text-align:center;margin-top:14px;">
                    <a href="contact.php?inquiry=<?= $pkg['id'] ?>" style="color:var(--primary);font-size:0.88rem;font-weight:600;">
                        &#128172; Have a question? Ask us
                    </a>
                </div>

                <div style="border-top:1px solid #eee;margin-top:20px;padding-top:16px;text-align:center;">
                    <p style="font-size:0.8rem;color:#999;">&#128274; Secure booking &nbsp;|&nbsp; &#128179; No hidden fees</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- RELATED PACKAGES -->
<?php if ($related->num_rows > 0): ?>
<section class="section section-alt">
    <div class="section-header">
        <span class="section-tag">More <?= $pkg['category'] ?> Tours</span>
        <h2>You Might Also Like</h2>
        <div class="divider"></div>
    </div>
    <div class="packages-grid">
        <?php while ($rel = $related->fetch_assoc()):
            $rel_discount = $rel['original_price'] ? round((($rel['original_price'] - $rel['price']) / $rel['original_price']) * 100) : 0;
        ?>
        <div class="package-card">
            <div class="card-img-wrap">
                <img src="<?= htmlspecialchars($rel['image_url']) ?>" alt="<?= htmlspecialchars($rel['name']) ?>" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=800&q=80'">
                <span class="card-badge <?= strtolower($rel['category']) ?>"><?= $rel['category'] ?></span>
                <?php if ($rel_discount > 0): ?><span class="card-discount">-<?= $rel_discount ?>% OFF</span><?php endif; ?>
            </div>
            <div class="card-body">
                <div class="card-destination"><?= htmlspecialchars($rel['destination']) ?></div>
                <h3><?= htmlspecialchars($rel['name']) ?></h3>
                <div class="card-meta">
                    <span>&#128197; <?= $rel['duration'] ?> Days</span>
                    <span>&#11088; <?= $rel['rating'] ?></span>
                </div>
            </div>
            <div class="card-footer">
                <div class="price-wrap">
                    <span class="price">&#8377;<?= number_format($rel['price']) ?></span>
                    <span class="per">/person</span>
                </div>
                <div class="card-actions">
                    <a href="package_detail.php?id=<?= $rel['id'] ?>" class="btn-sm outline">Details</a>
                    <a href="booking.php?id=<?= $rel['id'] ?>" class="btn-sm accent">Book</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>
<?php endif; ?>

<!-- FOOTER -->
<footer>
    <div class="footer-grid">
        <div class="footer-brand">
            <span class="logo">Wander<span>World</span></span>
            <p>Creating extraordinary travel experiences since 2015.</p>
            <div class="social-links"><a href="#">f</a><a href="#">in</a><a href="#">tw</a><a href="#">yt</a></div>
        </div>
        <div class="footer-col"><h4>Quick Links</h4><ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="packages.php">All Packages</a></li>
            <li><a href="contact.php">Contact Us</a></li>
        </ul></div>
        <div class="footer-col"><h4>Categories</h4><ul>
            <?php foreach (['Beach','Adventure','Cultural','Wildlife','Mountain','Luxury'] as $c): ?>
            <li><a href="packages.php?category=<?= $c ?>"><?= $c ?></a></li>
            <?php endforeach; ?>
        </ul></div>
        <div class="footer-col"><h4>Contact</h4><ul>
            <li><a href="mailto:info@wanderworld.com">info@wanderworld.com</a></li>
            <li><a href="tel:+911800123456">+91 1800-123-456</a></li>
        </ul></div>
    </div>
    <div class="footer-bottom">
        <span>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</span>
        <span>Made with &#10084; for curious travelers</span>
    </div>
</footer>

<button class="scroll-top" id="scrollTop" title="Back to top" onclick="window.scrollTo({top:0,behavior:'smooth'})">&#8679;</button>

<script>
const menuToggle = document.getElementById('menuToggle');
const navLinks = document.getElementById('navLinks');
menuToggle.addEventListener('click', () => {
    navLinks.classList.toggle('open');
    menuToggle.classList.toggle('open');
});
window.addEventListener('scroll', () => {
    document.getElementById('scrollTop').classList.toggle('visible', window.scrollY > 400);
});
</script>
</body>
</html>
