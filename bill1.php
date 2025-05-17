<?php 
// Connect to the database
$conn = new mysqli("localhost", "root", "", "billing_system");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $residentID = $_POST['residentID'];
    $billtypeID = $_POST['billtypeID'];
    $billingMonth = $_POST['billingMonth'];
    $amount = $_POST['amount'];
    $dueDate = $_POST['dueDate'];
    $paymentStatus = $_POST['paymentStatus'];

    $sql = "INSERT INTO bill (residentID, billtypeID, billingMonth, Amount, Duedate, PaymentStatus) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisdss", $residentID, $billtypeID, $billingMonth, $amount, $dueDate, $paymentStatus);

    if ($stmt->execute()) {
        $successMessage = "Bill invoice created successfully.";
    } else {
        $errorMessage = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Bill Invoice</title>
    <style>
        body {
            font-family: Arial;
            background: linear-gradient(to right, #2c3e50, #4ca1af);
            padding: 30px;
            color: #fff;
        }
        form {
            background: #ffffff;
            color: #000;
            padding: 25px;
            border-radius: 10px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0px 0px 15px rgba(0,0,0,0.3);
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background-color: #3498db;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        .success {
            color: #2ecc71;
            text-align: center;
        }
        .error {
            color: #e74c3c;
            text-align: center;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>

<h2>Create Bill Invoice</h2>

<?php if ($successMessage): ?>
    <p class="success"><?= $successMessage ?></p>
<?php elseif ($errorMessage): ?>
    <p class="error"><?= $errorMessage ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="residentID">Resident ID:</label>
    <input type="number" name="residentID" required>

    <label for="billtypeID">Bill Type ID:</label>
    <input type="number" name="billtypeID" required>

    <label for="billingMonth">Billing Month (e.g., 2024-10):</label>
    <input type="month" name="billingMonth" required>

    <label for="amount">Amount:</label>
    <input type="number" step="0.01" name="amount" required>

    <label for="dueDate">Due Date:</label>
    <input type="date" name="dueDate" required>

    <label for="paymentStatus">Payment Status:</label>
    <select name="paymentStatus" required>
        <option value="Unpaid">Unpaid</option>
        <option value="Paid">Paid</option>
    </select>

    <input type="submit" value="Create Invoice">
</form>

</body>
</html>
