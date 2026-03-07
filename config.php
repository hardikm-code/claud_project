<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'marketingelsnerd_claud');
define('DB_PASS', 'K-QDs43-C~purMBe');
define('DB_NAME', 'marketingelsnerd_claud');

// Site Configuration
define('SITE_NAME', 'TilesCraft Pro');
define('SITE_URL', 'http://localhost/claud_project');
define('CURRENCY', '$');
define('TAX_RATE', 0.08);
define('SHIPPING_RATE', 15.00);
define('FREE_SHIPPING_MIN', 500.00);

// Database Connection (singleton)
function getDB() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die('<div style="background:#f8d7da;color:#721c24;padding:20px;margin:20px;border-radius:8px;font-family:Arial,sans-serif;">
                <strong>DB Error:</strong> ' . $conn->connect_error . '
                <br><small>Run <a href="setup.php">setup.php</a> to initialize the database.</small></div>');
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

function escape($data) {
    return getDB()->real_escape_string($data ?? '');
}

function clean($data) {
    return htmlspecialchars(strip_tags(trim($data ?? '')));
}

function formatPrice($price) {
    return CURRENCY . number_format((float)$price, 2);
}

function getCartCount() {
    if (!isset($_SESSION['cart'])) return 0;
    return array_sum(array_column($_SESSION['cart'], 'quantity'));
}

function getCartTotal() {
    if (!isset($_SESSION['cart'])) return 0;
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

function addToCart($product_id, $quantity = 1) {
    $db = getDB();
    $pid = (int)$product_id;
    $qty = max(1, (int)$quantity);
    $result = $db->query("SELECT * FROM products WHERE id = $pid AND stock > 0");
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $price = ($product['sale_price'] > 0) ? $product['sale_price'] : $product['price'];
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (isset($_SESSION['cart'][$pid])) {
            $_SESSION['cart'][$pid]['quantity'] += $qty;
        } else {
            $_SESSION['cart'][$pid] = [
                'id'       => $pid,
                'name'     => $product['name'],
                'price'    => $price,
                'image'    => $product['image'],
                'sku'      => $product['sku'],
                'size'     => $product['size'],
                'quantity' => $qty
            ];
        }
        return true;
    }
    return false;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function showFlash() {
    $flash = getFlash();
    if ($flash) {
        $cls = $flash['type'] === 'success' ? 'alert-success' : ($flash['type'] === 'error' ? 'alert-error' : 'alert-info');
        echo "<div class='alert $cls'>" . clean($flash['message']) . "</div>";
    }
}

function getCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}

// Tile pattern SVG backgrounds by category
function getTileStyle($cat_id, $color = null) {
    $colors = [
        1 => ['#8B6914','#C4A35A','#F0D080'], // Floor - golden
        2 => ['#4A7C9E','#7AADC8','#B0D4E8'], // Wall - blue
        3 => ['#3A7D44','#6AAD6A','#A8D4A8'], // Outdoor - green
        4 => ['#7B4EA6','#A87BC8','#D0AEE8'], // Mosaic - purple
        5 => ['#1A7A7A','#4AADAD','#8AD4D4'], // Bathroom - teal
        6 => ['#9E4A2A','#C87A5A','#E8AE8A'], // Kitchen - terracotta
    ];
    $c = $colors[$cat_id] ?? ['#6B7280','#9CA3AF','#D1D5DB'];
    return "background: linear-gradient(135deg, {$c[0]} 0%, {$c[1]} 50%, {$c[2]} 100%);";
}

function renderNav() {
    $count = getCartCount();
    $page = basename($_SERVER['PHP_SELF']);
    ?>
    <nav class="navbar">
      <div class="container nav-inner">
        <a href="index.php" class="logo">
          <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
            <rect x="1" y="1" width="11" height="11" rx="2" fill="#C4A35A"/>
            <rect x="16" y="1" width="11" height="11" rx="2" fill="#8B6914"/>
            <rect x="1" y="16" width="11" height="11" rx="2" fill="#8B6914"/>
            <rect x="16" y="16" width="11" height="11" rx="2" fill="#C4A35A"/>
          </svg>
          TilesCraft <strong>Pro</strong>
        </a>
        <button class="hamburger" id="hamburger" aria-label="Menu">
          <span></span><span></span><span></span>
        </button>
        <ul class="nav-links" id="navLinks">
          <li><a href="index.php"  <?= $page==='index.php'?'class="active"':'' ?>>Home</a></li>
          <li><a href="shop.php"   <?= $page==='shop.php'?'class="active"':'' ?>>Shop</a></li>
          <li class="has-dropdown">
            <a href="#">Collections &#9660;</a>
            <ul class="dropdown">
              <li><a href="shop.php?cat=1">Floor Tiles</a></li>
              <li><a href="shop.php?cat=2">Wall Tiles</a></li>
              <li><a href="shop.php?cat=3">Outdoor Tiles</a></li>
              <li><a href="shop.php?cat=4">Mosaic Tiles</a></li>
              <li><a href="shop.php?cat=5">Bathroom Tiles</a></li>
              <li><a href="shop.php?cat=6">Kitchen Tiles</a></li>
            </ul>
          </li>
          <li><a href="contact.php" <?= $page==='contact.php'?'class="active"':'' ?>>Contact</a></li>
          <?php if (isLoggedIn()): ?>
            <li><a href="orders.php" <?= $page==='orders.php'?'class="active"':'' ?>>My Orders</a></li>
            <?php if (isAdmin()): ?>
              <li><a href="admin.php" class="nav-admin">&#9881; Admin</a></li>
            <?php endif; ?>
            <li><a href="logout.php" class="nav-logout">Logout</a></li>
          <?php else: ?>
            <li><a href="login.php" <?= $page==='login.php'?'class="active"':'' ?>>Login</a></li>
            <li><a href="register.php" class="nav-register">Register</a></li>
          <?php endif; ?>
          <li>
            <a href="cart.php" class="nav-cart <?= $page==='cart.php'?'active':'' ?>">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
              Cart
              <?php if ($count > 0): ?>
                <span class="cart-count"><?= $count ?></span>
              <?php endif; ?>
            </a>
          </li>
        </ul>
      </div>
    </nav>
    <script>
    document.getElementById('hamburger').addEventListener('click', function(){
        document.getElementById('navLinks').classList.toggle('open');
        this.classList.toggle('active');
    });
    </script>
    <?php
}

function renderFooter() {
    ?>
    <footer class="footer">
      <div class="container">
        <div class="footer-grid">
          <div>
            <div class="footer-logo">
              <svg width="32" height="32" viewBox="0 0 28 28" fill="none">
                <rect x="1" y="1" width="11" height="11" rx="2" fill="#C4A35A"/>
                <rect x="16" y="1" width="11" height="11" rx="2" fill="#8B6914"/>
                <rect x="1" y="16" width="11" height="11" rx="2" fill="#8B6914"/>
                <rect x="16" y="16" width="11" height="11" rx="2" fill="#C4A35A"/>
              </svg>
              TilesCraft <strong>Pro</strong>
            </div>
            <p>Premium quality tiles for every space. Transform interiors &amp; exteriors with our extensive collection.</p>
            <div class="footer-badges">
              <span>&#9733; 4.9/5 Rating</span>
              <span>&#10003; Verified Supplier</span>
              <span>&#128666; Free Shipping $500+</span>
            </div>
          </div>
          <div>
            <h4>Collections</h4>
            <ul>
              <li><a href="shop.php?cat=1">Floor Tiles</a></li>
              <li><a href="shop.php?cat=2">Wall Tiles</a></li>
              <li><a href="shop.php?cat=3">Outdoor Tiles</a></li>
              <li><a href="shop.php?cat=4">Mosaic Tiles</a></li>
              <li><a href="shop.php?cat=5">Bathroom Tiles</a></li>
              <li><a href="shop.php?cat=6">Kitchen Tiles</a></li>
            </ul>
          </div>
          <div>
            <h4>Customer Service</h4>
            <ul>
              <li><a href="contact.php">Contact Us</a></li>
              <li><a href="orders.php">Track Order</a></li>
              <li><a href="login.php">My Account</a></li>
              <li><a href="shop.php">All Products</a></li>
              <li><a href="cart.php">Shopping Cart</a></li>
            </ul>
          </div>
          <div>
            <h4>Get In Touch</h4>
            <p>&#128205; 123 Tile District, Design Ave<br>New York, NY 10001</p>
            <p>&#128222; +1 (555) 845-3777</p>
            <p>&#128231; info@tilescraft.com</p>
            <p>&#128336; Mon–Sat: 9AM–6PM</p>
            <div class="footer-newsletter">
              <form action="contact.php" method="get">
                <input type="email" name="newsletter" placeholder="Your email address">
                <button type="submit">Subscribe</button>
              </form>
            </div>
          </div>
        </div>
        <div class="footer-bottom">
          <p>&copy; <?= date('Y') ?> TilesCraft Pro. All rights reserved.</p>
          <div class="payment-icons">
            <span>VISA</span><span>MC</span><span>AMEX</span><span>PayPal</span>
          </div>
        </div>
      </div>
    </footer>
    <?php
}
?>
