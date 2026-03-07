<?php
require_once 'config.php';

$error = '';

// ---------- Auth ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_login'])) {
    $u = trim($_POST['username'] ?? '');
    $p = trim($_POST['password'] ?? '');
    if ($u === ADMIN_USER && password_verify($p, ADMIN_PASS)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    }
    $error = 'Invalid username or password.';
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// ---------- Login screen ----------
if (!isAdminLoggedIn()) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?php echo COMPANY_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="admin-login">
    <div class="login-card">
        <h1><?php echo COMPANY_NAME; ?></h1>
        <p class="sub">Incident Response Admin Panel</p>

        <?php if ($error): ?>
        <div class="alert alert-error"><span>&#9888;</span><div><?php echo htmlspecialchars($error); ?></div></div>
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
            <button type="submit" name="do_login" class="btn btn-primary" style="width:100%;">Sign In</button>
        </form>
        <p style="text-align:center; margin-top:20px; font-size:0.8rem; color:var(--gray);">
            <a href="index.php">&#8592; Back to Incident Report</a>
        </p>
    </div>
</div>
</body>
</html>
<?php
    exit;
}

// ---------- Admin actions ----------
$page = $_GET['page'] ?? 'dashboard';

// Add incident update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_update'])) {
    $label   = sanitize($_POST['status_label'] ?? '');
    $title   = sanitize($_POST['upd_title'] ?? '');
    $message = sanitize($_POST['upd_message'] ?? '');
    if ($label && $title && $message) {
        $stmt = $conn->prepare("INSERT INTO incident_updates (status_label, title, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $label, $title, $message);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: admin.php?page=updates&saved=1');
    exit;
}

// Delete update
if (isset($_GET['del_update'])) {
    $id = (int)$_GET['del_update'];
    $conn->query("DELETE FROM incident_updates WHERE id = $id");
    header('Location: admin.php?page=updates');
    exit;
}

// Update claim status
if (isset($_GET['claim_status']) && isset($_GET['id'])) {
    $id  = (int)$_GET['id'];
    $st  = in_array($_GET['claim_status'], ['pending','reviewing','resolved','rejected'])
           ? $_GET['claim_status'] : 'pending';
    $stmt = $conn->prepare("UPDATE order_claims SET resolution_status = ? WHERE id = ?");
    $stmt->bind_param("si", $st, $id);
    $stmt->execute();
    $stmt->close();
    header('Location: admin.php?page=claims');
    exit;
}

// Save admin note on claim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_note'])) {
    $id   = (int)$_POST['claim_id'];
    $note = sanitize($_POST['admin_notes'] ?? '');
    $stmt = $conn->prepare("UPDATE order_claims SET admin_notes = ? WHERE id = ?");
    $stmt->bind_param("si", $note, $id);
    $stmt->execute();
    $stmt->close();
    header('Location: admin.php?page=claims');
    exit;
}

// Delete claim
if (isset($_GET['del_claim'])) {
    $id = (int)$_GET['del_claim'];
    $conn->query("DELETE FROM order_claims WHERE id = $id");
    header('Location: admin.php?page=claims');
    exit;
}

