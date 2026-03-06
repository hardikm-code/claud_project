<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/m_destination.php';
require_once __DIR__ . '/m_tour.php';
require_once __DIR__ . '/db.php';

$destModel = new DestinationModel();
$tourModel = new TourModel();

$featuredDests  = $destModel->getFeatured(6);
$featuredTours  = $tourModel->getFeatured(6);

// Testimonials
$testimonials = db()->query('SELECT * FROM testimonials WHERE featured = 1 ORDER BY id LIMIT 6')->fetchAll();

$pageTitle = 'Home - Discover the World';
$pageDesc  = 'WanderLux Travel - Discover extraordinary destinations and book unforgettable tours worldwide.';

include __DIR__ . '/header.php';
?>

<!-- ============================================================
     HERO
     ============================================================ -->
<section class="hero">
  <div class="container hero-content">
    <div class="hero-tag">
      <span></span> Trusted by 50,000+ travelers worldwide
    </div>
    <h1 class="hero-title">
      Discover Your Next<br><span>Great Adventure</span>
    </h1>
    <p class="hero-subtitle">
      From tropical paradise to ancient wonders — we craft extraordinary journeys that create lifelong memories.
    </p>
    <div class="hero-actions">
      <a href="tours.php" class="btn btn-accent btn-lg">Explore Tours</a>
      <a href="destinations.php" class="btn btn-outline-white btn-lg">View Destinations</a>
    </div>

    <!-- Search Bar -->
    <form action="tours.php" method="GET" class="search-bar">
      <div class="search-field">
        <span class="search-field-icon">🔍</span>
        <input type="text" name="q" placeholder="Where do you want to go?" autocomplete="off">
      </div>
      <div class="search-divider"></div>
      <div class="search-field">
        <span class="search-field-icon">✈</span>
        <select name="type">
          <option value="">Tour Type</option>
          <option value="Adventure">Adventure</option>
          <option value="Cultural">Cultural</option>
          <option value="Romantic">Romantic</option>
          <option value="Luxury">Luxury</option>
          <option value="City">City</option>
        </select>
      </div>
      <div class="search-divider"></div>
      <div class="search-field">
        <span class="search-field-icon">👥</span>
        <select name="persons">
          <option value="">Travelers</option>
          <option value="1">1 Traveler</option>
          <option value="2">2 Travelers</option>
          <option value="4">4 Travelers</option>
          <option value="6">6+ Travelers</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Search Tours</button>
    </form>

    <div class="hero-stats">
      <div class="hero-stat">
        <div class="hero-stat-num">50K+</div>
        <div class="hero-stat-label">Happy Travelers</div>
      </div>
      <div class="hero-stat">
        <div class="hero-stat-num">120+</div>
        <div class="hero-stat-label">Destinations</div>
      </div>
      <div class="hero-stat">
        <div class="hero-stat-num">500+</div>
        <div class="hero-stat-label">Tour Packages</div>
      </div>
      <div class="hero-stat">
        <div class="hero-stat-num">15yr</div>
        <div class="hero-stat-label">Experience</div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     FEATURED DESTINATIONS
     ============================================================ -->
<section class="section" style="background: var(--light-gray)">
  <div class="container">
    <div class="section-header">
      <span class="section-tag">Top Destinations</span>
      <h2 class="section-title">Popular Destinations</h2>
      <p class="section-desc">Hand-picked destinations offering the most extraordinary travel experiences around the globe.</p>
    </div>
    <div class="grid grid-3">
      <?php foreach ($featuredDests as $dest): ?>
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
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
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
    <div style="text-align:center;margin-top:40px">
      <a href="destinations.php" class="btn btn-outline">View All Destinations</a>
    </div>
  </div>
</section>

<!-- ============================================================
     WHY CHOOSE US
     ============================================================ -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <span class="section-tag">Why WanderLux</span>
      <h2 class="section-title">Travel With Confidence</h2>
      <p class="section-desc">We're more than a travel agency — we're your partner in crafting journeys that last a lifetime.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">🏆</div>
        <h3>Award-Winning Service</h3>
        <p>Recognized globally for exceptional travel experiences and customer satisfaction across 15+ years.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🛡</div>
        <h3>100% Safe & Secure</h3>
        <p>Your safety is our priority. All tours include comprehensive travel insurance and 24/7 emergency support.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">💎</div>
        <h3>Best Price Guarantee</h3>
        <p>We match any comparable offer. Get the best value without compromising on quality or experience.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🎯</div>
        <h3>Expert Local Guides</h3>
        <p>Our passionate local guides bring destinations to life with insider knowledge and authentic experiences.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     FEATURED TOURS
     ============================================================ -->
