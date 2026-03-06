<?php
class AdminController {

    public static function handleRequest(): void {
        $action = $_GET['action'] ?? 'dashboard';

        // Public actions (no auth required)
        if ($action === 'login')  { self::login();  return; }
        if ($action === 'logout') { self::logout(); return; }

        // All other actions require authentication
        requireAdminLogin();

        switch ($action) {
            case 'dashboard':           self::dashboard();          break;
            case 'menu_list':           self::menuList();           break;
            case 'menu_add':            self::menuAdd();            break;
            case 'menu_edit':           self::menuEdit();           break;
            case 'menu_delete':         self::menuDelete();         break;
            case 'menu_save':           self::menuSave();           break;
            case 'reservations':        self::reservations();       break;
            case 'reservation_update':  self::reservationUpdate();  break;
            case 'reservation_delete':  self::reservationDelete();  break;
            default:                    self::dashboard();
        }
    }

    // ── Login / Logout ────────────────────────────────────────

    private static function login(): void {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = AdminModel::verifyLogin($username, $password);
            if ($user) {
                startSecureSession();
                session_regenerate_id(true);
                $_SESSION['admin_id']       = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                header('Location: admin.php?action=dashboard');
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        }

        require 'view_admin_login.php';
    }

    private static function logout(): void {
        startSecureSession();
        session_unset();
        session_destroy();
        header('Location: admin.php?action=login');
        exit;
    }

    // ── Dashboard ─────────────────────────────────────────────

    private static function dashboard(): void {
        $stats = [
            'total_reservations' => ReservationModel::countTotal(),
            'pending'            => ReservationModel::countByStatus('pending'),
            'confirmed'          => ReservationModel::countByStatus('confirmed'),
            'cancelled'          => ReservationModel::countByStatus('cancelled'),
            'menu_items'         => MenuModel::countItems(),
            'today'              => count(ReservationModel::getTodayReservations()),
        ];
        $recent_reservations = ReservationModel::getRecentReservations(8);
        $page_title = 'Dashboard';

        require 'view_admin_dashboard.php';
    }

    // ── Menu Management ───────────────────────────────────────

    private static function menuList(): void {
        $menu_items = MenuModel::getAllItems();
        $categories = MenuModel::getAllCategories();
        $flash      = getFlash();
        $page_title = 'Menu Management';

        require 'view_admin_menu.php';
    }

    private static function menuAdd(): void {
        $categories = MenuModel::getAllCategories();
        $item       = null; // null = add mode
        $flash      = getFlash();
        $page_title = 'Add Menu Item';
        $form_mode  = 'add';

        require 'view_admin_menu_form.php';
    }

    private static function menuEdit(): void {
        $id   = sanitizeInt($_GET['id'] ?? 0);
        $item = MenuModel::getItemById($id);
        if (!$item) {
            setFlash('error', 'Menu item not found.');
            header('Location: admin.php?action=menu_list');
            exit;
        }
        $categories = MenuModel::getAllCategories();
        $flash      = getFlash();
        $page_title = 'Edit Menu Item';
        $form_mode  = 'edit';

        require 'view_admin_menu_form.php';
    }

    private static function menuSave(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: admin.php?action=menu_list');
            exit;
        }

        $id   = sanitizeInt($_POST['id'] ?? 0);
        $data = [
            'category_id'  => sanitizeInt($_POST['category_id'] ?? 0),
            'name'         => trim($_POST['name'] ?? ''),
            'description'  => trim($_POST['description'] ?? ''),
            'price'        => (float)($_POST['price'] ?? 0),
            'image_url'    => trim($_POST['image_url'] ?? ''),
            'is_featured'  => isset($_POST['is_featured']) ? 1 : 0,
            'is_available' => isset($_POST['is_available']) ? 1 : 0,
        ];

        if (empty($data['name']) || $data['category_id'] <= 0 || $data['price'] <= 0) {
            setFlash('error', 'Please fill in all required fields.');
            $redirect = $id > 0 ? "admin.php?action=menu_edit&id=$id" : "admin.php?action=menu_add";
            header("Location: $redirect");
            exit;
        }

        if ($id > 0) {
            MenuModel::updateItem($id, $data);
            setFlash('success', 'Menu item updated successfully.');
        } else {
            MenuModel::createItem($data);
            setFlash('success', 'Menu item added successfully.');
        }

        header('Location: admin.php?action=menu_list');
        exit;
    }

    private static function menuDelete(): void {
        $id = sanitizeInt($_GET['id'] ?? 0);
        if ($id > 0) {
            MenuModel::deleteItem($id);
            setFlash('success', 'Menu item deleted.');
        }
        header('Location: admin.php?action=menu_list');
        exit;
    }

    // ── Reservations ──────────────────────────────────────────

    private static function reservations(): void {
        $status_filter = $_GET['status'] ?? 'all';
        $reservations  = $status_filter !== 'all'
            ? ReservationModel::getAll($status_filter)
            : ReservationModel::getAll();
        $flash      = getFlash();
        $page_title = 'Reservations';

        require 'view_admin_reservations.php';
    }

    private static function reservationUpdate(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id     = sanitizeInt($_POST['id']     ?? 0);
            $status = trim($_POST['status']        ?? '');
            if ($id > 0 && $status) {
                ReservationModel::updateStatus($id, $status);
                setFlash('success', 'Reservation status updated.');
            }
        }
        header('Location: admin.php?action=reservations');
        exit;
    }

    private static function reservationDelete(): void {
        $id = sanitizeInt($_GET['id'] ?? 0);
        if ($id > 0) {
            ReservationModel::delete($id);
            setFlash('success', 'Reservation deleted.');
        }
        header('Location: admin.php?action=reservations');
        exit;
    }
}
