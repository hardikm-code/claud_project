<?php
/**
 * Bella Vista Restaurant – Admin Front Controller (MVC)
 * Routes all admin actions through AdminController
 */

require_once 'config.php';
require_once 'model_menu.php';
require_once 'model_reservation.php';
require_once 'model_admin.php';
require_once 'controller_admin.php';

startSecureSession();

AdminController::handleRequest();
