<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'admin');
define('DB_PASS', 'rH-#@/UDe.3A!');
define('DB_NAME', 'claud_project');


$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('<div style="padding:20px;background:#f8d7da;color:#721c24;font-family:sans-serif;">
        <h3>Database Connection Failed</h3>
        <p>' . $conn->connect_error . '</p>
        <p>Please import <strong>database.sql</strong> first.</p>
    </div>');
}

$conn->set_charset("utf8");

define('SITE_NAME', 'WanderWorld Travels');
define('SITE_URL', 'http://localhost/claud_project');
?>
