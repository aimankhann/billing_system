<?php
$host = "localhost";
$username = "root"; 
$password = "";     
$database = "billing_system"; 

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert dummy data if table is empty
$check_sql = "SELECT COUNT(*) AS count FROM bills";
$check_result = $conn->query($check_sql);
$row = $check_result->fetch_assoc();
// Insert 15 dummy bills if table is empty
$check_sql = "SELECT COUNT(*) AS count FROM bills";
$check_result = $conn->query($check_sql);
$row = $check_result->fetch_assoc();

if ($row['count'] < 100) {
    
    for ($i = 1; $i <= 15; $i++) {
        $billID = "B" . str_pad($i, 3, "0", STR_PAD_LEFT);
        $residentID = "R" . str_pad($i, 3, "0", STR_PAD_LEFT);
        $billtypeID = ($i % 3) + 1; // Cycles through 1, 2, 3
        $billingMonth = date('Y-m', strtotime("-$i months"));
        $amount = rand(100, 300) + (rand(0, 99) / 100); // Random amount with cents
        $dueDate = date('Y-m-d', strtotime($billingMonth . "-" . rand(10, 28)));
        $paymentStatus = ($i % 4 == 0) ? "Pending" : (($i % 2 == 0) ? "Paid" : "Unpaid");

        $insert_sql = "INSERT INTO bills (billID, residentID, billtypeID, billingMonth, amount, dueDate, paymentStatus)
                       VALUES ('$billID', '$residentID', $billtypeID, '$billingMonth', $amount, '$dueDate', '$paymentStatus')";
        $conn->query($insert_sql);
    }
}


// Fetch joined data
$sql = "SELECT 
            b.billID,
            b.residentID,
            bt.typeName,
            b.billingMonth,
            b.amount,
            b.dueDate,
            b.paymentStatus
        FROM bills b
        JOIN billtype bt ON b.billtypeID = bt.billtypeID
        ORDER BY b.billID ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Bill Overview</title>
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      margin: 40px;
      background: linear-gradient(to right, rgba(44, 62, 80, 0.8), rgba(76, 161, 175, 0.8)),
                  url('billl.png') no-repeat center center / cover;
      color: #555;
    }

    h1 {
      text-align: center;
      color: #04384c;
    }

    .controls {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .controls div {
      margin: 5px 0;
    }

    .controls select,
    .controls input {
      padding: 8px;
      font-size: 16px;
      border-radius: 8px;
      border: 1px solid #ddd;
      font-family: 'Roboto', sans-serif;
    }

    .table-container {
      max-height: 400px;
      overflow-y: auto;
      overflow-x: auto;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px;
      text-align: center;
      border: 1px solid #ccc;
    }

    th {
      background-color: #04384c;
      color: white;
      position: sticky;
      top: 0;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    tr:hover {
      background-color: #eaeaea;
    }

    .controls select,
    .controls input:focus {
      outline: 2px solid #00ffaa;
      border-color: #00ffaa;
    }
  </style>
</head>
<body>

  <h1>Bill Overview</h1>

  <div class="controls">
    <div>
      <label for="status-filter" style="font-weight: bold;">Filter by Payment Status:</label>
      <select id="status-filter" onchange="filterBills()">
        <option value="all">All</option>
        <option value="paid">Paid</option>
        <option value="unpaid">Unpaid</option>
      </select>
    </div>

    <div>
      <label for="search-input" style="font-weight: bold;">Search (Resident ID or Bill Type):</label>
      <input type="text" id="search-input" placeholder="e.g., R001 or Water" onkeyup="filterBills()">
    </div>
  </div>

  <div class="table-container">
    <table id="bill-table">
      <thead>
        <tr>
          <th>Bill ID</th>
          <th>Resident ID</th>
          <th>Bill Type</th>
          <th>Billing Month</th>
          <th>Amount</th>
          <th>Due Date</th>
          <th>Payment Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr data-status="<?= strtolower($row['paymentStatus']) ?>">
              <td><?= htmlspecialchars($row['billID']) ?></td>
              <td><?= htmlspecialchars($row['residentID']) ?></td>
              <td><?= htmlspecialchars($row['typeName']) ?></td>
              <td><?= htmlspecialchars($row['billingMonth']) ?></td>
              <td>$<?= htmlspecialchars($row['amount']) ?></td>
              <td><?= htmlspecialchars($row['dueDate']) ?></td>
              <td><?= htmlspecialchars($row['paymentStatus']) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7">No records found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <script>
    function filterBills() {
      const status = document.getElementById("status-filter").value.toLowerCase();
      const search = document.getElementById("search-input").value.toLowerCase();
      const rows = document.querySelectorAll("#bill-table tbody tr");

      rows.forEach(row => {
        const rowStatus = row.getAttribute("data-status").toLowerCase();
        const residentId = row.children[1].textContent.toLowerCase();
        const billType = row.children[2].textContent.toLowerCase();

        const matchStatus = (status === "all" || rowStatus === status);
        const matchSearch = (residentId.includes(search) || billType.includes(search));

        row.style.display = (matchStatus && matchSearch) ? "" : "none";
      });
    }
  </script>

</body>
</html>
