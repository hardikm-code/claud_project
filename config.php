<?php
// ============================================================
// config.php - Application Configuration
// ============================================================

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'marketingelsnerd_claud');
define('DB_PASS', 'K-QDs43-C~purMBe');
define('DB_NAME', 'marketingelsnerd_claud');

// Site settings
define('SITE_NAME', 'WanderLux Travel');
define('SITE_URL', 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/claud_project');
define('SITE_EMAIL', 'info@wanderlux.com');
define('SITE_PHONE', '+1 (555) 123-4567');

// Session & security
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
