<?php
// save_reading.php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: index.html");
    exit();
}

$host = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "billing_system";

$conn = new mysqli($host, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $required = ['residentId', 'readingMonth', 'previousReading', 'currentReading', 'ratePerUnit'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            die("Error: Missing required field '$field'");
        }
    }

    $residentId = $conn->real_escape_string($_POST['residentId']);
    $readingMonth = $conn->real_escape_string($_POST['readingMonth']);
    $previousReading = (float)$_POST['previousReading'];
    $currentReading = (float)$_POST['currentReading'];
    $ratePerUnit = (float)$_POST['ratePerUnit'];

    // Validate readings
    if ($currentReading <= $previousReading) {
        die("Error: Current reading must be greater than previous reading");
    }

    // Calculate values
    $unitsUsed = $currentReading - $previousReading;
    $totalAmount = $unitsUsed * $ratePerUnit;

    // Check for existing reading
    $check = $conn->prepare("SELECT id FROM meter_readings WHERE residentId = ? AND readingMonth = ?");
    $check->bind_param("ss", $residentId, $readingMonth);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        die("Error: Reading already exists for this resident and month");
    }

    // Insert new reading
    $stmt = $conn->prepare("INSERT INTO meter_readings 
                          (residentId, readingMonth, previousReading, currentReading, unitsUsed, ratePerUnit, totalAmount) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdddds", $residentId, $readingMonth, $previousReading, $currentReading, 
                     $unitsUsed, $ratePerUnit, $totalAmount);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
    exit();
}

// For GET requests - fetch residents
$residents = $conn->query("SELECT residentId, name FROM residents ORDER BY name");
$conn->close();
?>