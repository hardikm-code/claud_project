<?php
/**
 * Incident Response Portal - Database Setup
 * Visit once to create tables and seed initial data.
 * DELETE this file after setup is complete.
 */
define('DB_HOST', 'localhost');
define('DB_USER', 'marketingelsnerd_claud');
define('DB_PASS', 'K-QDs43-C~purMBe');
define('DB_NAME', 'marketingelsnerd_claud');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("<p style='color:red;font-family:sans-serif;padding:20px;'>Connection failed: " . $conn->connect_error . "</p>");
}
$conn->set_charset("utf8mb4");

$statements = [
    "CREATE TABLE IF NOT EXISTS `incident_updates` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `status_label` varchar(50) NOT NULL,
        `title` varchar(255) NOT NULL,
        `message` text NOT NULL,
        `posted_at` datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `order_claims` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `customer_name` varchar(200) NOT NULL,
        `customer_email` varchar(200) NOT NULL,
        `order_number` varchar(100) DEFAULT NULL,
        `order_amount` decimal(10,2) DEFAULT NULL,
        `order_time` varchar(100) DEFAULT NULL,
        `description` text NOT NULL,
        `resolution_status` enum('pending','reviewing','resolved','rejected') DEFAULT 'pending',
        `admin_notes` text DEFAULT NULL,
        `submitted_at` datetime DEFAULT CURRENT_TIMESTAMP,
        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];

$errors  = [];
$success = [];

foreach ($statements as $sql) {
    if ($conn->query($sql) === true) {
        preg_match('/`(\w+)`/', $sql, $m);
        $success[] = "Created table: " . ($m[1] ?? 'unknown');
    } else {
        $errors[] = "Error: " . $conn->error;
    }
}

// Seed incident updates if table is empty
$count = $conn->query("SELECT COUNT(*) AS c FROM incident_updates")->fetch_assoc()['c'];
if ($count == 0) {
    $seeds = [
        ['Resolved',      'Service Fully Restored',           'All systems are operating normally. The website is fully restored and accepting orders. We sincerely apologise for the disruption and are committed to ensuring this does not happen again.',                                                                              '2026-03-07 03:15:00'],
        ['Monitoring',    'Fix Applied — Monitoring Systems',  'Our engineering team has applied an emergency fix and restored the database connection pool. We are actively monitoring all services to ensure stability.',                                                                                                               '2026-03-07 02:55:00'],
        ['Identified',    'Root Cause Identified',             'We have identified the root cause as an exhausted database connection pool triggered by a sudden spike in sale traffic. Our team is applying a targeted fix now.',                                                                                                       '2026-03-07 02:20:00'],
        ['Investigating', 'Investigating Website Outage',      'We are aware that the website is currently unavailable. Our engineering team has been paged and is actively investigating. We apologise for the inconvenience and will provide updates every 30 minutes.',                                                               '2026-03-06 23:55:00'],
    ];

    $stmt = $conn->prepare("INSERT INTO incident_updates (status_label, title, message, posted_at) VALUES (?, ?, ?, ?)");
    foreach ($seeds as $s) {
        $stmt->bind_param("ssss", $s[0], $s[1], $s[2], $s[3]);
        $stmt->execute()
            ? $success[] = "Seeded update: " . $s[1]
            : $errors[]  = "Seed error: " . $conn->error;
    }
    $stmt->close();
} else {
    $success[] = "incident_updates already has data — skipped seeding.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Setup | Incident Portal</title>
    <style>
        body { font-family:'Segoe UI',sans-serif; max-width:680px; margin:48px auto; padding:0 20px; color:#111827; }
        h1 { font-size:1.5rem; margin-bottom:6px; }
        .sub { color:#6b7280; margin-bottom:28px; font-size:0.9rem; }
        .ok  { color:#065f46; background:#d1fae5; padding:7px 14px; border-radius:6px; margin:4px 0; font-size:0.85rem; }
        .err { color:#991b1b; background:#fee2e2; padding:7px 14px; border-radius:6px; margin:4px 0; font-size:0.85rem; }
        .actions { margin-top:28px; display:flex; gap:12px; flex-wrap:wrap; }
        .btn { display:inline-block; padding:11px 24px; border-radius:8px; font-weight:600; text-decoration:none; font-size:0.9rem; }
        .btn-blue  { background:#1a56db; color:#fff; }
        .btn-dark  { background:#111827; color:#fff; }
        .warn { background:#fef3c7; border:1px solid #fbbf24; padding:14px 18px; border-radius:8px; margin-top:24px; font-size:0.875rem; }
    </style>
</head>
<body>
    <h1>Incident Response Portal — Database Setup</h1>
    <p class="sub">Setting up tables for the incident tracking system.</p>

    <?php if (empty($errors)): ?>
    <p style="color:#065f46; font-weight:700; margin-bottom:16px;">Setup completed successfully!</p>
    <?php else: ?>
    <p style="color:#991b1b; font-weight:700; margin-bottom:16px;"><?php echo count($errors); ?> error(s) occurred.</p>
    <?php endif; ?>

    <?php foreach ($success as $s): ?>
    <div class="ok">&#10003; <?php echo htmlspecialchars($s); ?></div>
    <?php endforeach; ?>
    <?php foreach ($errors as $e): ?>
    <div class="err">&#9888; <?php echo htmlspecialchars($e); ?></div>
    <?php endforeach; ?>

    <div class="warn">
        <strong>Security Notice:</strong> Delete <code>setup.php</code> from the server after completing setup.
    </div>

    <div class="actions">
        <a href="index.php" class="btn btn-blue">View Incident Page</a>
        <a href="admin.php" class="btn btn-dark">Go to Admin Panel</a>
    </div>
</body>
</html>
