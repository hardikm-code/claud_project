<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservations – Bella Vista Admin</title>
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
      <div>
        <h1>Reservations</h1>
        <p>Manage all restaurant bookings</p>
      </div>
    </div>

    <!-- Flash Message -->
    <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
      <?= clean($flash['message']) ?>
    </div>
    <?php endif; ?>

    <!-- Status Filter Tabs -->
    <div class="admin-tabs">
      <a href="admin.php?action=reservations"
         class="admin-tab <?= ($status_filter ?? 'all') === 'all' ? 'active' : '' ?>">
        All (<?= count($reservations) ?>)
      </a>
      <a href="admin.php?action=reservations&status=pending"
         class="admin-tab <?= ($status_filter ?? '') === 'pending' ? 'active' : '' ?>">
        Pending
      </a>
      <a href="admin.php?action=reservations&status=confirmed"
         class="admin-tab <?= ($status_filter ?? '') === 'confirmed' ? 'active' : '' ?>">
        Confirmed
      </a>
      <a href="admin.php?action=reservations&status=cancelled"
         class="admin-tab <?= ($status_filter ?? '') === 'cancelled' ? 'active' : '' ?>">
        Cancelled
      </a>
    </div>

    <?php if (!empty($reservations)): ?>
    <!-- Search bar -->
    <div class="table-toolbar">
      <input type="text" id="searchInput" placeholder="Search by name, email, or phone..." class="search-input">
    </div>

    <div class="table-responsive">
      <table class="admin-table" id="resTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Guest Details</th>
            <th>Date & Time</th>
            <th>Guests</th>
            <th>Special Requests</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reservations as $res): ?>
          <tr>
            <td><?= $res['id'] ?></td>
            <td>
              <div class="guest-info">
                <strong><?= clean($res['name']) ?></strong>
                <small><?= clean($res['email']) ?></small>
                <small><?= clean($res['phone']) ?></small>
              </div>
            </td>
            <td>
              <div>
                <strong><?= date('M d, Y', strtotime($res['date'])) ?></strong>
                <small><?= date('g:i A', strtotime($res['time'])) ?></small>
                <small>Booked: <?= date('M d', strtotime($res['created_at'])) ?></small>
              </div>
            </td>
            <td class="center"><?= $res['guests'] ?> <?= $res['guests'] == 1 ? 'person' : 'people' ?></td>
            <td>
              <span title="<?= clean($res['special_requests']) ?>">
                <?= $res['special_requests'] ? mb_strimwidth(clean($res['special_requests']), 0, 40, '...') : '—' ?>
              </span>
            </td>
            <td>
              <span class="status-badge status-<?= $res['status'] ?>"><?= ucfirst($res['status']) ?></span>
            </td>
            <td class="actions-cell">
              <!-- Quick status update -->
              <form method="POST" action="admin.php?action=reservation_update" class="inline-form">
                <input type="hidden" name="id" value="<?= $res['id'] ?>">
                <select name="status" onchange="this.form.submit()" class="status-select status-<?= $res['status'] ?>">
                  <option value="pending"   <?= $res['status'] === 'pending'   ? 'selected' : '' ?>>Pending</option>
                  <option value="confirmed" <?= $res['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                  <option value="cancelled" <?= $res['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
              </form>
              <a href="admin.php?action=reservation_delete&id=<?= $res['id'] ?>"
                 class="btn-admin-xs btn-delete"
                 onclick="return confirm('Delete this reservation?')">Delete</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <div class="empty-icon">📋</div>
      <p>No reservations found<?= ($status_filter ?? 'all') !== 'all' ? " with status: <strong>" . clean($status_filter) . "</strong>" : '' ?>.</p>
      <?php if (($status_filter ?? 'all') !== 'all'): ?>
        <a href="admin.php?action=reservations" class="btn-admin-sm">View All</a>
      <?php endif; ?>
    </div>
    <?php endif; ?>

  </div>
</div>

<script>
// Live search
const searchInput = document.getElementById('searchInput');
if (searchInput) {
  searchInput.addEventListener('input', function() {
    const term = this.value.toLowerCase();
    document.querySelectorAll('#resTable tbody tr').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
  });
}
</script>

</body>
</html>
