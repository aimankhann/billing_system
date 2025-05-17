<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "billing_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$billData = null;
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $residentID = $_POST['residentID'];

    $sql = "SELECT * FROM bill WHERE residentID = ? ORDER BY billID DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $residentID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $billData = $result->fetch_assoc();
    } else {
        $error = "No bill found for Resident ID: $residentID";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Bill Invoice</title>
   <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f2f2f2;
        padding: 40px;
        margin: 0;
    }

    form, .invoice {
        background: linear-gradient(to right, #2c3e50, #4ca1af);
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        max-width: 500px;
        margin: auto;
        color: #fff;
    }

    form h2, .invoice h2 {
        text-align: center;
        margin-bottom: 25px;
        font-size: 24px;
        color: #ffffff;
    }

    label {
        font-weight: bold;
        margin-bottom: 5px;
        display: block;
    }

    input[type="number"],
    input[type="month"],
    input[type="date"],
    select {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: none;
        border-radius: 6px;
        box-sizing: border-box;
    }

    input[type="submit"] {
        background-color: #3498db;
        color: white;
        padding: 12px;
        font-size: 16px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #2980b9;
    }

    .invoice p {
        font-size: 16px;
        margin: 8px 0;
        line-height: 1.6;
    }

    .error, .success {
        text-align: center;
        font-size: 16px;
        margin-bottom: 15px;
    }

    .error {
        color: #ff4d4d;
    }

    .success {
        color: #2ecc71;
    }
</style>

</head>
<body>

<h2 style="text-align:center;">Check Bill Invoice</h2>

<form method="POST" action="">
    <label for="residentID">Enter Resident ID:</label>
    <input type="number" name="residentID" required>
    <input type="submit" value="View Invoice">
</form>

<?php if ($error): ?>
    <p class="error"><?= $error ?></p>
<?php elseif ($billData): ?>
    <div class="invoice">
        <h2>Bill Invoice</h2>
        <p><strong>Resident ID:</strong> <?= $billData['residentID'] ?></p>
        <p><strong>Bill Type ID:</strong> <?= $billData['billtypeID'] ?></p>
        <p><strong>Billing Month:</strong> <?= $billData['billingMonth'] ?></p>
        <p><strong>Amount:</strong> Rs. <?= number_format($billData['Amount'], 2) ?></p>
        <p><strong>Due Date:</strong> <?= $billData['Duedate'] ?></p>
        <p><strong>Payment Status:</strong> <?= $billData['PaymentStatus'] ?></p>
    </div>
<?php endif; ?>

</body>
</html>