// ---------- Fetch stats ----------
$totalClaims   = $conn->query("SELECT COUNT(*) AS c FROM order_claims")->fetch_assoc()['c'] ?? 0;
$pendingClaims = $conn->query("SELECT COUNT(*) AS c FROM order_claims WHERE resolution_status='pending'")->fetch_assoc()['c'] ?? 0;
$resolvedClaims = $conn->query("SELECT COUNT(*) AS c FROM order_claims WHERE resolution_status='resolved'")->fetch_assoc()['c'] ?? 0;
$totalUpdates  = $conn->query("SELECT COUNT(*) AS c FROM incident_updates")->fetch_assoc()['c'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | <?php echo COMPANY_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="admin-layout">

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <?php echo COMPANY_NAME; ?>
            <span>Incident Admin</span>
        </div>
        <nav class="admin-nav">
            <div class="admin-nav-section">Overview</div>
            <a href="admin.php?page=dashboard" class="admin-nav-item <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                &#9783; &nbsp;Dashboard
            </a>

            <div class="admin-nav-section">Incident</div>
            <a href="admin.php?page=updates" class="admin-nav-item <?php echo $page === 'updates' ? 'active' : ''; ?>">
                &#128203; &nbsp;Timeline Updates
            </a>
            <a href="admin.php?page=claims" class="admin-nav-item <?php echo $page === 'claims' ? 'active' : ''; ?>">
                &#128221; &nbsp;Order Claims
                <?php if ($pendingClaims > 0): ?>
                <span style="background:var(--danger); color:#fff; font-size:0.68rem; padding:1px 7px; border-radius:20px; margin-left:auto;"><?php echo $pendingClaims; ?></span>
                <?php endif; ?>
            </a>

            <div class="admin-nav-section">Site</div>
            <a href="index.php" class="admin-nav-item" target="_blank">&#127760; &nbsp;View Public Page</a>
            <a href="admin.php?logout=1" class="admin-nav-item" style="color:#f87171;">&#10005; &nbsp;Logout</a>
        </nav>
    </aside>

    <!-- Main -->
    <main class="admin-main">
        <div class="admin-topbar">
            <h1>
                <?php
                $titles = [
                    'dashboard' => 'Dashboard',
                    'updates'   => 'Incident Timeline Updates',
                    'claims'    => 'Lost Order Claims',
                ];
                echo $titles[$page] ?? 'Dashboard';
                ?>
            </h1>
            <span style="font-size:0.8rem; color:var(--gray);">
                Incident: <?php echo INCIDENT_DATE; ?> &bull;
                <a href="index.php" target="_blank">View public page</a>
            </span>
        </div>

        <div class="admin-content">

        <?php if ($page === 'dashboard'): ?>
            <!-- Stats -->
            <div class="stats-row">
                <div class="stat-tile">
                    <div class="num"><?php echo $totalClaims; ?></div>
                    <div class="lbl">Total Claims</div>
                </div>
                <div class="stat-tile red">
                    <div class="num"><?php echo $pendingClaims; ?></div>
                    <div class="lbl">Pending Review</div>
                </div>
                <div class="stat-tile green">
                    <div class="num"><?php echo $resolvedClaims; ?></div>
                    <div class="lbl">Resolved</div>
                </div>
                <div class="stat-tile amber">
                    <div class="num"><?php echo $totalUpdates; ?></div>
                    <div class="lbl">Timeline Updates</div>
                </div>
            </div>

            <!-- Recent Claims -->
            <div class="card">
                <div class="card-header">
                    <h3>Recent Order Claims</h3>
                    <a href="admin.php?page=claims" class="btn btn-outline btn-sm">View All</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Order #</th>
                            <th>Submitted</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rec = $conn->query("SELECT * FROM order_claims ORDER BY submitted_at DESC LIMIT 8");
                        if ($rec && $rec->num_rows > 0):
                            while ($r = $rec->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($r['customer_name']); ?></strong><br>
                                <span style="font-size:0.8rem; color:var(--gray);"><?php echo htmlspecialchars($r['customer_email']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($r['order_number'] ?: '—'); ?></td>
                            <td style="font-size:0.8rem; white-space:nowrap;"><?php echo date('M j, g:i A', strtotime($r['submitted_at'])); ?></td>
                            <td><span class="badge badge-<?php echo $r['resolution_status']; ?>"><?php echo ucfirst($r['resolution_status']); ?></span></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="4" style="text-align:center; color:var(--gray); padding:28px;">No claims submitted yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Incident Summary -->
            <div class="card">
                <div class="card-header"><h3>Incident Summary</h3></div>
                <div class="card-body">
                    <table>
                        <tbody>
                            <tr><td style="font-weight:600; width:180px;">Date</td><td><?php echo INCIDENT_DATE; ?></td></tr>
                            <tr><td style="font-weight:600;">Outage Start</td><td><?php echo INCIDENT_START; ?></td></tr>
                            <tr><td style="font-weight:600;">Service Restored</td><td><?php echo INCIDENT_END; ?></td></tr>
                            <tr><td style="font-weight:600;">Duration</td><td><?php echo INCIDENT_DURATION; ?></td></tr>
                            <tr><td style="font-weight:600;">Root Cause</td><td>Database connection pool exhaustion (traffic surge)</td></tr>
                            <tr><td style="font-weight:600;">Current Status</td><td><span class="badge badge-resolved">Resolved</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page === 'updates'): ?>

            <?php if (isset($_GET['saved'])): ?>
            <div class="alert alert-success"><span>&#10003;</span><div>Update posted successfully.</div></div>
            <?php endif; ?>

            <!-- Post new update -->
            <div class="card" style="margin-bottom:28px;">
                <div class="card-header"><h3>Post New Timeline Update</h3></div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Status Label</label>
                                <select name="status_label" required>
                                    <option value="">Select status</option>
                                    <option value="Investigating">Investigating</option>
                                    <option value="Identified">Identified</option>
                                    <option value="Monitoring">Monitoring</option>
                                    <option value="Resolved">Resolved</option>
                                    <option value="Update">Update</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Update Title</label>
                                <input type="text" name="upd_title" placeholder="Brief title for this update" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="upd_message" placeholder="Describe what is happening right now..." required></textarea>
                        </div>
                        <button type="submit" name="add_update" class="btn btn-primary">Post Update</button>
                    </form>
                </div>
            </div>

            <!-- All updates -->
            <div class="card">
                <div class="card-header"><h3>All Timeline Updates (<?php echo $totalUpdates; ?>)</h3></div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Status</th>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Posted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $ups = $conn->query("SELECT * FROM incident_updates ORDER BY posted_at DESC");
                        if ($ups && $ups->num_rows > 0):
                            while ($u = $ups->fetch_assoc()):
                                $slug = strtolower(preg_replace('/[^a-zA-Z]/', '', $u['status_label']));
                        ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td><span class="status-pill status-<?php echo htmlspecialchars($slug); ?>"><?php echo htmlspecialchars($u['status_label']); ?></span></td>
                            <td style="font-weight:600;"><?php echo htmlspecialchars($u['title']); ?></td>
                            <td style="max-width:300px; font-size:0.85rem; color:var(--gray);"><?php echo htmlspecialchars($u['message']); ?></td>
                            <td style="font-size:0.8rem; white-space:nowrap;"><?php echo date('M j, Y g:i A', strtotime($u['posted_at'])); ?></td>
                            <td>
                                <a href="admin.php?del_update=<?php echo $u['id']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this update?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="6" style="text-align:center; color:var(--gray); padding:28px;">No updates yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($page === 'claims'): ?>

            <div class="card">
                <div class="card-header">
                    <h3>All Lost Order Claims (<?php echo $totalClaims; ?>)</h3>
                    <span style="font-size:0.8rem; color:var(--gray);"><?php echo $pendingClaims; ?> pending review</span>
                </div>
                <?php
                $claims = $conn->query("SELECT * FROM order_claims ORDER BY submitted_at DESC");
                if ($claims && $claims->num_rows > 0):
                ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Order #</th>
                            <th>Amount</th>
                            <th>Time</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($c = $claims->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $c['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($c['customer_name']); ?></strong><br>
                                <a href="mailto:<?php echo htmlspecialchars($c['customer_email']); ?>" style="font-size:0.8rem;"><?php echo htmlspecialchars($c['customer_email']); ?></a>
                            </td>
                            <td><?php echo htmlspecialchars($c['order_number'] ?: '—'); ?></td>
                            <td><?php echo $c['order_amount'] ? '$'.number_format($c['order_amount'],2) : '—'; ?></td>
                            <td style="font-size:0.8rem;"><?php echo htmlspecialchars($c['order_time'] ?: '—'); ?></td>
                            <td style="max-width:220px; font-size:0.82rem; color:var(--gray);"><?php echo htmlspecialchars($c['description']); ?></td>
                            <td>
                                <select onchange="location.href='admin.php?page=claims&claim_status='+this.value+'&id=<?php echo $c['id']; ?>'"
                                        style="font-size:0.8rem; padding:4px 8px; border-radius:6px; border:1.5px solid var(--border);">
                                    <?php foreach (['pending','reviewing','resolved','rejected'] as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo $c['resolution_status'] === $s ? 'selected' : ''; ?>>
                                        <?php echo ucfirst($s); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td style="font-size:0.78rem; white-space:nowrap;"><?php echo date('M j, g:i A', strtotime($c['submitted_at'])); ?></td>
                            <td>
                                <!-- Note form inline -->
                                <form method="POST" style="display:flex; gap:6px; flex-direction:column; min-width:180px;">
                                    <input type="hidden" name="claim_id" value="<?php echo $c['id']; ?>">
                                    <textarea name="admin_notes" rows="2"
                                              placeholder="Internal note..."
                                              style="font-size:0.78rem; padding:6px; border:1.5px solid var(--border); border-radius:6px; resize:vertical;"><?php echo htmlspecialchars($c['admin_notes'] ?? ''); ?></textarea>
                                    <div style="display:flex; gap:6px;">
                                        <button type="submit" name="save_note" class="btn btn-outline btn-sm">Save Note</button>
                                        <a href="admin.php?del_claim=<?php echo $c['id']; ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Delete this claim permanently?')">Del</a>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div style="text-align:center; padding:48px; color:var(--gray);">
                    <p style="font-size:1.1rem; margin-bottom:8px;">No claims submitted yet.</p>
                    <p style="font-size:0.875rem;">Claims submitted via the <a href="index.php#claim">public page</a> will appear here.</p>
                </div>
                <?php endif; ?>
            </div>

        <?php endif; ?>

        </div><!-- /admin-content -->
    </main>
</div>
</body>
</html>
