<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - ' . SITE_NAME : SITE_NAME; ?></title>
  <meta name="description" content="<?php echo isset($pageDesc) ? e($pageDesc) : 'Discover the world with WanderLux Travel. Unforgettable tours, luxury destinations, and expertly crafted travel experiences.'; ?>">
  <link rel="stylesheet" href="<?php echo SITE_URL; ?>/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    body { font-family: 'Inter', 'Segoe UI', sans-serif; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar" id="navbar">
  <div class="nav-inner">
    <a href="<?php echo SITE_URL; ?>/index.php" class="nav-logo">
      <div class="nav-logo-icon">✈</div>
      <span class="nav-logo-text"><?php echo SITE_NAME; ?></span>
    </a>

    <ul class="nav-menu" id="navMenu">
      <li><a href="<?php echo SITE_URL; ?>/index.php" class="nav-link <?php echo isActive('index.php'); ?>">Home</a></li>
      <li><a href="<?php echo SITE_URL; ?>/destinations.php" class="nav-link <?php echo isActive('destinations.php'); ?>">Destinations</a></li>
      <li><a href="<?php echo SITE_URL; ?>/tours.php" class="nav-link <?php echo isActive('tours.php'); ?>">Tours</a></li>
      <li><a href="<?php echo SITE_URL; ?>/gallery.php" class="nav-link <?php echo isActive('gallery.php'); ?>">Gallery</a></li>
      <li><a href="<?php echo SITE_URL; ?>/about.php" class="nav-link <?php echo isActive('about.php'); ?>">About</a></li>
      <li><a href="<?php echo SITE_URL; ?>/contact.php" class="nav-link <?php echo isActive('contact.php'); ?>">Contact</a></li>

      <div class="nav-actions">
        <?php if (isLoggedIn()): ?>
          <div class="nav-user">
            <span class="nav-user-name">👤 <?php echo e($_SESSION['user_name']); ?></span>
          </div>
          <?php if (isAdmin()): ?>
            <a href="<?php echo SITE_URL; ?>/admin.php" class="btn btn-accent btn-sm">Admin</a>
          <?php endif; ?>
          <a href="<?php echo SITE_URL; ?>/my_bookings.php" class="btn btn-outline-white btn-sm">My Bookings</a>
          <a href="<?php echo SITE_URL; ?>/logout.php" class="btn btn-outline-white btn-sm">Logout</a>
        <?php else: ?>
          <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-outline-white btn-sm">Login</a>
          <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-accent btn-sm">Register</a>
        <?php endif; ?>
      </div>
    </ul>

    <div class="nav-actions" style="display:flex">
      <?php if (isLoggedIn()): ?>
        <span class="nav-user-name" style="margin-right:8px">👤 <?php echo e($_SESSION['user_name']); ?></span>
        <?php if (isAdmin()): ?>
          <a href="<?php echo SITE_URL; ?>/admin.php" class="btn btn-accent btn-sm" style="margin-right:6px">Admin</a>
        <?php endif; ?>
        <a href="<?php echo SITE_URL; ?>/my_bookings.php" class="btn btn-outline-white btn-sm" style="margin-right:6px">My Bookings</a>
        <a href="<?php echo SITE_URL; ?>/logout.php" class="btn btn-outline-white btn-sm">Logout</a>
      <?php else: ?>
        <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-outline-white btn-sm" style="margin-right:8px">Login</a>
        <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-accent btn-sm">Register</a>
      <?php endif; ?>
      <button class="nav-toggle" id="navToggle" style="margin-left:12px">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</nav>

<script>
// Navbar scroll effect
window.addEventListener('scroll', function() {
  const nav = document.getElementById('navbar');
  if (window.scrollY > 50) { nav.classList.add('scrolled'); }
  else { nav.classList.remove('scrolled'); }
});
// Mobile toggle
document.getElementById('navToggle').addEventListener('click', function() {
  document.getElementById('navMenu').classList.toggle('open');
});
</script>
