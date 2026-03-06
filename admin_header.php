<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/m_contact.php';
require_once __DIR__ . '/m_booking.php';
requireAdmin();

$contactModel = new ContactModel();
$bookingModel = new BookingModel();
$unreadMessages = $contactModel->countUnread();
$pendingBookings = $bookingModel->countPending();

$currentAdminPage = basename($_SERVER['PHP_SELF']);
function adminActive(string $page): string {
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - Admin' : 'Admin Panel'; ?> | <?php echo SITE_NAME; ?></title>
  <link rel="stylesheet" href="<?php echo SITE_URL; ?>/admin_style.css">
  <style>@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap'); body{font-family:'Inter','Segoe UI',sans-serif;}</style>
</head>
<body>

<button class="admin-sidebar-toggle" id="sidebarToggle">☰</button>

<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-logo">
    <a href="<?php echo SITE_URL; ?>/admin.php">
      <div class="sidebar-logo-icon">✈</div>
      <span>WanderLux Admin</span>
    </a>
  </div>

  <nav class="sidebar-nav">
    <div class="sidebar-nav-section">
      <div class="sidebar-nav-label">Main</div>
      <a href="<?php echo SITE_URL; ?>/admin.php" class="<?php echo adminActive('admin.php'); ?>">
        <span class="nav-icon">📊</span> Dashboard
      </a>
    </div>

    <div class="sidebar-nav-section">
      <div class="sidebar-nav-label">Travel</div>
      <a href="<?php echo SITE_URL; ?>/admin_destinations.php" class="<?php echo adminActive('admin_destinations.php'); ?>">
        <span class="nav-icon">🗺</span> Destinations
      </a>
      <a href="<?php echo SITE_URL; ?>/admin_tours.php" class="<?php echo adminActive('admin_tours.php'); ?>">
        <span class="nav-icon">✈</span> Tours
      </a>
    </div>

    <div class="sidebar-nav-section">
      <div class="sidebar-nav-label">Management</div>
      <a href="<?php echo SITE_URL; ?>/admin_bookings.php" class="<?php echo adminActive('admin_bookings.php'); ?>">
        <span class="nav-icon">📋</span> Bookings
        <?php if ($pendingBookings > 0): ?>
          <span class="sidebar-badge"><?php echo $pendingBookings; ?></span>
        <?php endif; ?>
      </a>
      <a href="<?php echo SITE_URL; ?>/admin_users.php" class="<?php echo adminActive('admin_users.php'); ?>">
        <span class="nav-icon">👥</span> Users
      </a>
      <a href="<?php echo SITE_URL; ?>/admin_messages.php" class="<?php echo adminActive('admin_messages.php'); ?>">
        <span class="nav-icon">✉</span> Messages
        <?php if ($unreadMessages > 0): ?>
          <span class="sidebar-badge"><?php echo $unreadMessages; ?></span>
        <?php endif; ?>
      </a>
    </div>

    <div class="sidebar-nav-section">
      <div class="sidebar-nav-label">Account</div>
      <a href="<?php echo SITE_URL; ?>/index.php" target="_blank">
        <span class="nav-icon">🌐</span> View Site
      </a>
      <a href="<?php echo SITE_URL; ?>/logout.php">
        <span class="nav-icon">🚪</span> Logout
      </a>
    </div>
  </nav>

  <div class="sidebar-user">
    <div class="sidebar-user-avatar"><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></div>
    <div class="sidebar-user-info">
      <div class="sidebar-user-name"><?php echo e($_SESSION['user_name']); ?></div>
      <div class="sidebar-user-role">Administrator</div>
    </div>
  </div>
</aside>

<!-- Main -->
<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div class="topbar-title"><?php echo isset($pageTitle) ? e($pageTitle) : 'Dashboard'; ?></div>
    <div class="topbar-right">
      <a href="<?php echo SITE_URL; ?>/index.php" target="_blank" class="topbar-view-site">🌐 View Site</a>
      <?php if ($pendingBookings > 0): ?>
        <a href="<?php echo SITE_URL; ?>/admin_bookings.php" style="position:relative">
          <button class="topbar-btn" title="Pending bookings">📋</button>
          <span style="position:absolute;top:-4px;right:-4px;background:#ef4444;color:white;font-size:10px;font-weight:700;min-width:16px;height:16px;border-radius:8px;display:flex;align-items:center;justify-content:center"><?php echo $pendingBookings; ?></span>
        </a>
      <?php endif; ?>
      <?php if ($unreadMessages > 0): ?>
        <a href="<?php echo SITE_URL; ?>/admin_messages.php" style="position:relative">
          <button class="topbar-btn" title="Unread messages">✉</button>
          <span style="position:absolute;top:-4px;right:-4px;background:#ef4444;color:white;font-size:10px;font-weight:700;min-width:16px;height:16px;border-radius:8px;display:flex;align-items:center;justify-content:center"><?php echo $unreadMessages; ?></span>
        </a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Content -->
  <div class="admin-content">
    <?php echo flash('success'); echo flash('error'); ?>
