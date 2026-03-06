<?php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Create tables if they do not exist
$sql = "CREATE TABLE IF NOT EXISTS menu (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(30) NOT NULL,
description TEXT,
price DECIMAL(8, 2) NOT NULL
);

CREATE TABLE IF NOT EXISTS reservations (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(50) NOT NULL,
email VARCHAR(50),
reservation_date DATE,
tel VARCHAR(15)
);";

if ($conn->multi_query($sql) === TRUE) {
  echo "Tables created successfully";
} else {
  echo "Error creating tables: " . $conn->error;
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Restaurant Website</title>
  <style>
    /* Add basic styling */
  </style>
</head>
<body>
  <h1>Welcome to Our Restaurant!</h1>
  <h2>Menu</h2>
  <?php
  // Fetch and display menu
  $conn = new mysqli($servername, $username, $password, $dbname);
  $sql = "SELECT * FROM menu";
  $result = $conn->query($sql);
  
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      echo "<div>" .
        "<h3>" . $row["name"] . "</h3>" .
        "<p>" . $row["description"] . "</p>" .
        "<p>Price: $" . $row["price"] . "</p>" .
      "</div>";
    }
  } else {
    echo "No menus available.";
  }
  $conn->close();
  ?>

  <h2>Make a Reservation</h2>
  <form action="reservation.php" method="post">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required><br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>
    <label for="reservation_date">Reservation Date:</label>
    <input type="date" id="reservation_date" name="reservation_date" required><br>
    <label for="tel">Telephone Number:</label>
    <input type="tel" id="tel" name="tel" required><br><br>
    <input type="submit" value="Reserve">
  </form>

</body>
</html>

<?php
// reservation.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $reservation_date = $_POST['reservation_date'];
  $tel = $_POST['tel'];

  $conn = new mysqli($servername, $username, $password, $dbname);

  $stmt = $conn->prepare("INSERT INTO reservations (name, email, reservation_date, tel) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $name, $email, $reservation_date, $tel);

  if ($stmt->execute() === TRUE) {
    echo "Reservation successful.";
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
}
?>