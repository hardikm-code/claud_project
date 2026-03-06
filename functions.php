<?php
// ============================================================
// functions.php - Helper & Utility Functions
// ============================================================

require_once __DIR__ . '/config.php';

// ---- Flash Messages ----
function flash(string $key, string $message = '', string $type = 'success'): string {
    if ($message) {
        $_SESSION['flash'][$key] = ['msg' => $message, 'type' => $type];
        return '';
    }
    if (isset($_SESSION['flash'][$key])) {
        $flash = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        $icon = $flash['type'] === 'success' ? '&#10003;' : '&#9888;';
        return '<div class="alert alert-' . $flash['type'] . '">' . $icon . ' ' . htmlspecialchars($flash['msg']) . '</div>';
    }
    return '';
}

// ---- Authentication Helpers ----
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        flash('error', 'Please login to access that page.', 'error');
        header('Location: ' . SITE_URL . '/login.php');
        exit;
    }
}

function requireAdmin(): void {
    if (!isLoggedIn() || !isAdmin()) {
        flash('error', 'Access denied. Admin only.', 'error');
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    return [
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role'  => $_SESSION['user_role'],
    ];
}

// ---- String Helpers ----
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function truncate(string $text, int $length = 120): string {
    return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
}

function generateRef(): string {
    return 'WL-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
}

// ---- Format Helpers ----
function formatPrice(float $price): string {
    return '$' . number_format($price, 2);
}

function formatDate(string $date): string {
    return date('M d, Y', strtotime($date));
}

function renderStars(float $rating): string {
    $stars = '';
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5;
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $full) {
            $stars .= '<i class="star star-full">&#9733;</i>';
        } elseif ($half && $i == $full + 1) {
            $stars .= '<i class="star star-half">&#9733;</i>';
            $half = false;
        } else {
            $stars .= '<i class="star star-empty">&#9733;</i>';
        }
    }
    return '<span class="stars">' . $stars . '</span>';
}

// ---- Sanitize ----
function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)));
}

// ---- CSRF ----
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        http_response_code(403);
        die('Invalid CSRF token.');
    }
}

// ---- Active nav link ----
function isActive(string $page): string {
    $current = basename($_SERVER['PHP_SELF']);
    return $current === $page ? 'active' : '';
}

// ---- Pagination helper ----
function paginate(int $total, int $perPage, int $current, string $url): string {
    if ($total <= $perPage) return '';
    $pages = ceil($total / $perPage);
    $html = '<div class="pagination">';
    for ($i = 1; $i <= $pages; $i++) {
        $active = $i === $current ? ' class="pg-active"' : '';
        $html .= '<a href="' . $url . '?page=' . $i . '"' . $active . '>' . $i . '</a>';
    }
    $html .= '</div>';
    return $html;
}

// ---- Status badge ----
function statusBadge(string $status): string {
    $colors = [
        'pending'   => '#f59e0b',
        'confirmed' => '#10b981',
        'cancelled' => '#ef4444',
        'completed' => '#3b82f6',
        'paid'      => '#10b981',
        'unpaid'    => '#f59e0b',
        'refunded'  => '#8b5cf6',
        'active'    => '#10b981',
        'inactive'  => '#ef4444',
        'unread'    => '#ef4444',
        'read'      => '#6b7280',
        'replied'   => '#10b981',
    ];
    $color = $colors[$status] ?? '#6b7280';
    return '<span class="badge" style="background:' . $color . '">' . ucfirst($status) . '</span>';
}
