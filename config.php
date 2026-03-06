<?php
// ─── Database Configuration ────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'marketingelsnerd_claud');
define('DB_PASS', 'K-QDs43-C~purMBe');
define('DB_NAME', 'marketingelsnerd_claud');

// ─── Application Settings ─────────────────────────────────────────────────
define('SITE_NAME', 'Bella Vista');
define('SITE_TAGLINE', 'Fine Dining Experience');
define('SITE_EMAIL', 'info@bellavista.com');
define('SITE_PHONE', '(555) 123-4567');
define('SITE_ADDRESS', '123 Gourmet Avenue, New York, NY 10001');

// ─── Database Connection (Singleton PDO) ──────────────────────────────────
function getDB(): PDO {
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
            die('<div style="font-family:sans-serif;padding:40px;background:#1a1a1a;color:#e74c3c;text-align:center;">
                <h2>Database Connection Failed</h2><p>' . htmlspecialchars($e->getMessage()) . '</p>
                <p>Please run <a href="setup.php" style="color:#c8a97e">setup.php</a> first.</p></div>');
        }
    }
    return $pdo;
}

// ─── Session Helpers ──────────────────────────────────────────────────────
function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
        session_start();
    }
}

function isAdminLoggedIn(): bool {
    startSecureSession();
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_username']);
}

function requireAdminLogin(): void {
    if (!isAdminLoggedIn()) {
        header('Location: admin.php?action=login');
        exit;
    }
}

// ─── Input Sanitization ───────────────────────────────────────────────────
function clean(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function sanitizeInt(mixed $val): int {
    return (int) filter_var($val, FILTER_SANITIZE_NUMBER_INT);
}

// ─── Flash Messages ───────────────────────────────────────────────────────
function setFlash(string $type, string $message): void {
    startSecureSession();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    startSecureSession();
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function showFlash(): void {
    $flash = getFlash();
    if ($flash) {
        $type = $flash['type'] === 'success' ? 'success' : 'error';
        echo "<div class='flash-message flash-{$type}'>" . clean($flash['message']) . "</div>";
    }
}
