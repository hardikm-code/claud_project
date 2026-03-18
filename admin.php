<?php
require_once 'config.php';

// Simple password protection
$admin_pass = 'admin123';
session_start();

if (isset($_POST['password'])) {
    if ($_POST['password'] === $admin_pass) {
        $_SESSION['admin'] = true;
    } else {
        $login_error = 'Incorrect password.';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

$is_admin = !empty($_SESSION['admin']);

// Update booking status
if ($is_admin && isset($_POST['update_status'])) {
    $bid    = intval($_POST['booking_id']);
    $status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE bookings SET status = '$status' WHERE id = $bid");
    header('Location: admin.php?section=bookings&updated=1');
    exit;
}

// Package management
if ($is_admin) {
    $upload_dir = __DIR__ . '/uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    // Add package
    if (isset($_POST['add_package'])) {
        $pkg_name    = $conn->real_escape_string(trim($_POST['pkg_name']));
        $destination = $conn->real_escape_string(trim($_POST['destination']));
        $category    = $conn->real_escape_string($_POST['category']);
        $duration    = intval($_POST['duration']);
        $price       = floatval($_POST['price']);
        $orig_price  = !empty($_POST['original_price']) ? floatval($_POST['original_price']) : null;
        $max_people  = intval($_POST['max_people']);
        $short_desc  = $conn->real_escape_string(trim($_POST['short_desc']));
        $description = $conn->real_escape_string(trim($_POST['description']));
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_active   = intval($_POST['is_active']);

        $image_url = '';
        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $fname = 'pkg_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $fname)) {
                    $image_url = 'uploads/' . $fname;
                }
            }
        }
        if (empty($image_url) && !empty($_POST['image_url_ext'])) {
            $image_url = $conn->real_escape_string(trim($_POST['image_url_ext']));
        }

        $op_sql = $orig_price !== null ? $orig_price : 'NULL';
        $conn->query("INSERT INTO packages (name, destination, category, duration, price, original_price, max_people, image_url, short_desc, description, is_featured, is_active)
            VALUES ('$pkg_name','$destination','$category',$duration,$price,$op_sql,$max_people,'$image_url','$short_desc','$description',$is_featured,$is_active)");
        header('Location: admin.php?section=packages&msg=added');
        exit;
    }

    // Edit package
    if (isset($_POST['edit_package'])) {
        $pid         = intval($_POST['pkg_id']);
        $pkg_name    = $conn->real_escape_string(trim($_POST['pkg_name']));
        $destination = $conn->real_escape_string(trim($_POST['destination']));
        $category    = $conn->real_escape_string($_POST['category']);
        $duration    = intval($_POST['duration']);
        $price       = floatval($_POST['price']);
        $orig_price  = !empty($_POST['original_price']) ? floatval($_POST['original_price']) : null;
        $max_people  = intval($_POST['max_people']);
        $short_desc  = $conn->real_escape_string(trim($_POST['short_desc']));
        $description = $conn->real_escape_string(trim($_POST['description']));
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_active   = intval($_POST['is_active']);

        $img_sql = '';
        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $fname = 'pkg_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $fname)) {
                    $ni      = $conn->real_escape_string('uploads/' . $fname);
                    $img_sql = ", image_url='$ni'";
                }
            }
        } elseif (!empty($_POST['image_url_ext'])) {
            $ni      = $conn->real_escape_string(trim($_POST['image_url_ext']));
            $img_sql = ", image_url='$ni'";
        }

        $op_sql = $orig_price !== null ? $orig_price : 'NULL';
        $conn->query("UPDATE packages SET name='$pkg_name', destination='$destination', category='$category',
            duration=$duration, price=$price, original_price=$op_sql, max_people=$max_people,
            short_desc='$short_desc', description='$description', is_featured=$is_featured, is_active=$is_active
            $img_sql WHERE id=$pid");
        header('Location: admin.php?section=packages&msg=updated');
        exit;
    }

    // Delete package
    if (isset($_GET['delete_pkg'])) {
        $pid = intval($_GET['delete_pkg']);
        $conn->query("DELETE FROM packages WHERE id=$pid");
        header('Location: admin.php?section=packages&msg=deleted');
        exit;
    }
}

