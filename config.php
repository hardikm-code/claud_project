<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'marketingelsnerd_claud');
define('DB_PASS', 'K-QDs43-C~purMBe');
define('DB_NAME', 'marketingelsnerd_claud');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>
