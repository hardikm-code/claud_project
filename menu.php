<?php
require_once 'config.php';
$pageTitle = 'Our Menu';
$pageDesc  = 'Explore the full menu at La Bella Cucina — authentic Italian dishes made with the finest ingredients.';
include 'header.php';

// Fetch all categories and their items
$categories = $conn->query("SELECT * FROM menu_categories ORDER BY display_order ASC");
$catList = [];
while ($cat = $categories->fetch_assoc()) {
    $catList[] = $cat;
}

$menuData = [];
foreach ($catList as $cat) {
    $cid   = (int)$cat['id'];
    $items = $conn->query("SELECT * FROM menu_items WHERE category_id = $cid AND is_available = 1 ORDER BY name ASC");
    $menuData[$cid] = [];
    while ($item = $items->fetch_assoc()) {
        $menuData[$cid][] = $item;
    }
}
?>

<div class="menu-page-hero">
    <h1>Our Menu</h1>
    <p>Crafted with passion &bull; Served with love</p>
</div>

<!-- Category Tabs -->
<div class="menu-tabs">
    <div class="menu-tabs-inner">
        <?php foreach ($catList as $i => $cat): ?>
        <button class="tab-btn <?php echo $i === 0 ? 'active' : ''; ?>"
                onclick="showCategory('cat-<?php echo $cat['id']; ?>', this)">
            <?php echo htmlspecialchars($cat['name']); ?>
        </button>
        <?php endforeach; ?>
    </div>
</div>

<!-- Menu Content -->
<section style="background: var(--white); padding: 60px 24px;">
    <div class="container">
        <?php foreach ($catList as $i => $cat): ?>
        <div id="cat-<?php echo $cat['id']; ?>" class="menu-section <?php echo $i === 0 ? 'active' : ''; ?>">
            <h2 class="menu-category-title"><?php echo htmlspecialchars($cat['name']); ?></h2>
            <div class="menu-items-list">
                <?php if (!empty($menuData[$cat['id']])): ?>
                    <?php foreach ($menuData[$cat['id']] as $item): ?>
                    <div class="menu-item-row">
                        <div class="menu-item-info">
                            <h3>
                                <?php echo htmlspecialchars($item['name']); ?>
                                <?php if ($item['is_featured']): ?>
                                <span class="badge-featured" style="font-size:0.65rem; vertical-align:middle;">Chef's Pick</span>
                                <?php endif; ?>
                            </h3>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                        </div>
                        <div class="menu-item-price">$<?php echo number_format($item['price'], 2); ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--gray); padding: 20px 0;">No items available in this category yet.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Reservation CTA -->
<section style="background: var(--cream); padding: 60px 24px; text-align: center;">
    <div class="container">
        <h2 style="margin-bottom: 16px;">Fancy a Table?</h2>
        <p style="color: var(--gray); margin-bottom: 32px;">Book your dining experience and we'll have everything ready for you.</p>
        <a href="reservations.php" class="btn btn-primary">Reserve a Table</a>
    </div>
</section>

<script>
function showCategory(id, btn) {
    document.querySelectorAll('.menu-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    btn.classList.add('active');
}
</script>

<?php include 'footer.php'; ?>
