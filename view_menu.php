<!-- ── PAGE BANNER ───────────────────────────────────────────────── -->
<section class="page-banner">
  <div class="page-banner-overlay"></div>
  <div class="page-banner-content">
    <p class="section-label">Bella Vista</p>
    <h1>Our <em>Menu</em></h1>
    <p class="breadcrumb"><a href="index.php">Home</a> / <span>Menu</span></p>
  </div>
</section>

<!-- ── MENU SECTION ──────────────────────────────────────────────── -->
<section class="menu-section section-pad">
  <div class="container">

    <div class="section-header">
      <p class="section-label">Culinary Delights</p>
      <h2 class="section-title">Explore Our <em>Full Menu</em></h2>
      <p class="section-desc">Browse through our carefully curated selection of dishes, each crafted with the finest ingredients.</p>
    </div>

    <!-- Category Tabs -->
    <div class="menu-tabs" id="menuTabs">
      <button class="menu-tab active" data-category="all">All Items</button>
      <?php foreach ($categories as $cat): ?>
        <button class="menu-tab" data-category="<?= $cat['id'] ?>">
          <?= clean($cat['name']) ?>
        </button>
      <?php endforeach; ?>
    </div>

    <!-- Menu Items Grid -->
    <div class="menu-items-grid" id="menuGrid">
      <?php if (!empty($menu_items)): ?>
        <?php foreach ($menu_items as $item): ?>
        <div class="menu-item-card" data-category="<?= $item['category_id'] ?>">
          <div class="mic-image" style="<?= $item['image_url'] ? "background-image:url('" . clean($item['image_url']) . "')" : '' ?>">
            <?php if (!$item['image_url']): ?>
              <div class="mic-img-placeholder">🍽</div>
            <?php endif; ?>
            <?php if ($item['is_featured']): ?>
              <span class="mic-badge">Chef's Pick</span>
            <?php endif; ?>
          </div>
          <div class="mic-body">
            <div class="mic-category"><?= clean($item['category_name']) ?></div>
            <h3 class="mic-title"><?= clean($item['name']) ?></h3>
            <p class="mic-desc"><?= clean($item['description']) ?></p>
            <div class="mic-footer">
              <span class="mic-price">$<?= number_format($item['price'], 2) ?></span>
              <a href="index.php?page=reservation" class="btn-sm">Reserve</a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-items-msg">
          <p>Menu items are not yet available.</p>
          <a href="setup.php" class="btn btn-gold">Run Setup</a>
        </div>
      <?php endif; ?>
    </div>

    <!-- No results message (shown via JS) -->
    <div class="no-results" id="noResults" style="display:none;">
      <p>No items found in this category.</p>
    </div>

  </div>
</section>

<!-- ── MENU SCRIPTS ──────────────────────────────────────────────── -->
<script>
(function() {
  const tabs    = document.querySelectorAll('.menu-tab');
  const cards   = document.querySelectorAll('.menu-item-card');
  const noRes   = document.getElementById('noResults');

  tabs.forEach(tab => {
    tab.addEventListener('click', function() {
      // Toggle active tab
      tabs.forEach(t => t.classList.remove('active'));
      this.classList.add('active');

      const cat = this.dataset.category;
      let visible = 0;

      cards.forEach(card => {
        const match = cat === 'all' || card.dataset.category === cat;
        card.style.display = match ? '' : 'none';
        if (match) visible++;
      });

      noRes.style.display = visible === 0 ? 'block' : 'none';
    });
  });
})();
</script>
