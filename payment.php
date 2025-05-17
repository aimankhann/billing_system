<?php
session_start();

// 1) SESSION CHECK
if (!isset($_SESSION["username"])) {
    header("Location: index.html");
    exit();
}

// 2) DATABASE SETTINGS
$host     = "localhost";
$dbUser   = "root";
$dbPass   = "";
$dbName   = "billing_system";

$conn = new mysqli($host, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// 3) HANDLE FORM SUBMISSION: ADD OR UPDATE PAYMENT FROM JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if ($data && isset($data['paymentId'])) {
        $paymentId   = $conn->real_escape_string($data['paymentId']);
        $billId      = $conn->real_escape_string($data['billId']);
        $paymentDate = $conn->real_escape_string($data['paymentDate']);
        $amountPaid  = $conn->real_escape_string($data['amountPaid']);
        $paymentMode = $conn->real_escape_string($data['paymentMode']);

        // Check if payment ID exists
        $check = $conn->query("SELECT paymentID FROM payments WHERE paymentID = '$paymentId'");
        if ($check && $check->num_rows > 0) {
            // UPDATE existing payment
            $sql = "UPDATE payments 
                    SET billID = '$billId', paymentDate = '$paymentDate', AmountPaid = '$amountPaid', paymentMode = '$paymentMode'
                    WHERE paymentID = '$paymentId'";
        } else {
            // INSERT new payment
            $sql = "INSERT INTO payments (paymentID, billID, paymentDate, AmountPaid, paymentMode)
                    VALUES ('$paymentId', '$billId', '$paymentDate', '$amountPaid', '$paymentMode')";
        }

        if ($conn->query($sql) === TRUE) {
            echo "✅ Payment record successfully saved.";
        } else {
            echo "❌ DB Error: " . $conn->error;
        }
        exit();
    }
}

// 4) HANDLE DELETE PAYMENT
if (isset($_GET['delete_id'])) {
    $delId = intval($_GET['delete_id']);
    $conn->query("DELETE FROM payments WHERE paymentID = $delId");
    header("Location: payment.php");
    exit();
}

// 5) FETCH ALL PAYMENTS
$result = $conn->query("SELECT * FROM payments ORDER BY paymentID ASC");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Payments Page</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #2c3e50, #4ca1af);
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 600px;
      margin: 40px auto;
      background: #a8bbb3;
      padding: 30px 40px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
    }
    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 25px;
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: 600;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      box-sizing: border-box;
    }
    input:focus {
      outline: 2px solid #00ffaa;
      border-color: #00ffaa;
    }
    button {
      margin-top: 25px;
      background-color: #04384c;
      color: white;
      padding: 12px;
      width: 100%;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    button:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Payment Entry Form</h2>
    <form id="paymentForm">
      <label for="paymentId">Payment ID</label>
      <input type="text" name="paymentId" id="paymentId" placeholder="Enter Payment ID" required />

      <label for="billId">Bill ID</label>
      <input type="text" name="billId" id="billId" placeholder="Enter Bill ID" required />

      <label for="paymentDate">Payment Date</label>
      <input type="date" name="paymentDate" id="paymentDate" required />

      <label for="amountPaid">Amount Paid (Rs.)</label>
      <input type="number" name="amountPaid" id="amountPaid" placeholder="Enter amount paid" required />

      <label for="paymentMode">Payment Mode</label>
      <select name="paymentMode" id="paymentMode" required>
        <option value="">Select Mode</option>
        <option value="Cash">Cash</option>
        <option value="Credit Card">Credit Card</option>
        <option value="Bank Transfer">Bank Transfer</option>
      </select>

      <button type="submit">Submit Payment</button>
    </form>
  </div>

  <script>
    const form = document.getElementById('paymentForm');
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      const paymentData = {
        paymentId: document.getElementById('paymentId').value,
        billId: document.getElementById('billId').value,
        paymentDate: document.getElementById('paymentDate').value,
        amountPaid: document.getElementById('amountPaid').value,
        paymentMode: document.getElementById('paymentMode').value
      };

      fetch('payment.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(paymentData)
      })
      .then(response => response.text())
      .then(data => {
        alert(data);
        form.reset();
      })
      .catch(error => {
        alert("❌ Error submitting payment: " + error);
      });
    });
  </script>
</body>
</html>
