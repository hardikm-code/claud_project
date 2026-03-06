<?php
session_start();

$host = 'localhost';
$db = 'claud_database';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if (isset($_POST['action'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($_POST['action'] == 'register') {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param('ss', $username, $hashed_password);
        $stmt->execute();
        $stmt->close();
        echo 'Registration successful!';
    } elseif ($_POST['action'] == 'login') {
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['username'] = $username;
            header('Location: dashboard.php');
        } else {
            echo 'Invalid login credentials.';
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register</title>
</head>
<body>
    <h2>Register</h2>
    <form action="index.php" method="post">
        <input type="text" name="username" required placeholder="Username">
        <input type="password" name="password" required placeholder="Password">
        <input type="hidden" name="action" value="register">
        <button type="submit">Register</button>
    </form>

    <h2>Login</h2>
    <form action="index.php" method="post">
        <input type="text" name="username" required placeholder="Username">
        <input type="password" name="password" required placeholder="Password">
        <input type="hidden" name="action" value="login">
        <button type="submit">Login</button>
    </form>
</body>
</html>