<section class="section" style="background: var(--light-gray)">
  <div class="container">
    <div class="section-header">
      <span class="section-tag">Popular Tours</span>
      <h2 class="section-title">Handpicked Tour Packages</h2>
      <p class="section-desc">Carefully crafted itineraries designed to maximize your experience at every destination.</p>
    </div>
    <div class="grid grid-3">
      <?php foreach ($featuredTours as $tour): ?>
      <a href="tour.php?id=<?php echo $tour['id']; ?>" class="card" style="display:block">
        <div class="card-img-wrap">
          <img src="<?php echo e($tour['image']); ?>" alt="<?php echo e($tour['name']); ?>" loading="lazy">
          <span class="card-badge"><?php echo e($tour['tour_type']); ?></span>
          <span class="card-price-tag"><?php echo formatPrice($tour['price']); ?></span>
        </div>
        <div class="card-body">
          <div class="card-location">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
            <?php echo e($tour['destination_name']); ?>, <?php echo e($tour['country']); ?>
          </div>
          <h3 class="card-title"><?php echo e($tour['name']); ?></h3>
          <p class="card-desc"><?php echo truncate($tour['short_desc'] ?: $tour['description'], 95); ?></p>
          <div class="card-meta">
            <div class="card-duration">
              ⏱ <?php echo $tour['duration_days']; ?> Days
            </div>
            <div>
              <div class="card-price"><?php echo formatPrice($tour['price']); ?><small>/person</small></div>
            </div>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:40px">
      <a href="tours.php" class="btn btn-outline">Browse All Tours</a>
    </div>
  </div>
</section>

<!-- ============================================================
     TESTIMONIALS
     ============================================================ -->
<?php if (!empty($testimonials)): ?>
<section class="section">
  <div class="container">
    <div class="section-header">
      <span class="section-tag">Testimonials</span>
      <h2 class="section-title">What Our Travelers Say</h2>
      <p class="section-desc">Real stories from real travelers who have explored the world with WanderLux.</p>
    </div>
    <div class="testimonials-grid">
      <?php foreach ($testimonials as $t): ?>
      <div class="testimonial-card">
        <p class="testimonial-text">"<?php echo e($t['message']); ?>"</p>
        <div class="testimonial-author">
          <div class="testimonial-avatar"><?php echo strtoupper(substr($t['user_name'], 0, 1)); ?></div>
          <div>
            <div class="testimonial-name"><?php echo e($t['user_name']); ?></div>
            <div class="testimonial-location"><?php echo e($t['user_location']); ?></div>
            <div><?php echo renderStars($t['rating']); ?></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ============================================================
     CTA SECTION
     ============================================================ -->
<section class="cta-section">
  <div class="container">
    <h2>Ready to Start Your Adventure?</h2>
    <p>Join thousands of happy travelers. Book your dream trip today!</p>
    <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap">
      <a href="tours.php" class="btn btn-outline-white btn-lg">Browse Tours</a>
      <a href="contact.php" class="btn btn-lg" style="background:white;color:var(--accent)">Get Custom Quote</a>
    </div>
  </div>
</section>

<!-- ============================================================
     NEWSLETTER
     ============================================================ -->
<section class="newsletter">
  <div class="container text-center">
    <span class="section-tag" style="background:rgba(255,255,255,.2);color:white">Newsletter</span>
    <h2 class="section-title text-white" style="margin-top:12px">Get Travel Inspiration</h2>
    <p style="color:rgba(255,255,255,.75);font-size:17px">Subscribe to receive exclusive deals, travel tips and destination guides.</p>
    <form class="newsletter-form" onsubmit="return handleNewsletter(event)">
      <input type="email" placeholder="Enter your email address" required>
      <button type="submit" class="btn btn-accent">Subscribe</button>
    </form>
  </div>
</section>

<script>
function handleNewsletter(e) {
  e.preventDefault();
  e.target.innerHTML = '<p style="color:white;font-size:16px;font-weight:600">✓ Thank you for subscribing! Get ready for amazing travel inspiration.</p>';
  return false;
}
</script>

<?php include __DIR__ . '/footer.php'; ?>
