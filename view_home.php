<!-- ── HERO ─────────────────────────────────────────────────────────── -->
<section class="hero" id="home">
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <p class="hero-subtitle">Welcome to Bella Vista</p>
    <h1 class="hero-title">A Fine Dining<br><em>Experience</em></h1>
    <p class="hero-desc">Where culinary artistry meets elegant ambiance. Every dish is a celebration of flavors crafted with the finest ingredients.</p>
    <div class="hero-btns">
      <a href="index.php?page=menu" class="btn btn-gold">Explore Menu</a>
      <a href="index.php?page=reservation" class="btn btn-outline">Book a Table</a>
    </div>
  </div>
  <div class="hero-scroll">
    <span>Scroll Down</span>
    <div class="scroll-line"></div>
  </div>
</section>

<!-- ── ABOUT ──────────────────────────────────────────────────────── -->
<section class="about section-pad" id="about">
  <div class="container">
    <div class="about-grid">
      <div class="about-image">
        <div class="about-img-wrap">
          <div class="about-img-bg"></div>
          <div class="about-badge">
            <span class="badge-num">25</span>
            <span class="badge-text">Years of<br>Excellence</span>
          </div>
        </div>
      </div>
      <div class="about-content">
        <p class="section-label">Our Story</p>
        <h2 class="section-title">Crafting Memorable <em>Dining Experiences</em></h2>
        <p class="about-text">Founded in 1999, Bella Vista has been a beacon of fine dining in New York City. Our philosophy is simple: exceptional food, prepared with love and served with warmth.</p>
        <p class="about-text">Each dish on our menu is a result of years of culinary exploration, using only the freshest locally sourced and imported premium ingredients.</p>
        <div class="about-stats">
          <div class="stat-item">
            <span class="stat-num">200+</span>
            <span class="stat-label">Menu Items</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <span class="stat-num">50k+</span>
            <span class="stat-label">Happy Guests</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <span class="stat-num">18</span>
            <span class="stat-label">Awards Won</span>
          </div>
        </div>
        <a href="index.php?page=menu" class="btn btn-gold">View Full Menu</a>
      </div>
    </div>
  </div>
</section>

<!-- ── FEATURED MENU ───────────────────────────────────────────────── -->
<section class="featured-menu section-pad bg-dark" id="featured">
  <div class="container">
    <div class="section-header">
      <p class="section-label">Signature Dishes</p>
      <h2 class="section-title">Featured <em>Menu</em></h2>
      <p class="section-desc">Handpicked favorites by our Executive Chef – dishes that define the Bella Vista experience.</p>
    </div>

    <div class="menu-grid">
      <?php if (!empty($featured_items)): ?>
        <?php foreach ($featured_items as $item): ?>
        <div class="menu-card">
          <div class="menu-card-img" style="background-image: url('<?= clean($item['image_url'] ?: '') ?>')">
            <div class="menu-card-overlay"></div>
            <span class="menu-card-badge">Featured</span>
          </div>
          <div class="menu-card-body">
            <span class="menu-card-category"><?= clean($item['category_name']) ?></span>
            <h3 class="menu-card-title"><?= clean($item['name']) ?></h3>
            <p class="menu-card-desc"><?= clean($item['description']) ?></p>
            <div class="menu-card-footer">
              <span class="menu-card-price">$<?= number_format($item['price'], 2) ?></span>
              <a href="index.php?page=menu" class="btn-sm">Order Now</a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="no-items">No featured items available. <a href="setup.php">Run setup</a> to add sample data.</p>
      <?php endif; ?>
    </div>

    <div class="section-cta">
      <a href="index.php?page=menu" class="btn btn-gold">View Full Menu</a>
    </div>
  </div>
</section>

<!-- ── FEATURES ───────────────────────────────────────────────────── -->
<section class="features section-pad">
  <div class="container">
    <div class="section-header">
      <p class="section-label">Why Choose Us</p>
      <h2 class="section-title">The Bella Vista <em>Difference</em></h2>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">🍽</div>
        <h3>Farm to Table</h3>
        <p>We source only the freshest seasonal produce from local farms and trusted suppliers around the world.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">👨‍🍳</div>
        <h3>Award-Winning Chefs</h3>
        <p>Our team of Michelin-trained chefs brings decades of experience and passion to every plate.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🍷</div>
        <h3>Curated Wine List</h3>
        <p>Over 300 labels selected by our sommelier to pair perfectly with every dish on our menu.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">✨</div>
        <h3>Impeccable Service</h3>
        <p>Our attentive staff ensures every guest feels like royalty from the first greeting to the last goodbye.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🎂</div>
        <h3>Private Events</h3>
        <p>Host your celebrations, corporate dinners, and special occasions in our elegant private dining room.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🌿</div>
        <h3>Dietary Options</h3>
        <p>Extensive vegetarian, vegan, and gluten-free menu options crafted with the same artisanal care.</p>
      </div>
    </div>
  </div>
</section>

<!-- ── TESTIMONIALS ────────────────────────────────────────────────── -->
<section class="testimonials section-pad bg-dark">
  <div class="container">
    <div class="section-header">
      <p class="section-label">Guest Reviews</p>
      <h2 class="section-title">What Our <em>Guests Say</em></h2>
    </div>
    <div class="testimonials-grid">
      <div class="testimonial-card">
        <div class="stars">★★★★★</div>
        <p class="testimonial-text">"An absolutely sublime dining experience. The truffle tagliatelle was unlike anything I've ever tasted. We'll be back for every anniversary."</p>
        <div class="testimonial-author">
          <div class="author-avatar">SL</div>
          <div>
            <span class="author-name">Sarah Lancaster</span>
            <span class="author-title">Food Critic, NY Times</span>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="stars">★★★★★</div>
        <p class="testimonial-text">"From the moment we walked in, everything was perfect. The ambiance, the service, the food – Bella Vista is in a class of its own."</p>
        <div class="testimonial-author">
          <div class="author-avatar">JM</div>
          <div>
            <span class="author-name">James Mitchell</span>
            <span class="author-title">Regular Guest</span>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="stars">★★★★★</div>
        <p class="testimonial-text">"We celebrated our 25th anniversary here and it exceeded every expectation. The staff remembered our names and made us feel truly special."</p>
        <div class="testimonial-author">
          <div class="author-avatar">EK</div>
          <div>
            <span class="author-name">Elena & Karl Weber</span>
            <span class="author-title">Anniversary Guests</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── RESERVATION CTA ─────────────────────────────────────────────── -->
<section class="reservation-cta section-pad">
  <div class="container">
    <div class="cta-content">
      <p class="section-label">Reserve Your Table</p>
      <h2 class="section-title">Make a <em>Reservation</em></h2>
      <p class="cta-desc">Join us for an unforgettable dining experience. Tables fill up fast – secure yours today.</p>
      <div class="cta-info">
        <div class="cta-info-item">
          <span>📞</span>
          <span>(555) 123-4567</span>
        </div>
        <div class="cta-info-item">
          <span>📍</span>
          <span>123 Gourmet Ave, New York</span>
        </div>
      </div>
      <a href="index.php?page=reservation" class="btn btn-gold btn-lg">Book Your Table Now</a>
    </div>
  </div>
</section>
