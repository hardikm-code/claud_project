<?php
require_once 'config.php';

$db_status = 'success';
$db_message = 'Database connected successfully!';

if ($conn->connect_error) {
    $db_status = 'error';
    $db_message = 'Database connection failed: ' . $conn->connect_error;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello World</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Hello, World!</h1>
        <p>Welcome to my first PHP page. This page is served by PHP and connected to a MySQL database.</p>
        <div class="badge">PHP + MySQL</div>

        <div class="db-status <?php echo $db_status; ?>">
            <?php echo htmlspecialchars($db_message); ?>
        </div>

        <div class="footer">
            PHP <?php echo phpversion(); ?> &bull; <?php echo date('Y-m-d'); ?>
        </div>
    </div>
</body>
</html>
