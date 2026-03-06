<?php require_once 'config.php'; ?>
<?php
$categories = [];
$menuByCategory = [];

try {
    $pdo = getDB();
    $cats = $pdo->query("SELECT * FROM menu_categories ORDER BY display_order")->fetchAll();
    foreach ($cats as $cat) {
        $categories[$cat['id']] = $cat;
        $menuByCategory[$cat['id']] = [];
    }
    $items = $pdo->query("SELECT * FROM menu_items ORDER BY category_id, name")->fetchAll();
    foreach ($items as $item) {
        $menuByCategory[$item['category_id']][] = $item;
    }
} catch (Exception $e) {}

$catEmojis = ['starters' => '🥗', 'mains' => '🍽️', 'pasta' => '🍝', 'desserts' => '🍮', 'beverages' => '🍷'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu — <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="nav-inner">
        <a class="nav-logo" href="index.php">La Bella <span>Cucina</span></a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="menu.php" class="active">Menu</a></li>
            <li><a href="reservations.php">Reservations</a></li>
            <li><a href="reservations.php" class="nav-reserve">Book a Table</a></li>
        </ul>
    </div>
</nav>

<!-- MENU HERO -->
<div class="menu-hero">
    <h1><em>Our</em> Menu</h1>
    <p>Fresh, seasonal ingredients crafted into dishes that carry the soul of Italy</p>
</div>

<!-- TABS -->
<?php if (!empty($categories)): ?>
<div class="menu-tabs">
    <div class="tabs-inner">
        <?php $first = true; foreach ($categories as $cat): ?>
        <button class="tab-btn <?= $first ? 'active' : '' ?>" data-target="cat-<?= $cat['id'] ?>">
            <?= $catEmojis[$cat['slug']] ?? '🍴' ?> <?= htmlspecialchars($cat['name']) ?>
        </button>
        <?php $first = false; endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- MENU SECTIONS -->
<?php if (!empty($categories)): ?>
    <?php foreach ($categories as $cat): ?>
    <div class="menu-section" id="cat-<?= $cat['id'] ?>">
        <div class="container">
            <h2 class="menu-cat-title"><?= $catEmojis[$cat['slug']] ?? '🍴' ?> <em><?= htmlspecialchars($cat['name']) ?></em></h2>
            <div class="menu-cat-divider"></div>
            <?php $items = $menuByCategory[$cat['id']] ?? []; ?>
            <?php if (!empty($items)): ?>
            <div class="menu-items-grid">
                <?php foreach ($items as $item):
                    $badgeClass = 'badge-gold';
                    if (in_array($item['badge'], ['Popular', 'Must Try'])) $badgeClass = 'badge-red';
                    if (in_array($item['badge'], ['Vegan', 'Vegetarian']))  $badgeClass = 'badge-green';
                    if (in_array($item['badge'], ['Signature', 'Premium'])) $badgeClass = 'badge-blue';
                    if (in_array($item['badge'], ['Sharing', 'Daily']))     $badgeClass = 'badge-gold';
                ?>
                <div class="menu-item-row <?= !$item['is_available'] ? 'unavailable' : '' ?>">
                    <div class="menu-item-info">
                        <h4><?= htmlspecialchars($item['name']) ?></h4>
                        <p><?= htmlspecialchars($item['description']) ?></p>
                    </div>
                    <div class="menu-item-meta">
                        <div class="item-price">$<?= number_format($item['price'], 2) ?></div>
                        <?php if ($item['badge']): ?>
                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($item['badge']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="color:#888;font-family:Arial,sans-serif;">No items in this category yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
<?php else: ?>
<div class="section container">
    <p style="text-align:center;color:#888;font-family:Arial,sans-serif;padding:60px 0;">
        Menu unavailable. Please <a href="database.sql" style="color:#c9a84c;">import database.sql</a> first.
    </p>
</div>
<?php endif; ?>

<!-- CTA -->
<div class="cta-bar">
    <h2>Fancy Dining In?</h2>
    <p>Reserve your table and enjoy the full La Bella Cucina experience.</p>
    <a href="reservations.php" class="btn btn-dark">Make a Reservation</a>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-grid">
        <div class="footer-brand">
            <h3>La Bella Cucina</h3>
            <p>Authentic Italian cuisine served with passion since 1987.</p>
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
            <h4>Hours</h4>
            <ul>
                <li><a href="#">Lunch: Tue–Fri 12–3pm</a></li>
                <li><a href="#">Dinner: Tue–Sun 6–10:30pm</a></li>
                <li><a href="#">Brunch: Sat–Sun 11–3pm</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <span>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</span>
        <span><a href="admin_login.php" style="color:#555;">Staff Login</a></span>
    </div>
</footer>

<script>
// Tab scroll + highlight
const tabs = document.querySelectorAll('.tab-btn');
const sections = document.querySelectorAll('.menu-section');

tabs.forEach(btn => {
    btn.addEventListener('click', () => {
        const target = document.getElementById(btn.dataset.target);
        if (target) {
            const offset = 70 + 54; // navbar + tabs height
            const top = target.getBoundingClientRect().top + window.scrollY - offset;
            window.scrollTo({ top, behavior: 'smooth' });
        }
    });
});

// Highlight active tab on scroll
function updateActiveTab() {
    const navH = 70 + 54;
    let current = sections[0]?.id;
    sections.forEach(sec => {
        if (sec.getBoundingClientRect().top <= navH + 40) current = sec.id;
    });
    tabs.forEach(btn => {
        btn.classList.toggle('active', btn.dataset.target === current);
    });
}
window.addEventListener('scroll', updateActiveTab, { passive: true });
</script>
</body>
</html>
