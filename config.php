<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'marketingelsnerd_claud');
define('DB_PASS', 'K-QDs43-C~purMBe');
define('DB_NAME', 'marketingelsnerd_claud');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

define('COMPANY_NAME', 'TechServe Solutions');
define('CLIENT_STORE', 'Your Online Store');
define('INCIDENT_DATE', 'March 6, 2026');
define('INCIDENT_START', '11:47 PM');
define('INCIDENT_END', '2:51 AM');
define('INCIDENT_DURATION', '3 hours 4 minutes');
define('ADMIN_USER', 'admin');
// Password: admin2026 (bcrypt)
define('ADMIN_PASS', '$2y$10$xNp3BdP2uT5lDqVqYSe8WuRk7Q0TzJmP6i2T3KMLe4NlNuQwC6Oae');

session_start();

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function sanitize($data) {
    global $conn;
    return htmlspecialchars(strip_tags(trim($conn->real_escape_string($data))));
}
?>
