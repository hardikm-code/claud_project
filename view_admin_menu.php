<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu Management – Bella Vista Admin</title>
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
        <h1>Menu Management</h1>
        <p>Manage your restaurant menu items</p>
      </div>
      <a href="admin.php?action=menu_add" class="btn-admin-primary">+ Add New Item</a>
    </div>

    <!-- Flash Message -->
    <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>">
      <?= clean($flash['message']) ?>
    </div>
    <?php endif; ?>

    <!-- Category Filter -->
    <div class="admin-tabs">
      <button class="admin-tab active" data-filter="all">All Items (<?= count($menu_items) ?>)</button>
      <?php foreach ($categories as $cat): ?>
      <button class="admin-tab" data-filter="<?= $cat['id'] ?>">
        <?= clean($cat['name']) ?>
      </button>
      <?php endforeach; ?>
    </div>

    <!-- Menu Items Table -->
    <?php if (!empty($menu_items)): ?>
    <div class="table-responsive">
      <table class="admin-table" id="menuTable">
        <thead>
          <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Featured</th>
            <th>Available</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($menu_items as $item): ?>
          <tr data-category="<?= $item['category_id'] ?>">
            <td>
              <div class="item-thumb">
                <?php if ($item['image_url']): ?>
                  <img src="<?= clean($item['image_url']) ?>" alt="<?= clean($item['name']) ?>" loading="lazy">
                <?php else: ?>
                  <div class="thumb-placeholder">🍽</div>
                <?php endif; ?>
              </div>
            </td>
            <td>
              <strong><?= clean($item['name']) ?></strong>
              <small><?= mb_strimwidth(clean($item['description']), 0, 60, '...') ?></small>
            </td>
            <td><?= clean($item['category_name']) ?></td>
            <td class="price-cell">$<?= number_format($item['price'], 2) ?></td>
            <td>
              <span class="badge <?= $item['is_featured'] ? 'badge-gold' : 'badge-gray' ?>">
                <?= $item['is_featured'] ? 'Yes' : 'No' ?>
              </span>
            </td>
            <td>
              <span class="badge <?= $item['is_available'] ? 'badge-green' : 'badge-red' ?>">
                <?= $item['is_available'] ? 'Active' : 'Hidden' ?>
              </span>
            </td>
            <td class="actions-cell">
              <a href="admin.php?action=menu_edit&id=<?= $item['id'] ?>" class="btn-admin-xs btn-edit">Edit</a>
              <a href="admin.php?action=menu_delete&id=<?= $item['id'] ?>"
                 class="btn-admin-xs btn-delete"
                 onclick="return confirm('Delete \'<?= clean($item['name']) ?>\'?')">Delete</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <p>No menu items found. Add your first item!</p>
      <a href="admin.php?action=menu_add" class="btn-admin-primary">Add Menu Item</a>
    </div>
    <?php endif; ?>

  </div>
</div>

<script>
// Admin table filter tabs
document.querySelectorAll('.admin-tab').forEach(tab => {
  tab.addEventListener('click', function() {
    document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
    this.classList.add('active');
    const filter = this.dataset.filter;
    document.querySelectorAll('#menuTable tbody tr').forEach(row => {
      row.style.display = filter === 'all' || row.dataset.category === filter ? '' : 'none';
    });
  });
});
</script>

</body>
</html>
