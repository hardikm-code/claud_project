<!-- ── PAGE BANNER ───────────────────────────────────────────────── -->
<section class="page-banner">
  <div class="page-banner-overlay"></div>
  <div class="page-banner-content">
    <p class="section-label">Bella Vista</p>
    <h1>Make a <em>Reservation</em></h1>
    <p class="breadcrumb"><a href="index.php">Home</a> / <span>Reservations</span></p>
  </div>
</section>

<!-- ── RESERVATION SECTION ──────────────────────────────────────── -->
<section class="reservation-section section-pad">
  <div class="container">
    <div class="reservation-grid">

      <!-- ── Form Column ── -->
      <div class="res-form-col">
        <div class="res-form-card">
          <p class="section-label">Reserve Online</p>
          <h2 class="section-title res-title">Book Your <em>Table</em></h2>

          <?php if ($success): ?>
          <!-- Success Message -->
          <div class="res-success">
            <div class="res-success-icon">✓</div>
            <h3>Reservation Confirmed!</h3>
            <p>Thank you for your reservation request. We'll contact you shortly to confirm your booking.</p>
            <a href="index.php" class="btn btn-gold" style="margin-top:20px">Back to Home</a>
          </div>

          <?php else: ?>

          <?php if (!empty($errors)): ?>
          <div class="res-errors">
            <ul><?php foreach ($errors as $e): ?><li><?= clean($e) ?></li><?php endforeach; ?></ul>
          </div>
          <?php endif; ?>

          <form method="POST" action="index.php?page=reservation" class="res-form" id="resForm">
            <div class="form-row">
              <div class="form-group">
                <label for="name">Full Name <span class="req">*</span></label>
                <input type="text" id="name" name="name"
                       value="<?= clean($form_data['name'] ?? '') ?>"
                       placeholder="John Doe" required>
              </div>
              <div class="form-group">
                <label for="email">Email Address <span class="req">*</span></label>
                <input type="email" id="email" name="email"
                       value="<?= clean($form_data['email'] ?? '') ?>"
                       placeholder="john@example.com" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="phone">Phone Number <span class="req">*</span></label>
                <input type="tel" id="phone" name="phone"
                       value="<?= clean($form_data['phone'] ?? '') ?>"
                       placeholder="(555) 123-4567" required>
              </div>
              <div class="form-group">
                <label for="guests">Number of Guests <span class="req">*</span></label>
                <select id="guests" name="guests" required>
                  <?php for ($i = 1; $i <= 20; $i++): ?>
                  <option value="<?= $i ?>" <?= ($form_data['guests'] ?? 2) == $i ? 'selected' : '' ?>>
                    <?= $i ?> <?= $i === 1 ? 'Guest' : 'Guests' ?>
                  </option>
                  <?php endfor; ?>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="date">Preferred Date <span class="req">*</span></label>
                <input type="date" id="date" name="date"
                       value="<?= clean($form_data['date'] ?? '') ?>"
                       min="<?= date('Y-m-d') ?>" required>
              </div>
              <div class="form-group">
                <label for="time">Preferred Time <span class="req">*</span></label>
                <select id="time" name="time" required>
                  <option value="">-- Select Time --</option>
                  <?php
                  $times = ['11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30',
                            '17:00','17:30','18:00','18:30','19:00','19:30','20:00','20:30','21:00','21:30'];
                  foreach ($times as $t):
                    $selected = ($form_data['time'] ?? '') === $t ? 'selected' : '';
                    $display  = date('g:i A', strtotime($t));
                  ?>
                  <option value="<?= $t ?>" <?= $selected ?>><?= $display ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="special_requests">Special Requests</label>
              <textarea id="special_requests" name="special_requests"
                        rows="4"
                        placeholder="Dietary requirements, celebrations, seating preferences..."><?= clean($form_data['special_requests'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-gold btn-block">
              Confirm Reservation
            </button>
          </form>
          <?php endif; ?>
        </div>
      </div>

      <!-- ── Info Column ── -->
      <div class="res-info-col">
        <div class="res-info-block">
          <h3>Opening Hours</h3>
          <ul class="res-hours">
            <li><span>Monday – Thursday</span><span>11:00 AM – 10:00 PM</span></li>
            <li><span>Friday – Saturday</span><span>11:00 AM – 11:00 PM</span></li>
            <li><span>Sunday</span><span>12:00 PM – 9:00 PM</span></li>
          </ul>
        </div>

        <div class="res-info-block">
          <h3>Contact Us</h3>
          <div class="res-contact-item">
            <span class="rc-icon">📞</span>
            <span>(555) 123-4567</span>
          </div>
          <div class="res-contact-item">
            <span class="rc-icon">✉</span>
            <span>info@bellavista.com</span>
          </div>
          <div class="res-contact-item">
            <span class="rc-icon">📍</span>
            <span>123 Gourmet Avenue<br>New York, NY 10001</span>
          </div>
        </div>

        <div class="res-info-block">
          <h3>Reservation Policy</h3>
          <ul class="res-policy">
            <li>Reservations are held for 15 minutes past the booking time.</li>
            <li>For parties of 8+, please call us directly.</li>
            <li>Cancellations must be made 24 hours in advance.</li>
            <li>A credit card may be required for large group bookings.</li>
          </ul>
        </div>

        <div class="res-cta-phone">
          <p>Prefer to call?</p>
          <a href="tel:5551234567" class="btn btn-gold btn-block">(555) 123-4567</a>
        </div>
      </div>

    </div>
  </div>
</section>

<script>
// Set minimum date to today
document.getElementById('date').min = new Date().toISOString().split('T')[0];

// Basic form validation
document.getElementById('resForm') && document.getElementById('resForm').addEventListener('submit', function(e) {
  const date = new Date(document.getElementById('date').value);
  const today = new Date(); today.setHours(0,0,0,0);
  if (date < today) {
    e.preventDefault();
    alert('Please select a future date.');
  }
});
</script>
