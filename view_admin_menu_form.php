<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $form_mode === 'edit' ? 'Edit' : 'Add' ?> Menu Item – Bella Vista Admin</title>
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
        <h1><?= $form_mode === 'edit' ? 'Edit Menu Item' : 'Add New Menu Item' ?></h1>
        <p><?= $form_mode === 'edit' ? 'Update existing item details' : 'Create a new dish for your menu' ?></p>
      </div>
      <a href="admin.php?action=menu_list" class="btn-admin-sm">&larr; Back to Menu</a>
    </div>

    <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?>"><?= clean($flash['message']) ?></div>
    <?php endif; ?>

    <div class="form-card">
      <form method="POST" action="admin.php?action=menu_save" class="admin-form">
        <?php if ($form_mode === 'edit'): ?>
          <input type="hidden" name="id" value="<?= $item['id'] ?>">
        <?php endif; ?>

        <div class="form-row-2">
          <div class="form-group">
            <label>Item Name <span class="req">*</span></label>
            <input type="text" name="name" value="<?= clean($item['name'] ?? '') ?>" placeholder="e.g. Truffle Tagliatelle" required>
          </div>
          <div class="form-group">
            <label>Category <span class="req">*</span></label>
            <select name="category_id" required>
              <option value="">-- Select Category --</option>
              <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= ($item['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                <?= clean($cat['name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea name="description" rows="3" placeholder="Describe the dish, key ingredients, etc."><?= clean($item['description'] ?? '') ?></textarea>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label>Price (USD) <span class="req">*</span></label>
            <input type="number" name="price" value="<?= $item['price'] ?? '' ?>" step="0.01" min="0.01" placeholder="0.00" required>
          </div>
          <div class="form-group">
            <label>Image URL</label>
            <input type="url" name="image_url" value="<?= clean($item['image_url'] ?? '') ?>" placeholder="https://...">
          </div>
        </div>

        <!-- Preview -->
        <?php if (!empty($item['image_url'])): ?>
        <div class="image-preview">
          <img src="<?= clean($item['image_url']) ?>" alt="Preview" style="max-height:150px;border-radius:8px;">
        </div>
        <?php endif; ?>

        <div class="form-row-2">
          <div class="form-group form-checkbox">
            <label class="checkbox-label">
              <input type="checkbox" name="is_featured" value="1" <?= !empty($item['is_featured']) ? 'checked' : '' ?>>
              <span>Featured Item (shown on homepage)</span>
            </label>
          </div>
          <div class="form-group form-checkbox">
            <label class="checkbox-label">
              <input type="checkbox" name="is_available" value="1" <?= ($item['is_available'] ?? 1) ? 'checked' : '' ?>>
              <span>Available (visible on menu)</span>
            </label>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-admin-primary">
            <?= $form_mode === 'edit' ? 'Update Item' : 'Add Item' ?>
          </button>
          <a href="admin.php?action=menu_list" class="btn-admin-outline">Cancel</a>
        </div>
      </form>
    </div>

  </div>
</div>

</body>
</html>
