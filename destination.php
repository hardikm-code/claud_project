<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/m_destination.php';
require_once __DIR__ . '/m_tour.php';
require_once __DIR__ . '/db.php';

$destModel = new DestinationModel();
$tourModel = new TourModel();

$id   = (int)($_GET['id'] ?? 0);
$dest = $id ? $destModel->getById($id) : null;

if (!$dest) {
    flash('error', 'Destination not found.', 'error');
    header('Location: destinations.php');
    exit;
}

$tours   = $tourModel->getByDestination($dest['id']);
$gallery = db()->prepare('SELECT * FROM gallery WHERE destination_id = ?');
$gallery->execute([$dest['id']]);
$gallery = $gallery->fetchAll();

$pageTitle = $dest['name'];
$pageDesc  = $dest['short_desc'] ?: $dest['description'];
include __DIR__ . '/header.php';
?>

<!-- Detail Hero -->
<div class="detail-hero" style="background-image:url('<?php echo e($dest['image']); ?>')">
  <div class="container detail-hero-content">
    <div class="breadcrumb" style="justify-content:flex-start;margin-bottom:16px">
      <a href="index.php">Home</a>
      <span class="breadcrumb-sep" style="color:rgba(255,255,255,.5)">›</span>
      <a href="destinations.php">Destinations</a>
      <span class="breadcrumb-sep" style="color:rgba(255,255,255,.5)">›</span>
      <span style="color:rgba(255,255,255,.8)"><?php echo e($dest['name']); ?></span>
    </div>
    <div class="chip chip-accent" style="margin-bottom:12px"><?php echo e($dest['country']); ?></div>
    <h1 style="font-size:clamp(36px,6vw,64px);font-weight:900;color:white;margin-bottom:12px"><?php echo e($dest['name']); ?></h1>
    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
      <div style="display:flex;align-items:center;gap:8px;color:rgba(255,255,255,.9)">
        <?php echo renderStars($dest['rating']); ?>
        <span style="font-weight:700"><?php echo $dest['rating']; ?> / 5.0</span>
      </div>
      <span style="color:rgba(255,255,255,.6)">|</span>
      <span style="color:rgba(255,255,255,.9)"><?php echo count($tours); ?> tours available</span>
    </div>
  </div>
</div>

