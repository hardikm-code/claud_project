<?php
/**
 * Bella Vista Restaurant – Front Controller (MVC)
 * Routes: index.php?page=home|menu|reservation
 */

require_once 'config.php';
require_once 'model_menu.php';
require_once 'model_reservation.php';

// Whitelist of valid pages
$valid_pages = ['home', 'menu', 'reservation'];
$page = $_GET['page'] ?? 'home';

if (!in_array($page, $valid_pages)) {
    $page = 'home';
}

// Route to the appropriate controller
switch ($page) {
    case 'menu':
        require_once 'controller_menu.php';
        MenuController::index();
        break;

    case 'reservation':
        require_once 'controller_reservation.php';
        ReservationController::index();
        break;

    case 'home':
    default:
        require_once 'controller_home.php';
        HomeController::index();
        break;
}
