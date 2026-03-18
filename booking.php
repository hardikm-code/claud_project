<?php
require_once 'config.php';

$pkg_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pkg = null;

if ($pkg_id) {
    $stmt = $conn->prepare("SELECT * FROM packages WHERE id = ? AND is_active = 1");
    $stmt->bind_param("i", $pkg_id);
    $stmt->execute();
    $pkg = $stmt->get_result()->fetch_assoc();
}

// Fetch all packages for the dropdown
$all_packages = $conn->query("SELECT id, name, price, duration FROM packages WHERE is_active = 1 ORDER BY name");

// Handle form submission
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $p_id      = intval($_POST['package_id']);
    $fname     = trim($conn->real_escape_string($_POST['first_name']));
    $lname     = trim($conn->real_escape_string($_POST['last_name']));
    $email     = trim($conn->real_escape_string($_POST['email']));
    $phone     = trim($conn->real_escape_string($_POST['phone']));
    $date      = $conn->real_escape_string($_POST['travel_date']);
    $adults    = max(1, intval($_POST['num_adults']));
    $children  = max(0, intval($_POST['num_children']));
    $special   = $conn->real_escape_string(trim($_POST['special_requests'] ?? ''));

    // Validate
    if (!$fname || !$lname || !$email || !$phone || !$date) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strtotime($date) < time()) {
        $error = 'Travel date must be in the future.';
    } else {
        // Get package price
        $p_stmt = $conn->prepare("SELECT * FROM packages WHERE id = ?");
        $p_stmt->bind_param("i", $p_id);
        $p_stmt->execute();
        $pkg_data = $p_stmt->get_result()->fetch_assoc();

        if (!$pkg_data) {
            $error = 'Invalid package selected.';
        } else {
            $child_discount = 0.7; // Children pay 70% of adult price
            $total = ($adults * $pkg_data['price']) + ($children * $pkg_data['price'] * $child_discount);
            $ref   = 'WW-' . date('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

            $ins = $conn->prepare("INSERT INTO bookings (booking_ref, package_id, first_name, last_name, email, phone, travel_date, num_adults, num_children, special_requests, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $ins->bind_param("sisssssiisd", $ref, $p_id, $fname, $lname, $email, $phone, $date, $adults, $children, $special, $total);

            if ($ins->execute()) {
                header("Location: booking_success.php?ref=$ref");
                exit;
            } else {
                $error = 'Booking failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Tour - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="style.css">
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
            <li><a href="booking.php" class="nav-btn active">Book Now</a></li>
        </ul>
        <div class="menu-toggle" id="menuToggle"><span></span><span></span><span></span></div>
    </div>
</nav>

<!-- PAGE HERO -->
<div class="page-hero">
    <h1>Book Your Dream Tour</h1>
    <p>Fill in the details below and we'll confirm your booking within 24 hours</p>
    <div class="breadcrumb">
        <a href="index.php">Home</a><span>/</span>
        <a href="packages.php">Packages</a><span>/</span>
        <span>Book Now</span>
    </div>
</div>

<!-- BOOKING FORM -->
<section class="booking-form-section">
    <div class="booking-form-wrap">

        <?php if ($error): ?>
        <div class="alert alert-error">&#9888; <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-card">
            <div class="form-header">
                <h2>&#128197; Booking Details</h2>
                <p>All fields marked * are required</p>
            </div>
            <div class="form-body">

                <!-- Package Summary -->
                <?php if ($pkg): ?>
                <div class="package-summary-box">
                    <img src="<?= htmlspecialchars($pkg['image_url']) ?>" alt="<?= htmlspecialchars($pkg['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=200&q=80'">
                    <div>
                        <h4><?= htmlspecialchars($pkg['name']) ?></h4>
                        <p>&#128205; <?= htmlspecialchars($pkg['destination']) ?> &nbsp;|&nbsp; &#128197; <?= $pkg['duration'] ?> Days</p>
                        <p style="color:var(--primary);font-weight:800;margin-top:4px;">&#8377;<?= number_format($pkg['price']) ?> per person</p>
                    </div>
                </div>
                <?php endif; ?>

                <form method="POST" id="bookingForm" onsubmit="return validateForm()">
                    <!-- Package Selection -->
                    <div class="form-group">
                        <label for="package_id">Select Package *</label>
                        <select name="package_id" id="package_id" required onchange="updatePrice()">
                            <option value="">-- Choose a Package --</option>
                            <?php
                            $all_packages->data_seek(0);
                            while ($p = $all_packages->fetch_assoc()):
                            ?>
                            <option value="<?= $p['id'] ?>"
                                    data-price="<?= $p['price'] ?>"
                                    data-duration="<?= $p['duration'] ?>"
                                    <?= ($pkg && $pkg['id'] == $p['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?> - &#8377;<?= number_format($p['price']) ?>/person (<?= $p['duration'] ?> days)
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Personal Info -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" name="first_name" id="first_name" placeholder="Enter first name" required value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" name="last_name" id="last_name" placeholder="Enter last name" required value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" name="email" id="email" placeholder="your@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" name="phone" id="phone" placeholder="+91 XXXXXXXXXX" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="travel_date">Preferred Travel Date *</label>
                        <input type="date" name="travel_date" id="travel_date" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= htmlspecialchars($_POST['travel_date'] ?? '') ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="num_adults">Number of Adults *</label>
                            <select name="num_adults" id="num_adults" required onchange="updatePrice()">
                                <?php for ($i = 1; $i <= 20; $i++): ?>
                                <option value="<?= $i ?>" <?= (isset($_POST['num_adults']) && $_POST['num_adults'] == $i) ? 'selected' : ($i == 2 ? 'selected' : '') ?>><?= $i ?> Adult<?= $i > 1 ? 's' : '' ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="num_children">Number of Children</label>
                            <select name="num_children" id="num_children" onchange="updatePrice()">
                                <?php for ($i = 0; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>" <?= (isset($_POST['num_children']) && $_POST['num_children'] == $i) ? 'selected' : ($i == 0 ? 'selected' : '') ?>><?= $i ?> Child<?= $i > 1 ? 'ren' : ($i == 1 ? '' : 'ren') ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="special_requests">Special Requests / Notes</label>
                        <textarea name="special_requests" id="special_requests" placeholder="Any dietary requirements, accessibility needs, room preferences..."><?= htmlspecialchars($_POST['special_requests'] ?? '') ?></textarea>
                    </div>

                    <!-- Price Calculation -->
                    <div class="price-calc" id="priceCalc" style="display:none;">
                        <h4>&#128179; Price Breakdown</h4>
                        <div class="price-row">
                            <span id="adultLabel">Adults (2)</span>
                            <span id="adultPrice">&#8377;0</span>
                        </div>
                        <div class="price-row" id="childRow" style="display:none;">
                            <span id="childLabel">Children (0) @ 70%</span>
                            <span id="childPrice">&#8377;0</span>
                        </div>
                        <div class="price-row total">
                            <span>Total Amount</span>
                            <span id="totalPrice">&#8377;0</span>
                        </div>
                    </div>

                    <div style="background:var(--primary-light);border-radius:8px;padding:14px 18px;margin-bottom:20px;font-size:0.85rem;color:#065a50;">
                        &#128274; <strong>Secure Booking:</strong> Your booking is protected. We collect payment only after confirmation. Free cancellation 7+ days before travel date.
                    </div>

                    <button type="submit" class="submit-btn">
                        Confirm Booking &rarr;
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

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

<button class="scroll-top" id="scrollTop" title="Back to top" onclick="window.scrollTo({top:0,behavior:'smooth'})">&#8679;</button>

<script>
const menuToggle = document.getElementById('menuToggle');
const navLinks = document.getElementById('navLinks');
menuToggle.addEventListener('click', () => {
    navLinks.classList.toggle('open');
    menuToggle.classList.toggle('open');
});
window.addEventListener('scroll', () => {
    document.getElementById('scrollTop').classList.toggle('visible', window.scrollY > 400);
});

function formatINR(n) {
    return '&#8377;' + n.toLocaleString('en-IN');
}

function updatePrice() {
    const select   = document.getElementById('package_id');
    const option   = select.options[select.selectedIndex];
    const price    = parseFloat(option.getAttribute('data-price')) || 0;
    const adults   = parseInt(document.getElementById('num_adults').value) || 1;
    const children = parseInt(document.getElementById('num_children').value) || 0;

    if (!price) {
        document.getElementById('priceCalc').style.display = 'none';
        return;
    }

    const adultTotal = adults * price;
    const childTotal = children * price * 0.7;
    const total      = adultTotal + childTotal;

    document.getElementById('adultLabel').textContent = `Adults (${adults})`;
    document.getElementById('adultPrice').innerHTML = formatINR(adultTotal);
    document.getElementById('totalPrice').innerHTML = formatINR(total);

    const childRow = document.getElementById('childRow');
    if (children > 0) {
        document.getElementById('childLabel').textContent = `Children (${children}) @ 70%`;
        document.getElementById('childPrice').innerHTML = formatINR(childTotal);
        childRow.style.display = 'flex';
    } else {
        childRow.style.display = 'none';
    }

    document.getElementById('priceCalc').style.display = 'block';
}

function validateForm() {
    const date = document.getElementById('travel_date').value;
    if (new Date(date) <= new Date()) {
        alert('Please select a future travel date.');
        return false;
    }
    return true;
}

// Init price on load if package pre-selected
window.addEventListener('load', updatePrice);
</script>
</body>
</html>