<div class="container">
  <div class="detail-layout">
    <!-- Left: Content -->
    <div>
      <div class="tabs-container">
        <div class="content-tabs">
          <button class="tab-btn active" data-tab="overview">Overview</button>
          <button class="tab-btn" data-tab="tours-tab">Tours (<?php echo count($tours); ?>)</button>
          <?php if (!empty($gallery)): ?>
          <button class="tab-btn" data-tab="gallery-tab">Gallery</button>
          <?php endif; ?>
        </div>

        <div id="overview" class="tab-content active">
          <h2 style="font-size:24px;font-weight:800;margin-bottom:16px">About <?php echo e($dest['name']); ?></h2>
          <div style="color:var(--gray);line-height:1.9;font-size:16px">
            <?php echo nl2br(e($dest['description'])); ?>
          </div>

          <div style="margin-top:32px">
            <h3 style="font-size:18px;font-weight:700;margin-bottom:16px">Quick Facts</h3>
            <div class="info-list">
              <div class="info-item">
                <div class="info-icon">🌍</div>
                <div>
                  <strong>Country</strong>
                  <?php echo e($dest['country']); ?>
                </div>
              </div>
              <div class="info-item">
                <div class="info-icon">💰</div>
                <div>
                  <strong>Starting From</strong>
                  <?php echo formatPrice($dest['price_from']); ?> per person
                </div>
              </div>
              <div class="info-item">
                <div class="info-icon">⭐</div>
                <div>
                  <strong>Rating</strong>
                  <?php echo $dest['rating']; ?>/5.0 — Excellent
                </div>
              </div>
              <div class="info-item">
                <div class="info-icon">✈</div>
                <div>
                  <strong>Available Tours</strong>
                  <?php echo count($tours); ?> packages available
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="tours-tab" class="tab-content">
          <?php if (empty($tours)): ?>
            <div class="empty-state">
              <div class="empty-state-icon">✈</div>
              <h3>No tours available yet</h3>
              <p>Check back soon or contact us for a custom tour.</p>
            </div>
          <?php else: ?>
            <div class="grid grid-2" style="gap:20px">
              <?php foreach ($tours as $tour): ?>
              <a href="tour.php?id=<?php echo $tour['id']; ?>" class="card" style="display:block">
                <div class="card-img-wrap" style="aspect-ratio:16/9">
                  <img src="<?php echo e($tour['image']); ?>" alt="<?php echo e($tour['name']); ?>" loading="lazy">
                  <span class="card-badge"><?php echo e($tour['tour_type']); ?></span>
                  <span class="card-price-tag"><?php echo formatPrice($tour['price']); ?></span>
                </div>
                <div class="card-body">
                  <h3 class="card-title"><?php echo e($tour['name']); ?></h3>
                  <p class="card-desc"><?php echo truncate($tour['short_desc'] ?: $tour['description'], 80); ?></p>
                  <div class="card-meta">
                    <span class="card-duration">⏱ <?php echo $tour['duration_days']; ?> Days</span>
                    <span class="btn btn-primary btn-sm">Book Now</span>
                  </div>
                </div>
              </a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <?php if (!empty($gallery)): ?>
        <div id="gallery-tab" class="tab-content">
          <div class="gallery-grid">
            <?php foreach ($gallery as $img): ?>
            <div class="gallery-item">
              <img src="<?php echo e($img['image_url']); ?>" alt="<?php echo e($img['title'] ?? $dest['name']); ?>" loading="lazy">
              <div class="gallery-item-overlay">🔍</div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Right: Booking Widget -->
    <div class="detail-sticky">
      <div class="booking-widget">
        <div class="booking-widget-price">
          <?php echo formatPrice($dest['price_from']); ?>
          <span>/ per person</span>
        </div>

        <?php if (!empty($tours)): ?>
          <a href="booking.php?tour_id=<?php echo $tours[0]['id']; ?>" class="btn btn-primary" style="width:100%;justify-content:center;margin-bottom:12px">
            Book Now
          </a>
        <?php endif; ?>
        <a href="tours.php?q=<?php echo urlencode($dest['name']); ?>" class="btn btn-outline" style="width:100%;justify-content:center">
          View All Tours
        </a>

        <div class="divider"></div>
        <div style="font-size:13px;color:var(--gray)">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">✅ Free cancellation up to 48h before</div>
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">✅ Best price guarantee</div>
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">✅ No booking fees</div>
          <div style="display:flex;align-items:center;gap:8px">📞 24/7 customer support</div>
        </div>
      </div>

      <div style="background:var(--light-gray);border-radius:var(--radius);padding:20px;margin-top:20px">
        <h4 style="font-weight:700;margin-bottom:12px;font-size:15px">Need Help?</h4>
        <p style="font-size:13px;color:var(--gray);margin-bottom:16px">Our travel experts are ready to help you plan the perfect trip.</p>
        <a href="contact.php" class="btn btn-outline" style="width:100%;justify-content:center;font-size:13px">Contact Us</a>
      </div>
    </div>
  </div>
</div>

<!-- Related Tours CTA -->
<?php if (!empty($tours)): ?>
<section class="section" style="background:var(--light-gray)">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Tours to <?php echo e($dest['name']); ?></h2>
    </div>
    <div class="grid grid-3">
      <?php foreach (array_slice($tours, 0, 3) as $tour): ?>
      <a href="tour.php?id=<?php echo $tour['id']; ?>" class="card" style="display:block">
        <div class="card-img-wrap">
          <img src="<?php echo e($tour['image']); ?>" alt="<?php echo e($tour['name']); ?>" loading="lazy">
          <span class="card-badge"><?php echo e($tour['tour_type']); ?></span>
          <span class="card-price-tag"><?php echo formatPrice($tour['price']); ?></span>
        </div>
        <div class="card-body">
          <h3 class="card-title"><?php echo e($tour['name']); ?></h3>
          <p class="card-desc"><?php echo truncate($tour['short_desc'] ?: $tour['description'], 90); ?></p>
          <div class="card-meta">
            <span class="card-duration">⏱ <?php echo $tour['duration_days']; ?> Days</span>
            <span class="card-price"><?php echo formatPrice($tour['price']); ?></span>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
