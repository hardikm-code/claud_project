<?php
class ReservationController {

    public static function index(): void {
        $success     = false;
        $errors      = [];
        $form_data   = [];
        $page_title  = 'Reservations';
        $active_page = 'reservation';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = self::processForm();
            $success = $result['success'];
            $errors  = $result['errors'];
            $form_data = $result['form_data'];
        }

        require 'view_layout_header.php';
        require 'view_reservation.php';
        require 'view_layout_footer.php';
    }

    private static function processForm(): array {
        $errors    = [];
        $form_data = [];

        $name             = trim($_POST['name']             ?? '');
        $email            = trim($_POST['email']            ?? '');
        $phone            = trim($_POST['phone']            ?? '');
        $date             = trim($_POST['date']             ?? '');
        $time             = trim($_POST['time']             ?? '');
        $guests           = sanitizeInt($_POST['guests']    ?? 2);
        $special_requests = trim($_POST['special_requests'] ?? '');

        $form_data = compact('name','email','phone','date','time','guests','special_requests');

        // Validation
        if (empty($name))  $errors[] = 'Full name is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email address is required.';
        if (empty($phone)) $errors[] = 'Phone number is required.';
        if (empty($date))  $errors[] = 'Date is required.';
        elseif (strtotime($date) < strtotime('today')) $errors[] = 'Date cannot be in the past.';
        if (empty($time))  $errors[] = 'Time is required.';
        if ($guests < 1 || $guests > 20) $errors[] = 'Guests must be between 1 and 20.';

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'form_data' => $form_data];
        }

        $ok = ReservationModel::create([
            'name'             => $name,
            'email'            => $email,
            'phone'            => $phone,
            'date'             => $date,
            'time'             => $time,
            'guests'           => $guests,
            'special_requests' => $special_requests,
        ]);

        if ($ok) {
            return ['success' => true, 'errors' => [], 'form_data' => []];
        } else {
            return ['success' => false, 'errors' => ['Failed to save your reservation. Please try again.'], 'form_data' => $form_data];
        }
    }
}
