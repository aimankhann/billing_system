<?php
session_start();
require 'db_config.php'; // Include DB config

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch form data
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($username) || empty($password)) {
        echo "<script>alert('Please fill in both fields.'); window.history.back();</script>";
        exit();
    }

    // Prepare SQL to check if the admin exists
    $sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            // Plain-text password comparison (for now)
            if ($password === $row['password']) {
                // Save session info
                $_SESSION['adminid'] = $row['adminid'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['admin_logged_in'] = true;  // Set a flag to mark the admin as logged in

                // Redirect to dashboard
                echo "<script>alert('Login successful!'); window.location.href='dashboard.php';</script>";
                exit();
            } else {
                echo "<script>alert('Incorrect password.'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('Admin not found.'); window.history.back();</script>";
            exit();
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    $conn->close();
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
    exit();
}
?>
