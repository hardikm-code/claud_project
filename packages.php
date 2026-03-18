<?php
require_once 'config.php';

// Filters
$category    = isset($_GET['category'])    ? $conn->real_escape_string($_GET['category'])    : '';
$destination = isset($_GET['destination']) ? $conn->real_escape_string($_GET['destination']) : '';
$duration    = isset($_GET['duration'])    ? intval($_GET['duration'])    : 0;
$budget      = isset($_GET['budget'])      ? intval($_GET['budget'])      : 0;
$sort        = isset($_GET['sort'])        ? $conn->real_escape_string($_GET['sort']) : 'rating';

$where = ["is_active = 1"];
if ($category)    $where[] = "category = '$category'";
if ($destination) $where[] = "destination LIKE '%$destination%'";
if ($duration)    $where[] = "duration <= $duration";
if ($budget)      $where[] = "price <= $budget";

$where_sql = implode(' AND ', $where);

$order = match($sort) {
    'price_asc'  => 'price ASC',
    'price_desc' => 'price DESC',
    'duration'   => 'duration ASC',
    'name'       => 'name ASC',
    default      => 'rating DESC'
};

$result = $conn->query("SELECT * FROM packages WHERE $where_sql ORDER BY $order");
$total  = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Packages - <?= SITE_NAME ?></title>
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
            <li><a href="booking.php" class="nav-btn">Book Now</a></li>
        </ul>
        <div class="menu-toggle" id="menuToggle"><span></span><span></span><span></span></div>
    </div>
</nav>

<!-- PAGE HERO -->
<div class="page-hero">
    <h1>Explore Our Tour Packages</h1>
    <p><?= $total ?> package<?= $total != 1 ? 's' : '' ?> found for your search</p>
    <div class="breadcrumb">
        <a href="index.php">Home</a>
        <span>/</span>
        <span>Packages</span>
        <?php if ($category): ?><span>/</span><span><?= htmlspecialchars($category) ?></span><?php endif; ?>
    </div>
</div>

<!-- SEARCH & FILTER -->
<div class="search-section">
    <form class="search-form" method="GET">
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach (['Beach','Adventure','Cultural','Wildlife','Mountain','Luxury'] as $cat): ?>
            <option value="<?= $cat ?>" <?= $category == $cat ? 'selected' : '' ?>><?= $cat ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="destination" placeholder="Destination..." value="<?= htmlspecialchars($destination) ?>">
        <select name="duration">
            <option value="">Any Duration</option>
            <option value="7"  <?= $duration == 7  ? 'selected' : '' ?>>Up to 7 Days</option>
            <option value="10" <?= $duration == 10 ? 'selected' : '' ?>>Up to 10 Days</option>
            <option value="14" <?= $duration == 14 ? 'selected' : '' ?>>Up to 14 Days</option>
        </select>
        <select name="budget">
            <option value="">Any Budget</option>
            <option value="50000"  <?= $budget == 50000  ? 'selected' : '' ?>>Under &#8377;50,000</option>
            <option value="100000" <?= $budget == 100000 ? 'selected' : '' ?>>Under &#8377;1,00,000</option>
            <option value="150000" <?= $budget == 150000 ? 'selected' : '' ?>>Under &#8377;1,50,000</option>
        </select>
        <select name="sort">
            <option value="rating"     <?= $sort == 'rating'     ? 'selected' : '' ?>>Top Rated</option>
            <option value="price_asc"  <?= $sort == 'price_asc'  ? 'selected' : '' ?>>Price: Low to High</option>
            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
            <option value="duration"   <?= $sort == 'duration'   ? 'selected' : '' ?>>Shortest First</option>
            <option value="name"       <?= $sort == 'name'       ? 'selected' : '' ?>>Name A-Z</option>
        </select>
        <button type="submit">&#128269; Filter</button>
    </form>
</div>

<!-- CATEGORY PILLS -->
<section class="section" style="padding-top:40px; padding-bottom:20px;">
    <div class="filter-bar">
        <a href="packages.php" class="filter-btn <?= !$category ? 'active' : '' ?>">All Packages</a>
        <?php foreach (['Beach','Adventure','Cultural','Wildlife','Mountain','Luxury'] as $cat): ?>
        <a href="packages.php?category=<?= $cat ?>" class="filter-btn <?= $category == $cat ? 'active' : '' ?>"><?= $cat ?></a>
        <?php endforeach; ?>
    </div>

    <!-- PACKAGES GRID -->
    <?php if ($total == 0): ?>
    <div class="no-results">
        <span>&#128205;</span>
        <h3>No packages found</h3>
        <p>Try adjusting your filters or <a href="packages.php" style="color:var(--primary);">view all packages</a>.</p>
    </div>
    <?php else: ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
        <p style="color:#6c757d;font-size:0.95rem;">Showing <strong style="color:#1a1a2e;"><?= $total ?></strong> package<?= $total != 1 ? 's' : '' ?></p>
        <?php if ($category || $destination || $duration || $budget): ?>
        <a href="packages.php" style="font-size:0.88rem;color:#e74c3c;font-weight:600;">&#10006; Clear Filters</a>
        <?php endif; ?>
    </div>
    <div class="packages-grid">
        <?php while ($pkg = $result->fetch_assoc()):
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
                <?php if ($pkg['is_featured']): ?>
                    <span style="position:absolute;bottom:10px;left:10px;background:rgba(245,166,35,0.9);color:#1a1a2e;padding:3px 10px;border-radius:20px;font-size:0.75rem;font-weight:700;">&#11088; FEATURED</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="card-destination"><?= htmlspecialchars($pkg['destination']) ?></div>
                <h3><?= htmlspecialchars($pkg['name']) ?></h3>
                <p class="card-desc"><?= htmlspecialchars(substr($pkg['short_desc'], 0, 90)) ?>...</p>
                <div class="card-meta">
                    <span>&#128197; <?= $pkg['duration'] ?> Days</span>
                    <span>&#128101; Max <?= $pkg['max_people'] ?></span>
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
    <?php endif; ?>
</section>

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
