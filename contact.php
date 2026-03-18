<?php
require_once 'config.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($conn->real_escape_string($_POST['name']));
    $email   = trim($conn->real_escape_string($_POST['email']));
    $phone   = trim($conn->real_escape_string($_POST['phone'] ?? ''));
    $subject = trim($conn->real_escape_string($_POST['subject'] ?? ''));
    $message = trim($conn->real_escape_string($_POST['message']));

    if (!$name || !$email || !$message) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
        if ($stmt->execute()) {
            $success = "Thank you, $name! We've received your message and will get back to you within 24 hours.";
        } else {
            $error = 'Something went wrong. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - <?= SITE_NAME ?></title>
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
            <li><a href="contact.php" class="active">Contact</a></li>
            <li><a href="booking.php" class="nav-btn">Book Now</a></li>
        </ul>
        <div class="menu-toggle" id="menuToggle"><span></span><span></span><span></span></div>
    </div>
</nav>

<!-- PAGE HERO -->
<div class="page-hero">
    <h1>Get In Touch</h1>
    <p>Have a question or need help planning your trip? We're here for you 24/7.</p>
    <div class="breadcrumb">
        <a href="index.php">Home</a><span>/</span><span>Contact</span>
    </div>
</div>

<!-- CONTACT SECTION -->
<section class="contact-section">
    <div class="contact-grid">

        <!-- Contact Info -->
        <div class="contact-info">
            <h3>Let's Plan Your Perfect Trip</h3>
            <p>Whether you're looking for a relaxing beach holiday, an adrenaline-filled adventure, or a cultural immersion — our travel experts are ready to help you craft the perfect itinerary.</p>

            <div class="contact-cards">
                <div class="contact-card">
                    <div class="contact-card-icon">&#128222;</div>
                    <div>
                        <h4>Call Us</h4>
                        <p>+91 1800-123-456 (Toll Free)</p>
                        <p style="font-size:0.82rem;color:#999;">Mon–Sat: 9:00 AM – 7:00 PM IST</p>
                    </div>
                </div>
                <div class="contact-card">
                    <div class="contact-card-icon">&#128140;</div>
                    <div>
                        <h4>Email Us</h4>
                        <p>info@wanderworld.com</p>
                        <p style="font-size:0.82rem;color:#999;">We reply within 24 hours</p>
                    </div>
                </div>
                <div class="contact-card">
                    <div class="contact-card-icon">&#128205;</div>
                    <div>
                        <h4>Visit Our Office</h4>
                        <p>1st Floor, Travel House, MG Road</p>
                        <p>Mumbai, Maharashtra 400001</p>
                    </div>
                </div>
                <div class="contact-card">
                    <div class="contact-card-icon">&#128172;</div>
                    <div>
                        <h4>WhatsApp</h4>
                        <p>+91 98765-43210</p>
                        <p style="font-size:0.82rem;color:#999;">Quick response guaranteed</p>
                    </div>
                </div>
            </div>

            <!-- Map Placeholder -->
            <div style="margin-top:28px; background:var(--light-gray); border-radius:var(--radius); height:200px; display:flex; align-items:center; justify-content:center; color:#6c757d; font-size:0.9rem;">
                <div style="text-align:center;">
                    <div style="font-size:2.5rem; margin-bottom:8px;">&#127758;</div>
                    <div>MG Road, Mumbai, Maharashtra</div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div>
            <?php if ($success): ?>
            <div class="alert alert-success" style="margin-bottom:20px;">
                <strong>&#9989;</strong> <?= htmlspecialchars($success) ?>
            </div>
            <?php endif; ?>
            <?php if ($error): ?>
            <div class="alert alert-error">&#9888; <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="form-card">
                <div class="form-header">
                    <h2>&#9993; Send Us a Message</h2>
                    <p>We'll get back to you within 24 hours</p>
                </div>
                <div class="form-body">
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Your Name *</label>
                                <input type="text" name="name" placeholder="Full name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Email Address *</label>
                                <input type="email" name="email" placeholder="your@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="tel" name="phone" placeholder="+91 XXXXXXXXXX" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Subject</label>
                                <select name="subject">
                                    <option value="">Select a topic</option>
                                    <option value="Package Inquiry" <?= (($_POST['subject'] ?? '') == 'Package Inquiry') ? 'selected' : '' ?>>Package Inquiry</option>
                                    <option value="Booking Help"    <?= (($_POST['subject'] ?? '') == 'Booking Help')    ? 'selected' : '' ?>>Booking Help</option>
                                    <option value="Custom Tour"     <?= (($_POST['subject'] ?? '') == 'Custom Tour')     ? 'selected' : '' ?>>Custom Tour</option>
                                    <option value="Cancellation"    <?= (($_POST['subject'] ?? '') == 'Cancellation')    ? 'selected' : '' ?>>Cancellation/Refund</option>
                                    <option value="Other"           <?= (($_POST['subject'] ?? '') == 'Other')           ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Message *</label>
                            <textarea name="message" rows="5" placeholder="Tell us about your dream destination, travel dates, group size, budget or any questions..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="submit-btn">Send Message &#8594;</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQs -->
<section class="section section-alt">
    <div class="section-header">
        <span class="section-tag">Common Questions</span>
        <h2>Frequently Asked Questions</h2>
        <div class="divider"></div>
    </div>
    <div style="max-width:800px; margin:0 auto;">
        <?php
        $faqs = [
            ['q' => 'How far in advance should I book a tour?', 'a' => 'We recommend booking at least 4–6 weeks in advance for popular destinations, especially during peak season (Oct–Feb). For budget-friendly deals, booking 3+ months ahead is ideal.'],
            ['q' => 'What is your cancellation policy?', 'a' => 'Free cancellation up to 7 days before travel. 50% refund for cancellations 3–7 days prior. No refund within 3 days of departure, though you may reschedule.'],
            ['q' => 'Is travel insurance included in the package?', 'a' => 'Basic travel insurance is included in most packages. We strongly recommend purchasing comprehensive cover for medical emergencies and trip interruptions.'],
            ['q' => 'Can I customize a tour package?', 'a' => 'Absolutely! Contact our team to design a fully customized itinerary based on your interests, budget, travel dates, and group size.'],
            ['q' => 'What payment methods do you accept?', 'a' => 'We accept bank transfers, UPI, credit/debit cards, and EMI options. A 25% advance secures your booking with the balance due 14 days before travel.'],
        ];
        foreach ($faqs as $i => $faq):
        ?>
        <div class="faq-item" id="faq-<?= $i ?>">
            <div class="faq-question" onclick="toggleFaq('faq-<?= $i ?>')">
                <?= htmlspecialchars($faq['q']) ?>
                <span class="faq-icon">+</span>
            </div>
            <div class="faq-answer"><?= htmlspecialchars($faq['a']) ?></div>
        </div>
        <?php endforeach; ?>
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

function toggleFaq(id) {
    const item = document.getElementById(id);
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(el => el.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
}
</script>
</body>
</html>
