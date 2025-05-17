<?php
$host = "localhost";         // Change if your DB is hosted elsewhere
$dbname = "billing_system";  // Replace with your actual database name
$username = "root";          // Default XAMPP/WAMP username
$password = "";              // Default is empty for localhost

// Create a new connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
