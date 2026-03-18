<?php
require_once 'config.php';

$ref = isset($_GET['ref']) ? $conn->real_escape_string($_GET['ref']) : '';
if (!$ref) { header('Location: index.php'); exit; }

$stmt = $conn->prepare("SELECT b.*, p.name as package_name, p.destination, p.duration, p.image_url FROM bookings b JOIN packages p ON b.package_id = p.id WHERE b.booking_ref = ?");
$stmt->bind_param("s", $ref);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) { header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        @keyframes popIn {
            0% { transform: scale(0.5); opacity: 0; }
            80% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        .success-icon { animation: popIn 0.6s ease; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .success-card { animation: fadeUp 0.5s ease 0.3s both; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar scrolled" id="navbar">
    <div class="navbar-inner">
        <a href="index.php" class="logo">Wander<span>World</span></a>
        <ul class="nav-links" id="navLinks">
            <li><a href="index.php">Home</a></li>
            <li><a href="packages.php">Packages</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="booking.php" class="nav-btn">Book Now</a></li>
        </ul>
        <div class="menu-toggle" id="menuToggle"><span></span><span></span><span></span></div>
    </div>
</nav>

<div style="min-height:100vh; background:var(--light-gray); padding:100px 20px 60px; display:flex; align-items:center; justify-content:center;">
    <div class="success-card" style="max-width:620px; width:100%; background:white; border-radius:16px; box-shadow:0 10px 40px rgba(0,0,0,0.12); overflow:hidden;">

        <!-- Green Header -->
        <div style="background:linear-gradient(135deg,#065f46,#059669); padding:40px; text-align:center; color:white;">
            <div class="success-icon" style="font-size:4.5rem; display:block; margin-bottom:16px;">&#9989;</div>
            <h1 style="font-size:1.8rem; font-weight:900; margin-bottom:8px;">Booking Confirmed!</h1>
            <p style="opacity:0.85;">Your adventure is officially booked. Get ready to explore!</p>
        </div>

        <!-- Booking Details -->
        <div style="padding:36px;">
            <!-- Reference Badge -->
            <div style="background:#ecfdf5; border:2px solid #a7f3d0; border-radius:10px; padding:16px 20px; text-align:center; margin-bottom:28px;">
                <div style="font-size:0.85rem; color:#065f46; font-weight:600; text-transform:uppercase; letter-spacing:1px;">Booking Reference</div>
                <div style="font-size:1.8rem; font-weight:900; color:#065f46; letter-spacing:2px; margin-top:4px;"><?= htmlspecialchars($booking['booking_ref']) ?></div>
                <div style="font-size:0.8rem; color:#6c757d; margin-top:4px;">Save this for your records</div>
            </div>

            <!-- Package Info -->
            <div style="display:flex; gap:16px; background:var(--light-gray); border-radius:10px; padding:16px; margin-bottom:24px; align-items:center;">
                <img src="<?= htmlspecialchars($booking['image_url']) ?>" style="width:80px; height:60px; object-fit:cover; border-radius:8px; flex-shrink:0;" alt="Package" onerror="this.src='https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=200&q=80'">
                <div>
                    <div style="font-weight:800; color:#1a1a2e; font-size:1rem;"><?= htmlspecialchars($booking['package_name']) ?></div>
                    <div style="font-size:0.85rem; color:#6c757d; margin-top:4px;">&#128205; <?= htmlspecialchars($booking['destination']) ?> &nbsp;|&nbsp; &#128197; <?= $booking['duration'] ?> Days</div>
                </div>
            </div>

            <!-- Details Table -->
            <div class="booking-details">
                <div class="row"><span>Traveler Name</span><strong><?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?></strong></div>
                <div class="row"><span>Email</span><strong><?= htmlspecialchars($booking['email']) ?></strong></div>
                <div class="row"><span>Phone</span><strong><?= htmlspecialchars($booking['phone']) ?></strong></div>
                <div class="row"><span>Travel Date</span><strong><?= date('d M Y', strtotime($booking['travel_date'])) ?></strong></div>
                <div class="row"><span>Adults</span><strong><?= $booking['num_adults'] ?></strong></div>
                <?php if ($booking['num_children'] > 0): ?>
                <div class="row"><span>Children</span><strong><?= $booking['num_children'] ?></strong></div>
                <?php endif; ?>
                <div class="row"><span>Total Amount</span><strong style="color:var(--primary); font-size:1.1rem;">&#8377;<?= number_format($booking['total_price']) ?></strong></div>
                <div class="row"><span>Status</span><strong style="color:#059669;">&#10003; <?= $booking['status'] ?></strong></div>
                <div class="row"><span>Booked On</span><strong><?= date('d M Y, h:i A', strtotime($booking['created_at'])) ?></strong></div>
            </div>

            <!-- What Next -->
            <div style="background:#eff6ff; border-radius:10px; padding:18px; margin-bottom:24px;">
                <div style="font-weight:800; color:#1e40af; margin-bottom:12px;">&#128276; What Happens Next?</div>
                <ul style="list-style:none; display:flex; flex-direction:column; gap:8px;">
                    <li style="font-size:0.88rem; color:#3730a3; display:flex; gap:8px;">&#9312; Our team will review your booking and confirm within 24 hours.</li>
                    <li style="font-size:0.88rem; color:#3730a3; display:flex; gap:8px;">&#9313; You'll receive a confirmation email with full tour details.</li>
                    <li style="font-size:0.88rem; color:#3730a3; display:flex; gap:8px;">&#9314; Payment instructions will be sent to your email.</li>
                    <li style="font-size:0.88rem; color:#3730a3; display:flex; gap:8px;">&#9315; Pack your bags and get ready for an amazing adventure!</li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <a href="packages.php" class="btn-primary" style="flex:1; text-align:center;">&#128269; Explore More Packages</a>
                <a href="index.php" class="btn-outline" style="flex:1; text-align:center; color:#333; border-color:#ddd;">&#127968; Back to Home</a>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-grid">
        <div class="footer-brand">
            <span class="logo">Wander<span>World</span></span>
            <p>Creating extraordinary travel experiences since 2015.</p>
            <div class="social-links"><a href="#">f</a><a href="#">in</a><a href="#">tw</a><a href="#">yt</a></div>
        </div>
        <div class="footer-col"><h4>Quick Links</h4><ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="packages.php">All Packages</a></li>
            <li><a href="contact.php">Contact Us</a></li>
        </ul></div>
        <div class="footer-col"><h4>Categories</h4><ul>
            <?php foreach (['Beach','Adventure','Cultural','Wildlife','Mountain','Luxury'] as $c): ?>
            <li><a href="packages.php?category=<?= $c ?>"><?= $c ?></a></li>
            <?php endforeach; ?>
        </ul></div>
        <div class="footer-col"><h4>Contact</h4><ul>
            <li><a href="mailto:info@wanderworld.com">info@wanderworld.com</a></li>
            <li><a href="tel:+911800123456">+91 1800-123-456</a></li>
        </ul></div>
    </div>
    <div class="footer-bottom">
        <span>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</span>
        <span>Made with &#10084; for curious travelers</span>
    </div>
</footer>

<script>
const menuToggle = document.getElementById('menuToggle');
const navLinks = document.getElementById('navLinks');
menuToggle.addEventListener('click', () => {
    navLinks.classList.toggle('open');
    menuToggle.classList.toggle('open');
});
</script>
</body>
</html>
