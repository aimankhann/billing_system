<?php
session_start(); // Start the session

// Destroy all session variables
session_unset(); 

// Destroy the session itself
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="1;url=index.html"> <!-- Redirects to login after 3 seconds -->
    <title>Logout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 50px;
        }
        .logout-message {
            background-color: #2ecc71;
            color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px;
        }
        a {
            float: right;
            padding: 10px;
            color: white;
            background: #e74c3c;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="logout-message">
        <p>You have successfully logged out.</p>
        <p>You will be redirected to the login page shortly.</p>
    </div>

    <a href="logout.php">Logout</a>

</body>
</html>
