<?php
require_once 'config.php';
$pageTitle = 'Reservations';
$pageDesc  = 'Book a table at La Bella Cucina. Reserve online for the best Italian dining experience in New York.';
include 'header.php';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_submit'])) {
    $name     = sanitize($_POST['res_name'] ?? '');
    $email    = sanitize($_POST['res_email'] ?? '');
    $phone    = sanitize($_POST['res_phone'] ?? '');
    $date     = sanitize($_POST['res_date'] ?? '');
    $time     = sanitize($_POST['res_time'] ?? '');
    $guests   = (int)($_POST['res_guests'] ?? 0);
    $special  = sanitize($_POST['res_special'] ?? '');

    $errors = [];

    if (!$name)  $errors[] = 'Name is required.';
    if (!$email || !filter_var($_POST['res_email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (!$phone) $errors[] = 'Phone number is required.';
    if (!$date)  $errors[] = 'Date is required.';
    if (!$time)  $errors[] = 'Time is required.';
    if ($guests < 1 || $guests > 20) $errors[] = 'Please enter a valid number of guests (1-20).';

    // Check date is not in the past
    if ($date && strtotime($date) < strtotime('today')) {
        $errors[] = 'Reservation date cannot be in the past.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO reservations (name, email, phone, date, time, guests, special_requests) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $name, $email, $phone, $date, $time, $guests, $special);
        // Fix: bind_param expects int for 'i', not string
        $stmt->bind_param("sssssis", $name, $email, $phone, $date, $time, $guests, $special);
        if ($stmt->execute()) {
            $msg = '<div class="alert alert-success">
                <strong>Reservation Submitted!</strong><br>
                Thank you, ' . htmlspecialchars($name) . '. We\'ve received your reservation for ' . htmlspecialchars($date) . ' at ' . htmlspecialchars($time) . ' for ' . $guests . ' guest(s). We will confirm shortly.
            </div>';
        } else {
            $msg = '<div class="alert alert-error">Something went wrong. Please try again or call us directly.</div>';
        }
        $stmt->close();
    } else {
        $msg = '<div class="alert alert-error">' . implode('<br>', $errors) . '</div>';
    }
}

// Time slots
$timeSlots = [
    '12:00', '12:30', '13:00', '13:30', '14:00', '14:30',
    '17:00', '17:30', '18:00', '18:30', '19:00', '19:30',
    '20:00', '20:30', '21:00', '21:30'
];
?>

<div class="res-page-hero">
    <h1>Book Your Table</h1>
    <p>Reserve your dining experience online &mdash; quick, easy and instant.</p>
</div>

<section class="reservations-section" style="padding: 80px 24px;">
    <div class="container">
        <div class="reservation-grid">
            <!-- Info -->
            <div class="reservation-info">
                <span class="section-eyebrow">Visit Us</span>
                <h2>Plan Your Visit</h2>
                <p>We look forward to welcoming you. Whether it's an intimate dinner, a family gathering, or a business lunch, we'll make it memorable.</p>

                <ul class="info-list">
                    <li>
                        <div class="info-icon">&#128205;</div>
                        <div><strong>Location</strong><br>123 Via Roma, Suite 1, New York, NY 10001</div>
                    </li>
                    <li>
                        <div class="info-icon">&#128222;</div>
                        <div><strong>Phone</strong><br>+1 (212) 555-0100</div>
                    </li>
                    <li>
                        <div class="info-icon">&#128336;</div>
                        <div>
                            <strong>Opening Hours</strong><br>
                            Mon &ndash; Thu: 12:00 PM &ndash; 10:00 PM<br>
                            Fri &ndash; Sat: 12:00 PM &ndash; 11:00 PM<br>
                            Sun: 12:00 PM &ndash; 9:00 PM
                        </div>
                    </li>
                    <li>
                        <div class="info-icon">&#128101;</div>
                        <div><strong>Group Size</strong><br>We accommodate groups of 1 to 20 guests. For larger events, please call us.</div>
                    </li>
                </ul>

                <div style="margin-top: 32px; padding: 20px; background: var(--cream); border-radius: var(--radius); border-left: 4px solid var(--gold);">
                    <strong>Private Dining</strong>
                    <p style="color: var(--gray); margin-top: 8px; font-size: 0.9rem;">Planning a special occasion? Ask about our private dining room available for groups of 10-30 guests.</p>
                </div>
            </div>

            <!-- Form -->
            <div>
                <?php echo $msg; ?>

                <div class="form-card">
                    <h3 style="font-size: 1.3rem; margin-bottom: 24px;">Reservation Details</h3>
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Full Name *</label>
                                <input type="text" name="res_name" placeholder="John Smith" required
                                       value="<?php echo isset($_POST['res_name']) ? htmlspecialchars($_POST['res_name']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>Email Address *</label>
                                <input type="email" name="res_email" placeholder="john@example.com" required
                                       value="<?php echo isset($_POST['res_email']) ? htmlspecialchars($_POST['res_email']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Phone Number *</label>
                                <input type="tel" name="res_phone" placeholder="+1 (212) 555-0000" required
                                       value="<?php echo isset($_POST['res_phone']) ? htmlspecialchars($_POST['res_phone']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>Number of Guests *</label>
                                <select name="res_guests" required>
                                    <option value="">Select guests</option>
                                    <?php for ($i = 1; $i <= 20; $i++): ?>
                                    <option value="<?php echo $i; ?>"
                                        <?php echo (isset($_POST['res_guests']) && $_POST['res_guests'] == $i) ? 'selected' : ''; ?>>
                                        <?php echo $i; ?> <?php echo $i === 1 ? 'Guest' : 'Guests'; ?>
                                    </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Preferred Date *</label>
                                <input type="date" name="res_date" required
                                       min="<?php echo date('Y-m-d'); ?>"
                                       value="<?php echo isset($_POST['res_date']) ? htmlspecialchars($_POST['res_date']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>Preferred Time *</label>
                                <select name="res_time" required>
                                    <option value="">Select time</option>
                                    <optgroup label="Lunch">
                                        <?php foreach ($timeSlots as $slot): if ($slot < '15:00'): ?>
                                        <option value="<?php echo $slot; ?>"
                                            <?php echo (isset($_POST['res_time']) && $_POST['res_time'] === $slot) ? 'selected' : ''; ?>>
                                            <?php echo date('g:i A', strtotime($slot)); ?>
                                        </option>
                                        <?php endif; endforeach; ?>
                                    </optgroup>
                                    <optgroup label="Dinner">
                                        <?php foreach ($timeSlots as $slot): if ($slot >= '17:00'): ?>
                                        <option value="<?php echo $slot; ?>"
                                            <?php echo (isset($_POST['res_time']) && $_POST['res_time'] === $slot) ? 'selected' : ''; ?>>
                                            <?php echo date('g:i A', strtotime($slot)); ?>
                                        </option>
                                        <?php endif; endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Special Requests <span style="font-weight:400; color:var(--gray)">(optional)</span></label>
                            <textarea name="res_special" placeholder="Dietary requirements, celebrations, seating preferences..."><?php echo isset($_POST['res_special']) ? htmlspecialchars($_POST['res_special']) : ''; ?></textarea>
                        </div>

                        <button type="submit" name="reservation_submit" class="btn btn-primary" style="width:100%; font-size:1rem;">Confirm Reservation</button>
                        <p style="text-align:center; font-size:0.8rem; color:var(--gray); margin-top:12px;">We will confirm your booking within 2 hours by email or phone.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
