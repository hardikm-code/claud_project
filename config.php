<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'marketingelsnerd_claud');
define('DB_PASS', 'K-QDs43-C~purMBe');
define('DB_NAME', 'marketingelsnerd_claud');
define('SITE_NAME', 'La Bella Cucina');
define('ADMIN_USER', 'admin');
// Default password: admin123
define('ADMIN_PASS_HASH', '$2y$10$TKh8H1.PfXPL.VoJNJKzZuqhkVkXGMajTHl.WXj1nScEL.Cy.BrQO');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:40px;background:#fff3cd;border:2px solid #ffc107;margin:40px auto;max-width:600px;border-radius:12px;">
                <h3 style="color:#856404;">Database Connection Error</h3>
                <p style="color:#533f03;">' . htmlspecialchars($e->getMessage()) . '</p>
                <p style="color:#533f03;margin-top:10px;">Please import <code>database.sql</code> and verify credentials in <code>config.php</code>.</p>
            </div>');
        }
    }
    return $pdo;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