// Stats
if ($is_admin) {
    $total_pkg  = $conn->query("SELECT COUNT(*) c FROM packages")->fetch_assoc()['c'];
    $total_book = $conn->query("SELECT COUNT(*) c FROM bookings")->fetch_assoc()['c'];
    $pending    = $conn->query("SELECT COUNT(*) c FROM bookings WHERE status='Pending'")->fetch_assoc()['c'];
    $confirmed  = $conn->query("SELECT COUNT(*) c FROM bookings WHERE status='Confirmed'")->fetch_assoc()['c'];
    $total_rev  = $conn->query("SELECT SUM(total_price) s FROM bookings WHERE status='Confirmed'")->fetch_assoc()['s'] ?? 0;

    $filter   = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';
    $w        = $filter ? "WHERE b.status = '$filter'" : '';
    $bookings = $conn->query("SELECT b.*, p.name as pkg_name, p.destination FROM bookings b JOIN packages p ON b.package_id = p.id $w ORDER BY b.created_at DESC");

    $section      = isset($_GET['section']) ? $_GET['section'] : 'dashboard';
    $all_packages = $conn->query("SELECT * FROM packages ORDER BY created_at DESC");
    $edit_pkg     = null;
    if ($section === 'packages' && isset($_GET['edit_pkg'])) {
        $epid = intval($_GET['edit_pkg']);
        $res  = $conn->query("SELECT * FROM packages WHERE id=$epid");
        if ($res && $res->num_rows > 0) $edit_pkg = $res->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-wrap { max-width:1200px; margin:0 auto; padding:30px 20px; }
        table { width:100%; border-collapse:collapse; background:white; border-radius:12px; overflow:hidden; box-shadow:var(--shadow); }
        th { background:var(--dark); color:white; padding:14px 16px; text-align:left; font-size:0.88rem; text-transform:uppercase; letter-spacing:0.5px; }
        td { padding:13px 16px; border-bottom:1px solid #f0f0f0; font-size:0.88rem; color:#444; vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#f9f9f9; }
        .status-badge { padding:4px 12px; border-radius:20px; font-size:0.78rem; font-weight:700; }
        .status-Pending   { background:#fff3cd; color:#856404; }
        .status-Confirmed { background:#d1fae5; color:#065f46; }
        .status-Cancelled { background:#fee2e2; color:#991b1b; }
        .status-active    { background:#d1fae5; color:#065f46; }
        .status-inactive  { background:#fee2e2; color:#991b1b; }
        .stat-box { background:white; border-radius:12px; padding:24px; box-shadow:var(--shadow); text-align:center; }
        .stat-box .n { font-size:2rem; font-weight:900; }
        .stat-box .l { font-size:0.85rem; color:#6c757d; margin-top:4px; }
        select.inline { padding:5px 10px; border:1px solid #ddd; border-radius:6px; font-size:0.82rem; }
        .upd-btn { padding:5px 12px; background:var(--primary); color:white; border:none; border-radius:6px; font-size:0.8rem; cursor:pointer; }
        .btn-sm { padding:5px 12px; border-radius:6px; font-size:0.8rem; cursor:pointer; border:none; font-weight:600; text-decoration:none; display:inline-block; }
        .btn-edit   { background:#2563eb; color:white; }
        .btn-delete { background:#dc2626; color:white; }
        .btn-add { background:var(--primary); color:white; padding:10px 22px; border-radius:8px; font-size:0.9rem; font-weight:700; text-decoration:none; display:inline-block; border:none; cursor:pointer; }
        .pkg-form { background:white; border-radius:12px; padding:28px; box-shadow:var(--shadow); margin-top:24px; }
        .pkg-form h3 { margin:0 0 20px; font-size:1.2rem; color:var(--dark); font-weight:800; }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .form-grid .full { grid-column:1/-1; }
        .pkg-form .form-group label { display:block; font-size:0.85rem; font-weight:600; color:#444; margin-bottom:5px; }
        .pkg-form .form-group input,
        .pkg-form .form-group select,
        .pkg-form .form-group textarea {
            width:100%; padding:9px 12px; border:1px solid #ddd; border-radius:8px;
            font-size:0.88rem; box-sizing:border-box; font-family:inherit; color:#333;
        }
        .pkg-form .form-group textarea { resize:vertical; min-height:80px; }
        .pkg-thumb { width:56px; height:44px; object-fit:cover; border-radius:6px; display:block; }
        .pkg-no-img { width:56px; height:44px; background:#f0f0f0; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; font-size:1.3rem; }
        @media(max-width:640px){ .form-grid { grid-template-columns:1fr; } .form-grid .full { grid-column:1; } }
    </style>
</head>
<body style="background:var(--light-gray);">

<?php if (!$is_admin): ?>
<!-- LOGIN -->
<div style="min-height:100vh; display:flex; align-items:center; justify-content:center; background:var(--dark);">
    <div style="background:white; border-radius:16px; padding:40px; width:380px; box-shadow:0 20px 60px rgba(0,0,0,0.4);">
        <div style="text-align:center; margin-bottom:28px;">
            <div class="logo">Wander<span>World</span></div>
            <div style="color:#6c757d; font-size:0.9rem; margin-top:8px;">Admin Panel</div>
        </div>
        <?php if (isset($login_error)): ?>
        <div class="alert alert-error"><?= $login_error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Admin Password</label>
                <input type="password" name="password" placeholder="Enter password" required autofocus>
            </div>
            <button type="submit" class="submit-btn" style="margin-top:8px;">Login &rarr;</button>
        </form>
        <div style="text-align:center; margin-top:16px; font-size:0.82rem; color:#999;">Default password: admin123</div>
    </div>
</div>

<?php else: ?>
<!-- ADMIN PANEL -->
<nav style="background:var(--dark); padding:0 4%;">
    <div style="display:flex; justify-content:space-between; align-items:center; min-height:60px; flex-wrap:wrap; gap:8px; padding:8px 0;">
        <span class="logo" style="font-size:1.3rem;">Wander<span>World</span>
            <span style="font-size:0.75rem; opacity:0.6; font-weight:400; margin-left:4px;">Admin</span>
        </span>
        <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
            <?php foreach (['dashboard' => '&#128202; Dashboard', 'packages' => '&#127758; Packages', 'bookings' => '&#128203; Bookings', 'messages' => '&#9993; Messages'] as $s => $lbl): ?>
            <a href="admin.php?section=<?= $s ?>"
               style="padding:6px 14px; border-radius:20px; font-size:0.82rem; font-weight:600; text-decoration:none;
                      background:<?= $section == $s ? 'var(--primary)' : 'rgba(255,255,255,0.12)' ?>;
                      color:white;"><?= $lbl ?></a>
            <?php endforeach; ?>
            <a href="index.php" style="color:rgba(255,255,255,0.7); font-size:0.85rem; margin-left:6px; text-decoration:none;" target="_blank">&#127760; Site</a>
            <a href="admin.php?logout=1" style="color:#f87171; font-size:0.85rem; text-decoration:none;">&#128682; Logout</a>
        </div>
    </div>
</nav>

<div class="admin-wrap">

<?php /* ===================== DASHBOARD ===================== */
if ($section === 'dashboard'): ?>

    <h2 style="font-size:1.5rem; font-weight:900; color:var(--dark); margin-bottom:24px;">Dashboard Overview</h2>

    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:16px; margin-bottom:32px;">
        <div class="stat-box"><div class="n" style="color:var(--primary);"><?= $total_pkg ?></div><div class="l">Total Packages</div></div>
        <div class="stat-box"><div class="n" style="color:#2563eb;"><?= $total_book ?></div><div class="l">Total Bookings</div></div>
        <div class="stat-box"><div class="n" style="color:#d97706;"><?= $pending ?></div><div class="l">Pending</div></div>
        <div class="stat-box"><div class="n" style="color:#059669;"><?= $confirmed ?></div><div class="l">Confirmed</div></div>
        <div class="stat-box"><div class="n" style="color:#7c3aed;">&#8377;<?= number_format($total_rev) ?></div><div class="l">Total Revenue</div></div>
    </div>

    <div style="display:flex; gap:12px; flex-wrap:wrap;">
        <a href="admin.php?section=packages&show_form=1" class="btn-add">+ Add New Package</a>
        <a href="admin.php?section=bookings" class="btn-add" style="background:#2563eb;">Manage Bookings</a>
        <a href="admin.php?section=messages" class="btn-add" style="background:#7c3aed;">View Messages</a>
    </div>

<?php /* ===================== PACKAGES ===================== */
elseif ($section === 'packages'): ?>

    <?php $pkg_msgs = ['added' => 'Package added successfully.', 'updated' => 'Package updated successfully.', 'deleted' => 'Package deleted successfully.'];
    if (isset($_GET['msg']) && isset($pkg_msgs[$_GET['msg']])): ?>
    <div class="alert alert-success" style="margin-bottom:16px;">&#9989; <?= $pkg_msgs[$_GET['msg']] ?></div>
    <?php endif; ?>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
        <h2 style="font-size:1.5rem; font-weight:900; color:var(--dark); margin:0;">Package Management</h2>
        <a href="admin.php?section=packages&show_form=1" class="btn-add">+ Add New Package</a>
    </div>

    <!-- Packages Table -->
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Package Name</th>
                    <th>Destination</th>
                    <th>Category</th>
                    <th>Duration</th>
                    <th>Price (&#8377;)</th>
                    <th>Max Persons</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $all_packages->data_seek(0);
                if ($all_packages->num_rows == 0): ?>
                <tr><td colspan="10" style="text-align:center; padding:40px; color:#999;">No packages yet. Click "+ Add New Package" to get started.</td></tr>
                <?php endif;
                $i = 1;
                while ($pkg = $all_packages->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td>
                        <?php if (!empty($pkg['image_url'])): ?>
                        <img src="<?= htmlspecialchars($pkg['image_url']) ?>" alt="" class="pkg-thumb"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='inline-flex';">
                        <span class="pkg-no-img" style="display:none;">&#128247;</span>
                        <?php else: ?>
                        <span class="pkg-no-img">&#128247;</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight:600; color:#1a1a2e;"><?= htmlspecialchars($pkg['name']) ?></div>
                        <?php if ($pkg['is_featured']): ?>
                        <span style="font-size:0.72rem; background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:10px; display:inline-block; margin-top:3px;">&#11088; Featured</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($pkg['destination']) ?></td>
                    <td><?= htmlspecialchars($pkg['category']) ?></td>
                    <td><?= intval($pkg['duration']) ?> days</td>
                    <td>
                        <strong>&#8377;<?= number_format($pkg['price']) ?></strong>
                        <?php if ($pkg['original_price']): ?>
                        <div style="font-size:0.75rem; color:#aaa; text-decoration:line-through;">&#8377;<?= number_format($pkg['original_price']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><?= intval($pkg['max_people']) ?></td>
                    <td>
                        <span class="status-badge <?= $pkg['is_active'] ? 'status-active' : 'status-inactive' ?>">
                            <?= $pkg['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td>
                        <div style="display:flex; gap:6px; flex-wrap:wrap;">
                            <a href="admin.php?section=packages&edit_pkg=<?= $pkg['id'] ?>#pkg-form" class="btn-sm btn-edit">&#9998; Edit</a>
                            <a href="admin.php?section=packages&delete_pkg=<?= $pkg['id'] ?>" class="btn-sm btn-delete"
                               onclick="return confirm('Delete &quot;<?= addslashes(htmlspecialchars($pkg['name'])) ?>&quot;?\n\nThis will also remove all associated bookings.');">&#128465; Del</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Add / Edit Package Form -->
    <?php
    $show_form = isset($_GET['show_form']) || $edit_pkg;
    if ($show_form):
        $f          = $edit_pkg ?: [];
        $form_title = $edit_pkg ? 'Edit Package' : 'Add New Package';
        $action_key = $edit_pkg ? 'edit_package' : 'add_package';
    ?>
    <div class="pkg-form" id="pkg-form">
        <h3><?= $form_title ?></h3>
        <form method="POST" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="<?= $action_key ?>" value="1">
            <?php if ($edit_pkg): ?>
            <input type="hidden" name="pkg_id" value="<?= intval($edit_pkg['id']) ?>">
            <?php endif; ?>

            <div class="form-grid">

                <div class="form-group">
                    <label>Package Name <span style="color:red;">*</span></label>
                    <input type="text" name="pkg_name" required maxlength="200"
                           value="<?= htmlspecialchars($f['name'] ?? '') ?>"
                           placeholder="e.g. Maldives Paradise Escape">
                </div>

                <div class="form-group">
                    <label>Destination <span style="color:red;">*</span></label>
                    <input type="text" name="destination" required maxlength="200"
                           value="<?= htmlspecialchars($f['destination'] ?? '') ?>"
                           placeholder="e.g. Maldives">
                </div>

                <div class="form-group">
                    <label>Category <span style="color:red;">*</span></label>
                    <select name="category" required>
                        <?php foreach (['Beach', 'Adventure', 'Cultural', 'Wildlife', 'Mountain', 'Luxury'] as $cat): ?>
                        <option value="<?= $cat ?>" <?= ($f['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Duration (Days) <span style="color:red;">*</span></label>
                    <input type="number" name="duration" required min="1" max="365"
                           value="<?= htmlspecialchars($f['duration'] ?? '') ?>"
                           placeholder="7">
                </div>

                <div class="form-group">
                    <label>Price (&#8377;) <span style="color:red;">*</span></label>
                    <input type="number" name="price" required min="0" step="0.01"
                           value="<?= htmlspecialchars($f['price'] ?? '') ?>"
                           placeholder="85000">
                </div>

                <div class="form-group">
                    <label>Original Price (&#8377;) <span style="color:#999; font-weight:400;">(optional — for showing discount)</span></label>
                    <input type="number" name="original_price" min="0" step="0.01"
                           value="<?= htmlspecialchars($f['original_price'] ?? '') ?>"
                           placeholder="99000">
                </div>

                <div class="form-group">
                    <label>Max Persons <span style="color:red;">*</span></label>
                    <input type="number" name="max_people" required min="1" max="500"
                           value="<?= htmlspecialchars($f['max_people'] ?? '20') ?>"
                           placeholder="20">
                </div>

                <div class="form-group">
                    <label>Status <span style="color:red;">*</span></label>
                    <select name="is_active" required>
                        <option value="1" <?= ($f['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= ($f['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>

                <div class="form-group full">
                    <label>Package Image <span style="color:#999; font-weight:400;">(upload file or paste URL)</span></label>
                    <?php if (!empty($f['image_url'])): ?>
                    <div style="margin-bottom:10px; display:flex; align-items:center; gap:10px; background:#f8f9fa; padding:10px; border-radius:8px;">
                        <img src="<?= htmlspecialchars($f['image_url']) ?>" style="height:64px; border-radius:6px; object-fit:cover;"
                             onerror="this.style.display='none'">
                        <div style="font-size:0.8rem; color:#666;">Current image. Upload a new file or enter a new URL below to replace it.</div>
                    </div>
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp"
                           style="margin-bottom:8px;">
                    <input type="url" name="image_url_ext"
                           placeholder="https://example.com/image.jpg (leave blank to keep current)"
                           value="<?= htmlspecialchars(isset($f['image_url']) && strpos($f['image_url'], 'http') === 0 ? $f['image_url'] : '') ?>">
                    <div style="font-size:0.78rem; color:#999; margin-top:4px;">Accepted file types: JPG, JPEG, PNG, GIF, WEBP. File upload takes priority over URL.</div>
                </div>

                <div class="form-group full">
                    <label>Short Description <span style="color:#999; font-weight:400;">(shown on package listing cards)</span></label>
                    <textarea name="short_desc" placeholder="Brief, engaging description — 1 or 2 sentences."><?= htmlspecialchars($f['short_desc'] ?? '') ?></textarea>
                </div>

                <div class="form-group full">
                    <label>Full Description <span style="color:red;">*</span></label>
                    <textarea name="description" required style="min-height:130px;"
                              placeholder="Detailed description of the package — what travelers will experience, highlights, unique selling points..."><?= htmlspecialchars($f['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group full" style="display:flex; align-items:center; gap:10px; background:#f8f9fa; padding:12px 14px; border-radius:8px;">
                    <input type="checkbox" name="is_featured" id="chk_featured" value="1"
                           <?= ($f['is_featured'] ?? 0) ? 'checked' : '' ?>
                           style="width:auto; margin:0; cursor:pointer; width:18px; height:18px;">
                    <label for="chk_featured" style="margin:0; cursor:pointer; font-size:0.9rem;">
                        &#11088; Mark as <strong>Featured</strong> — appears in the homepage featured packages section
                    </label>
                </div>

            </div><!-- .form-grid -->

            <div style="display:flex; gap:10px; margin-top:22px; flex-wrap:wrap; padding-top:18px; border-top:1px solid #e8e8e8;">
                <button type="submit" class="btn-add">
                    <?= $edit_pkg ? '&#9998; Update Package' : '+ Save Package' ?>
                </button>
                <a href="admin.php?section=packages"
                   style="padding:10px 22px; border-radius:8px; background:#6b7280; color:white; text-decoration:none; font-weight:600; font-size:0.9rem;">
                    Cancel
                </a>
            </div>
        </form>
    </div><!-- .pkg-form -->
    <?php endif; // show_form ?>

<?php /* ===================== BOOKINGS ===================== */
elseif ($section === 'bookings'): ?>

    <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success" style="margin-bottom:20px;">&#9989; Booking status updated successfully.</div>
    <?php endif; ?>

    <h2 style="font-size:1.5rem; font-weight:900; color:var(--dark); margin-bottom:24px;">Booking Management</h2>

    <!-- Filter Tabs -->
    <div style="display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap;">
        <?php foreach ([''=>'All Bookings', 'Pending'=>'Pending', 'Confirmed'=>'Confirmed', 'Cancelled'=>'Cancelled'] as $f => $label): ?>
        <a href="admin.php?section=bookings&filter=<?= $f ?>"
           style="padding:8px 20px; border-radius:25px; font-size:0.88rem; font-weight:600; text-decoration:none;
                  background:<?= $filter == $f ? 'var(--primary)' : 'white' ?>;
                  color:<?= $filter == $f ? 'white' : '#555' ?>;
                  box-shadow:var(--shadow);"><?= $label ?></a>
        <?php endforeach; ?>
    </div>

    <!-- Bookings Table -->
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>#</th><th>Ref</th><th>Package</th><th>Traveler</th>
                    <th>Travel Date</th><th>Pax</th><th>Total</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $bookings->data_seek(0);
                if ($bookings->num_rows == 0): ?>
                <tr><td colspan="9" style="text-align:center; padding:40px; color:#999;">No bookings found.</td></tr>
                <?php endif;
                $i = 1;
                while ($b = $bookings->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><strong style="font-family:monospace; color:var(--primary);"><?= htmlspecialchars($b['booking_ref']) ?></strong></td>
                    <td>
                        <div style="font-weight:600; color:#1a1a2e;"><?= htmlspecialchars($b['pkg_name']) ?></div>
                        <div style="font-size:0.8rem; color:#999;">&#128205; <?= htmlspecialchars($b['destination']) ?></div>
                    </td>
                    <td>
                        <div><?= htmlspecialchars($b['first_name'] . ' ' . $b['last_name']) ?></div>
                        <div style="font-size:0.8rem; color:#999;"><?= htmlspecialchars($b['email']) ?></div>
                    </td>
                    <td><?= date('d M Y', strtotime($b['travel_date'])) ?></td>
                    <td><?= $b['num_adults'] ?>A<?= $b['num_children'] > 0 ? '+' . $b['num_children'] . 'C' : '' ?></td>
                    <td><strong>&#8377;<?= number_format($b['total_price']) ?></strong></td>
                    <td><span class="status-badge status-<?= $b['status'] ?>"><?= $b['status'] ?></span></td>
                    <td>
                        <form method="POST" style="display:flex; gap:6px; align-items:center;">
                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                            <input type="hidden" name="update_status" value="1">
                            <select name="status" class="inline">
                                <option value="Pending"   <?= $b['status'] == 'Pending'   ? 'selected' : '' ?>>Pending</option>
                                <option value="Confirmed" <?= $b['status'] == 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                <option value="Cancelled" <?= $b['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <button type="submit" class="upd-btn">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

<?php /* ===================== MESSAGES ===================== */
elseif ($section === 'messages'): ?>

    <h2 style="font-size:1.5rem; font-weight:900; color:var(--dark); margin-bottom:24px;">Contact Messages</h2>
    <?php
    $contacts = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC");
    if ($contacts->num_rows == 0): ?>
    <div style="background:white; border-radius:12px; padding:40px; text-align:center; color:#999; box-shadow:var(--shadow);">
        No contact messages yet.
    </div>
    <?php else: ?>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Subject</th><th>Message</th><th>Date</th></tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($c = $contacts->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td style="white-space:nowrap;"><?= htmlspecialchars($c['name']) ?></td>
                    <td><?= htmlspecialchars($c['email']) ?></td>
                    <td><?= htmlspecialchars($c['phone'] ?: '-') ?></td>
                    <td><?= htmlspecialchars($c['subject'] ?: '-') ?></td>
                    <td style="max-width:280px;"><?= htmlspecialchars(substr($c['message'], 0, 120)) ?><?= strlen($c['message']) > 120 ? '…' : '' ?></td>
                    <td style="white-space:nowrap;"><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

<?php endif; /* end section switch */ ?>

    <div style="margin-top:36px; padding-top:20px; border-top:1px solid #e0e0e0; font-size:0.82rem; color:#999; text-align:center;">
        &copy; <?= date('Y') ?> <?= SITE_NAME ?> &mdash; Admin Panel
    </div>
</div><!-- .admin-wrap -->
<?php endif; // is_admin ?>

</body>
</html>
