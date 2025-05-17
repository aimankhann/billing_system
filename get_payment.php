<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: index.html");
    exit();
}

$host     = "localhost";
$dbUser   = "root";
$dbPass   = "";
$dbName   = "billing_system";

$conn = new mysqli($host, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $paymentId = $conn->real_escape_string($_GET['id']);
    $result = $conn->query("SELECT * FROM payments WHERE paymentID = '$paymentId'");
    
    if ($result && $result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Payment not found"]);
    }
}

$conn->close();
?>