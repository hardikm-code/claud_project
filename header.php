<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="description" content="<?php echo isset($pageDesc) ? $pageDesc : 'La Bella Cucina - Authentic Italian restaurant with fine dining experience.'; ?>">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="site-header">
    <div class="nav-container">
        <a href="index.php" class="nav-logo"><?php echo SITE_NAME; ?></a>

        <nav>
            <ul class="nav-links" id="nav-links">
                <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="menu.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>">Menu</a></li>
                <li><a href="reservations.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reservations.php' ? 'active' : ''; ?>">Reservations</a></li>
                <li><a href="reservations.php" class="nav-cta btn">Book a Table</a></li>
            </ul>
        </nav>

        <div class="hamburger" onclick="document.getElementById('nav-links').classList.toggle('open')">
            <span></span><span></span><span></span>
        </div>
    </div>
</header>
