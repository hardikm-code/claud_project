<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/m_destination.php';

$destModel = new DestinationModel();
$q = trim($_GET['q'] ?? '');
$destinations = $q ? $destModel->search($q) : $destModel->getAll(50);

$pageTitle = 'Destinations';
$pageDesc  = 'Explore our amazing travel destinations around the world.';
include __DIR__ . '/header.php';
?>

<div class="page-hero">
  <div class="page-hero-content container">
    <div class="breadcrumb">
      <a href="index.php">Home</a>
      <span class="breadcrumb-sep">›</span>
      <span>Destinations</span>
    </div>
    <h1 class="page-hero-title">Explore Destinations</h1>
    <p class="page-hero-desc">Discover breathtaking destinations across every corner of the globe.</p>
  </div>
</div>

<section class="section" style="background:var(--light-gray)">
  <div class="container">
    <!-- Search & Filter -->
    <form method="GET" class="filter-bar">
      <div class="form-group">
        <label class="form-label">Search Destinations</label>
        <input type="text" name="q" class="form-control" placeholder="Search by name, country..." value="<?php echo e($q); ?>">
      </div>
      <div style="display:flex;align-items:flex-end;gap:10px">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($q): ?>
          <a href="destinations.php" class="btn btn-gray">Clear</a>
        <?php endif; ?>
      </div>
    </form>

    <?php if ($q): ?>
      <p style="margin-bottom:24px;color:var(--gray)">
        Found <strong><?php echo count($destinations); ?></strong> result(s) for "<strong><?php echo e($q); ?></strong>"
      </p>
    <?php endif; ?>

    <?php if (empty($destinations)): ?>
      <div class="empty-state">
        <div class="empty-state-icon">🗺</div>
        <h3>No destinations found</h3>
        <p>Try a different search term.</p>
        <a href="destinations.php" class="btn btn-primary mt-3">View All</a>
      </div>
    <?php else: ?>
      <div class="grid grid-3">
        <?php foreach ($destinations as $dest): ?>
        <a href="destination.php?id=<?php echo $dest['id']; ?>" class="card" style="display:block">
          <div class="card-img-wrap">
            <img src="<?php echo e($dest['image']); ?>" alt="<?php echo e($dest['name']); ?>" loading="lazy">
            <?php if ($dest['featured']): ?>
              <span class="card-badge card-badge-featured">Featured</span>
            <?php endif; ?>
            <span class="card-price-tag">From <?php echo formatPrice($dest['price_from']); ?></span>
          </div>
          <div class="card-body">
            <div class="card-location">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
              <?php echo e($dest['country']); ?>
            </div>
            <h3 class="card-title"><?php echo e($dest['name']); ?></h3>
            <p class="card-desc"><?php echo truncate($dest['short_desc'] ?: $dest['description'], 100); ?></p>
            <div class="card-meta">
              <div class="card-rating">
                <?php echo renderStars($dest['rating']); ?>
                <span><?php echo $dest['rating']; ?></span>
              </div>
              <span class="btn btn-primary btn-sm">Explore →</span>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="cta-section">
  <div class="container">
    <h2>Can't Find Your Dream Destination?</h2>
    <p>Tell us where you want to go. We create custom itineraries just for you!</p>
    <a href="contact.php" class="btn btn-lg" style="background:white;color:var(--accent)">Request Custom Tour</a>
  </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
