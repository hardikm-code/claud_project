<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard – Bella Vista Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">

<?php require 'view_admin_sidebar.php'; ?>

<div class="admin-main">
  <?php require 'view_admin_topbar.php'; ?>

  <div class="admin-content">
    <div class="page-header">
      <h1>Dashboard</h1>
      <p>Welcome back, <strong><?= clean($_SESSION['admin_username']) ?></strong></p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card stat-blue">
        <div class="stat-icon">📋</div>
        <div class="stat-info">
          <span class="stat-value"><?= $stats['total_reservations'] ?></span>
          <span class="stat-label">Total Reservations</span>
        </div>
      </div>
      <div class="stat-card stat-yellow">
        <div class="stat-icon">⏳</div>
        <div class="stat-info">
          <span class="stat-value"><?= $stats['pending'] ?></span>
          <span class="stat-label">Pending</span>
        </div>
      </div>
      <div class="stat-card stat-green">
        <div class="stat-icon">✓</div>
        <div class="stat-info">
          <span class="stat-value"><?= $stats['confirmed'] ?></span>
          <span class="stat-label">Confirmed</span>
        </div>
      </div>
      <div class="stat-card stat-gold">
        <div class="stat-icon">📅</div>
        <div class="stat-info">
          <span class="stat-value"><?= $stats['today'] ?></span>
          <span class="stat-label">Today's Bookings</span>
        </div>
      </div>
      <div class="stat-card stat-purple">
        <div class="stat-icon">🍽</div>
        <div class="stat-info">
          <span class="stat-value"><?= $stats['menu_items'] ?></span>
          <span class="stat-label">Menu Items</span>
        </div>
      </div>
      <div class="stat-card stat-red">
        <div class="stat-icon">✗</div>
        <div class="stat-info">
          <span class="stat-value"><?= $stats['cancelled'] ?></span>
          <span class="stat-label">Cancelled</span>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
      <h2>Quick Actions</h2>
      <div class="qa-grid">
        <a href="admin.php?action=menu_add" class="qa-card">
          <span class="qa-icon">+</span>
          <span>Add Menu Item</span>
        </a>
        <a href="admin.php?action=reservations" class="qa-card">
          <span class="qa-icon">📋</span>
          <span>View Reservations</span>
        </a>
        <a href="admin.php?action=reservations&status=pending" class="qa-card qa-highlight">
          <span class="qa-icon">⚠</span>
          <span>Pending (<?= $stats['pending'] ?>)</span>
        </a>
        <a href="index.php" class="qa-card" target="_blank">
          <span class="qa-icon">🌐</span>
          <span>View Website</span>
        </a>
      </div>
    </div>

    <!-- Recent Reservations -->
    <div class="table-section">
      <div class="table-header">
        <h2>Recent Reservations</h2>
        <a href="admin.php?action=reservations" class="btn-admin-sm">View All</a>
      </div>
      <?php if (!empty($recent_reservations)): ?>
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Guest</th>
              <th>Date & Time</th>
              <th>Guests</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recent_reservations as $res): ?>
            <tr>
              <td><?= $res['id'] ?></td>
              <td>
                <div class="guest-info">
                  <strong><?= clean($res['name']) ?></strong>
                  <small><?= clean($res['email']) ?></small>
                </div>
              </td>
              <td><?= date('M d, Y', strtotime($res['date'])) ?> at <?= date('g:i A', strtotime($res['time'])) ?></td>
              <td><?= $res['guests'] ?></td>
              <td><span class="status-badge status-<?= $res['status'] ?>"><?= ucfirst($res['status']) ?></span></td>
              <td>
                <a href="admin.php?action=reservations" class="btn-admin-xs">Manage</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <div class="empty-state">
        <p>No reservations yet.</p>
        <a href="index.php?page=reservation" target="_blank" class="btn-admin-sm">Test Reservation Form</a>
      </div>
      <?php endif; ?>
    </div>

  </div><!-- /admin-content -->
</div><!-- /admin-main -->

</body>
</html>
