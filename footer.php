<?php require_once __DIR__ . '/config.php'; ?>
<!-- Back to top -->
<div class="back-to-top" id="backToTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">↑</div>

<footer>
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="footer-logo">✈ <?php echo SITE_NAME; ?></div>
        <p>We craft extraordinary travel experiences that turn dreams into unforgettable memories. Explore the world with confidence and style.</p>
        <div class="footer-social">
          <a href="#" title="Facebook">f</a>
          <a href="#" title="Twitter">t</a>
          <a href="#" title="Instagram">in</a>
          <a href="#" title="YouTube">▶</a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="<?php echo SITE_URL; ?>/index.php">Home</a></li>
          <li><a href="<?php echo SITE_URL; ?>/destinations.php">Destinations</a></li>
          <li><a href="<?php echo SITE_URL; ?>/tours.php">All Tours</a></li>
          <li><a href="<?php echo SITE_URL; ?>/gallery.php">Gallery</a></li>
          <li><a href="<?php echo SITE_URL; ?>/about.php">About Us</a></li>
          <li><a href="<?php echo SITE_URL; ?>/contact.php">Contact</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Tour Types</h4>
        <ul>
          <li><a href="<?php echo SITE_URL; ?>/tours.php?type=Adventure">Adventure Tours</a></li>
          <li><a href="<?php echo SITE_URL; ?>/tours.php?type=Cultural">Cultural Tours</a></li>
          <li><a href="<?php echo SITE_URL; ?>/tours.php?type=Romantic">Romantic Tours</a></li>
          <li><a href="<?php echo SITE_URL; ?>/tours.php?type=Luxury">Luxury Tours</a></li>
          <li><a href="<?php echo SITE_URL; ?>/tours.php?type=City">City Tours</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Contact Us</h4>
        <ul class="footer-contact">
          <li>📍 123 Travel Street, New York, NY 10001</li>
          <li>📞 <?php echo SITE_PHONE; ?></li>
          <li>✉ <?php echo SITE_EMAIL; ?></li>
          <li>🕐 Mon - Fri: 9am - 6pm EST</li>
        </ul>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;width:100%">
      <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
      <div class="footer-bottom-links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
        <a href="#">Cookie Policy</a>
      </div>
    </div>
  </div>
</footer>

<script>
// Back to top visibility
window.addEventListener('scroll', function() {
  const btn = document.getElementById('backToTop');
  if (window.scrollY > 300) { btn.classList.add('visible'); }
  else { btn.classList.remove('visible'); }
});
// Tab functionality
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const target = this.dataset.tab;
    const parent = this.closest('.tabs-container') || document;
    parent.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    parent.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    this.classList.add('active');
    const content = document.getElementById(target);
    if (content) content.classList.add('active');
  });
});
</script>
</body>
</html>
