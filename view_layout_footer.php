<!-- ── Footer ──────────────────────────────────────────────────────── -->
<footer class="footer" id="contact">
  <div class="footer-top">
    <div class="container">
      <div class="footer-grid">

        <!-- Brand -->
        <div class="footer-col">
          <div class="footer-logo">BELLA<span> VISTA</span></div>
          <p class="footer-tagline">Fine Dining Experience</p>
          <p class="footer-desc">An unforgettable culinary journey crafted with passion, premium ingredients, and unparalleled attention to detail.</p>
          <div class="footer-social">
            <a href="#" aria-label="Facebook">f</a>
            <a href="#" aria-label="Instagram">in</a>
            <a href="#" aria-label="Twitter">tw</a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="footer-col">
          <h4 class="footer-heading">Quick Links</h4>
          <ul class="footer-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php?page=menu">Our Menu</a></li>
            <li><a href="index.php?page=reservation">Reservations</a></li>
            <li><a href="#about">About Us</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </div>

        <!-- Opening Hours -->
        <div class="footer-col">
          <h4 class="footer-heading">Opening Hours</h4>
          <ul class="footer-hours">
            <li><span>Monday – Thursday</span><span>11:00 – 22:00</span></li>
            <li><span>Friday – Saturday</span><span>11:00 – 23:00</span></li>
            <li><span>Sunday</span><span>12:00 – 21:00</span></li>
          </ul>
        </div>

        <!-- Contact Info -->
        <div class="footer-col">
          <h4 class="footer-heading">Contact Us</h4>
          <ul class="footer-contact">
            <li>
              <span class="fc-icon">📍</span>
              <span>123 Gourmet Avenue<br>New York, NY 10001</span>
            </li>
            <li>
              <span class="fc-icon">📞</span>
              <a href="tel:5551234567">(555) 123-4567</a>
            </li>
            <li>
              <span class="fc-icon">✉</span>
              <a href="mailto:info@bellavista.com">info@bellavista.com</a>
            </li>
          </ul>
        </div>

      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="container">
      <p>&copy; <?= date('Y') ?> Bella Vista. All rights reserved.</p>
      <p><a href="admin.php">Staff Login</a></p>
    </div>
  </div>
</footer>

<!-- ── Back to Top ──────────────────────────────────────────────── -->
<button class="back-to-top" id="backToTop" aria-label="Back to top">&#8679;</button>

<!-- ── Scripts ──────────────────────────────────────────────────── -->
<script>
// Sticky navbar
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 60);
});

// Mobile nav toggle
const navToggle = document.getElementById('navToggle');
const navLinks  = document.getElementById('navLinks');
navToggle.addEventListener('click', () => {
  navLinks.classList.toggle('open');
  navToggle.classList.toggle('open');
});

// Close nav on link click
navLinks.querySelectorAll('a').forEach(link => {
  link.addEventListener('click', () => {
    navLinks.classList.remove('open');
    navToggle.classList.remove('open');
  });
});

// Back to top
const backBtn = document.getElementById('backToTop');
window.addEventListener('scroll', () => {
  backBtn.classList.toggle('visible', window.scrollY > 400);
});
backBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
</script>

</body>
</html>
