<?php
require_once 'config.php';

$error = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    if ($user === ADMIN_USER && password_verify($pass, ADMIN_PASS)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $user;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Handle reservation status update
if (isAdminLoggedIn() && isset($_GET['action']) && isset($_GET['id'])) {
    $id     = (int)$_GET['id'];
    $action = $_GET['action'];
    if (in_array($action, ['confirm', 'cancel', 'delete_res'])) {
        if ($action === 'confirm') {
            $conn->query("UPDATE reservations SET status = 'confirmed' WHERE id = $id");
        } elseif ($action === 'cancel') {
            $conn->query("UPDATE reservations SET status = 'cancelled' WHERE id = $id");
        } elseif ($action === 'delete_res') {
            $conn->query("DELETE FROM reservations WHERE id = $id");
        }
        header('Location: admin.php?page=reservations');
        exit;
    }
    if ($action === 'mark_read') {
        $conn->query("UPDATE contact_messages SET is_read = 1 WHERE id = $id");
        header('Location: admin.php?page=messages');
        exit;
    }
    if ($action === 'delete_msg') {
        $conn->query("DELETE FROM contact_messages WHERE id = $id");
        header('Location: admin.php?page=messages');
        exit;
    }
    if ($action === 'toggle_item') {
        $conn->query("UPDATE menu_items SET is_available = NOT is_available WHERE id = $id");
        header('Location: admin.php?page=menu');
        exit;
    }
    if ($action === 'delete_item') {
        $conn->query("DELETE FROM menu_items WHERE id = $id");
        header('Location: admin.php?page=menu');
        exit;
    }
}

// Handle new menu item
if (isAdminLoggedIn() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $catId  = (int)$_POST['item_category'];
    $name   = sanitize($_POST['item_name'] ?? '');
    $desc   = sanitize($_POST['item_desc'] ?? '');
    $price  = (float)$_POST['item_price'];
    $feat   = isset($_POST['item_featured']) ? 1 : 0;

    if ($catId && $name && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO menu_items (category_id, name, description, price, is_featured) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issdi", $catId, $name, $desc, $price, $feat);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: admin.php?page=menu&added=1');
    exit;
}

if (!isAdminLoggedIn()) {
    // Show login form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="admin-login">
    <div class="login-card">
        <h1><?php echo SITE_NAME; ?></h1>
        <p>Sign in to the admin panel</p>

        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="admin" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>
            </div>
            <button type="submit" name="admin_login" class="btn btn-primary" style="width:100%;">Sign In</button>
        </form>

        <p style="text-align:center; margin-top:20px; font-size:0.8rem; color:var(--gray);">
            <a href="index.php" style="color:var(--primary);">Back to website</a>
        </p>
        <p style="text-align:center; margin-top:8px; font-size:0.75rem; color:var(--gray);">Default: admin / password</p>
    </div>
</div>
</body>
</html>
<?php
    exit;
}

// Admin is logged in — get current page
$page = $_GET['page'] ?? 'dashboard';

// Fetch stats
$totalRes    = $conn->query("SELECT COUNT(*) as c FROM reservations")->fetch_assoc()['c'];
$pendingRes  = $conn->query("SELECT COUNT(*) as c FROM reservations WHERE status='pending'")->fetch_assoc()['c'];
$totalItems  = $conn->query("SELECT COUNT(*) as c FROM menu_items")->fetch_assoc()['c'];
$unreadMsgs  = $conn->query("SELECT COUNT(*) as c FROM contact_messages WHERE is_read=0")->fetch_assoc()['c'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="admin-layout">

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <?php echo SITE_NAME; ?>
            <span>Admin Panel</span>
        </div>

        <nav class="admin-nav">
            <div class="admin-nav-section">Main</div>
            <a href="admin.php?page=dashboard" class="admin-nav-item <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                &#9783; Dashboard
            </a>

            <div class="admin-nav-section">Manage</div>
            <a href="admin.php?page=reservations" class="admin-nav-item <?php echo $page === 'reservations' ? 'active' : ''; ?>">
                &#128197; Reservations
                <?php if ($pendingRes > 0): ?>
                <span style="background:var(--gold); color:var(--dark); font-size:0.7rem; padding:1px 7px; border-radius:20px; margin-left:6px;"><?php echo $pendingRes; ?></span>
                <?php endif; ?>
            </a>
            <a href="admin.php?page=menu" class="admin-nav-item <?php echo $page === 'menu' ? 'active' : ''; ?>">
                &#127859; Menu Items
            </a>
            <a href="admin.php?page=messages" class="admin-nav-item <?php echo $page === 'messages' ? 'active' : ''; ?>">
                &#9993; Messages
                <?php if ($unreadMsgs > 0): ?>
                <span style="background:var(--primary); color:var(--white); font-size:0.7rem; padding:1px 7px; border-radius:20px; margin-left:6px;"><?php echo $unreadMsgs; ?></span>
                <?php endif; ?>
            </a>

            <div class="admin-nav-section">Site</div>
            <a href="index.php" class="admin-nav-item" target="_blank">&#127968; View Website</a>
            <a href="admin.php?logout=1" class="admin-nav-item" style="color: #f87171;">&#10005; Logout</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-topbar">
            <h1>
                <?php
                $titles = ['dashboard'=>'Dashboard', 'reservations'=>'Reservations', 'menu'=>'Menu Management', 'messages'=>'Contact Messages'];
                echo $titles[$page] ?? 'Dashboard';
                ?>
            </h1>
            <span style="font-size:0.85rem; color:var(--gray);">Welcome, <?php echo htmlspecialchars($_SESSION['admin_user']); ?></span>
        </div>

        <div class="admin-content">

        <?php if ($page === 'dashboard'): ?>
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-num"><?php echo $totalRes; ?></div>
                    <div class="stat-label">Total Reservations</div>
                </div>
                <div class="stat-card gold">
                    <div class="stat-num"><?php echo $pendingRes; ?></div>
                    <div class="stat-label">Pending Reservations</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-num"><?php echo $totalItems; ?></div>
                    <div class="stat-label">Menu Items</div>
                </div>
                <div class="stat-card blue">
                    <div class="stat-num"><?php echo $unreadMsgs; ?></div>
                    <div class="stat-label">Unread Messages</div>
                </div>
            </div>

            <!-- Recent Reservations -->
            <div class="admin-table-card">
                <div class="admin-table-header">
                    <h3>Recent Reservations</h3>
                    <a href="admin.php?page=reservations" class="btn btn-edit action-btn">View All</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Guests</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent = $conn->query("SELECT * FROM reservations ORDER BY created_at DESC LIMIT 8");
                        while ($r = $recent->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['name']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($r['date'])); ?></td>
                            <td><?php echo date('g:i A', strtotime($r['time'])); ?></td>
                            <td><?php echo $r['guests']; ?></td>
                            <td><span class="status-badge status-<?php echo $r['status']; ?>"><?php echo ucfirst($r['status']); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($page === 'reservations'): ?>
            <div class="admin-table-card">
                <div class="admin-table-header">
                    <h3>All Reservations (<?php echo $totalRes; ?>)</h3>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Date &amp; Time</th>
                            <th>Guests</th>
                            <th>Special Requests</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $allRes = $conn->query("SELECT * FROM reservations ORDER BY date ASC, time ASC");
                        while ($r = $allRes->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $r['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($r['name']); ?></strong></td>
                            <td>
                                <div style="font-size:0.85rem;"><?php echo htmlspecialchars($r['email']); ?></div>
                                <div style="font-size:0.85rem; color:var(--gray);"><?php echo htmlspecialchars($r['phone']); ?></div>
                            </td>
                            <td>
                                <?php echo date('M j, Y', strtotime($r['date'])); ?><br>
                                <span style="color:var(--gray); font-size:0.85rem;"><?php echo date('g:i A', strtotime($r['time'])); ?></span>
                            </td>
                            <td><?php echo $r['guests']; ?></td>
                            <td style="max-width:200px; font-size:0.85rem;"><?php echo htmlspecialchars($r['special_requests'] ?: '—'); ?></td>
                            <td><span class="status-badge status-<?php echo $r['status']; ?>"><?php echo ucfirst($r['status']); ?></span></td>
                            <td>
                                <?php if ($r['status'] === 'pending'): ?>
                                <a href="admin.php?action=confirm&id=<?php echo $r['id']; ?>&page=reservations" class="action-btn btn-confirm" onclick="return confirm('Confirm this reservation?')">Confirm</a>
                                <a href="admin.php?action=cancel&id=<?php echo $r['id']; ?>&page=reservations" class="action-btn btn-cancel" onclick="return confirm('Cancel this reservation?')">Cancel</a>
                                <?php endif; ?>
                                <a href="admin.php?action=delete_res&id=<?php echo $r['id']; ?>&page=reservations" class="action-btn btn-delete" onclick="return confirm('Delete this reservation permanently?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($page === 'menu'): ?>
            <?php if (isset($_GET['added'])): ?>
            <div class="alert alert-success">Menu item added successfully.</div>
            <?php endif; ?>

            <!-- Add Item Form -->
            <div class="admin-table-card" style="margin-bottom:28px;">
                <div class="admin-table-header">
                    <h3>Add Menu Item</h3>
                </div>
                <div style="padding: 24px;">
                    <form method="POST">
                        <div style="display:grid; grid-template-columns: 1fr 2fr 1fr 1fr auto; gap:16px; align-items:end; flex-wrap:wrap;">
                            <div class="form-group" style="margin:0;">
                                <label>Category</label>
                                <select name="item_category" required>
                                    <option value="">Select</option>
                                    <?php
                                    $cats = $conn->query("SELECT * FROM menu_categories ORDER BY display_order");
                                    while ($c = $cats->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label>Item Name</label>
                                <input type="text" name="item_name" placeholder="Dish name" required>
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label>Price ($)</label>
                                <input type="number" name="item_price" placeholder="0.00" step="0.01" min="0" required>
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label>&nbsp;</label>
                                <div style="display:flex; align-items:center; gap:8px; padding:12px 0;">
                                    <input type="checkbox" name="item_featured" id="item_feat" style="width:auto;">
                                    <label for="item_feat" style="margin:0; font-weight:normal;">Featured</label>
                                </div>
                            </div>
                            <div style="padding-bottom:2px;">
                                <button type="submit" name="add_item" class="btn btn-primary">Add Item</button>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:16px;">
                            <label>Description</label>
                            <input type="text" name="item_desc" placeholder="Short description of the dish">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Items Table -->
            <div class="admin-table-card">
                <div class="admin-table-header">
                    <h3>All Menu Items (<?php echo $totalItems; ?>)</h3>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Featured</th>
                            <th>Available</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $items = $conn->query("
                            SELECT mi.*, mc.name AS cat_name
                            FROM menu_items mi
                            JOIN menu_categories mc ON mi.category_id = mc.id
                            ORDER BY mc.display_order, mi.name
                        ");
                        while ($item = $items->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td><?php echo htmlspecialchars($item['cat_name']); ?></td>
                            <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                            <td style="max-width:220px; font-size:0.85rem; color:var(--gray);"><?php echo htmlspecialchars($item['description']); ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['is_featured'] ? '<span style="color:#10b981;">&#10003; Yes</span>' : '<span style="color:#9ca3af;">No</span>'; ?></td>
                            <td><?php echo $item['is_available'] ? '<span style="color:#10b981;">Active</span>' : '<span style="color:#ef4444;">Hidden</span>'; ?></td>
                            <td>
                                <a href="admin.php?action=toggle_item&id=<?php echo $item['id']; ?>&page=menu" class="action-btn btn-edit">
                                    <?php echo $item['is_available'] ? 'Hide' : 'Show'; ?>
                                </a>
                                <a href="admin.php?action=delete_item&id=<?php echo $item['id']; ?>&page=menu" class="action-btn btn-delete" onclick="return confirm('Delete this menu item?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($page === 'messages'): ?>
            <div class="admin-table-card">
                <div class="admin-table-header">
                    <h3>Contact Messages</h3>
                    <?php if ($unreadMsgs > 0): ?>
                    <span style="color:var(--primary); font-size:0.85rem;"><?php echo $unreadMsgs; ?> unread</span>
                    <?php endif; ?>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Received</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $msgs = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
                        while ($m = $msgs->fetch_assoc()):
                        ?>
                        <tr style="<?php echo !$m['is_read'] ? 'font-weight:600;' : ''; ?>">
                            <td><?php echo $m['id']; ?></td>
                            <td><?php echo htmlspecialchars($m['name']); ?></td>
                            <td><a href="mailto:<?php echo htmlspecialchars($m['email']); ?>" style="color:var(--primary);"><?php echo htmlspecialchars($m['email']); ?></a></td>
                            <td style="max-width:300px; font-size:0.875rem;"><?php echo htmlspecialchars($m['message']); ?></td>
                            <td style="font-size:0.85rem; white-space:nowrap;"><?php echo date('M j, Y g:i A', strtotime($m['created_at'])); ?></td>
                            <td>
                                <?php if (!$m['is_read']): ?>
                                <span class="status-badge status-pending">Unread</span>
                                <?php else: ?>
                                <span class="status-badge status-confirmed">Read</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$m['is_read']): ?>
                                <a href="admin.php?action=mark_read&id=<?php echo $m['id']; ?>&page=messages" class="action-btn btn-confirm">Mark Read</a>
                                <?php endif; ?>
                                <a href="admin.php?action=delete_msg&id=<?php echo $m['id']; ?>&page=messages" class="action-btn btn-delete" onclick="return confirm('Delete this message?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        </div><!-- /admin-content -->
    </main>
</div><!-- /admin-layout -->
</body>
</html>
