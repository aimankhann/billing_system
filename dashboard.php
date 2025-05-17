<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: index.html"); // Redirect to login if not logged in
    exit();
}

// Database connection settings
$servername = "localhost";  // Replace with your server name
$username = "root";         // Replace with your database username
$password = "";             // Replace with your database password
$dbname = "billing_system"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch resident data (total residents)
$residentQuery = "SELECT COUNT(*) AS total_residents FROM residents";
$residentResult = $conn->query($residentQuery);
$residentData = $residentResult->fetch_assoc();
$totalResidents = $residentData['total_residents'];

// Fetch unpaid bills
$unpaidBillsQuery = "SELECT COUNT(*) AS unpaid_bills FROM bill WHERE PaymentStatus = 'Unpaid'";
$unpaidBillsResult = $conn->query($unpaidBillsQuery);
$unpaidBillsData = $unpaidBillsResult->fetch_assoc();
$unpaidBills = $unpaidBillsData['unpaid_bills'];

// Fetch payments this month (total payments)
$currentMonth = date('Y-m');  // Get current month (e.g., 2025-05)
$paymentsQuery = "SELECT SUM(Amountpaid) AS total_payments FROM payments WHERE paymentDate LIKE '$currentMonth%'";
$paymentsResult = $conn->query($paymentsQuery);
$paymentsData = $paymentsResult->fetch_assoc();
$totalPayments = $paymentsData['total_payments'];

// Fetch bills due this month
$billsDueQuery = "SELECT COUNT(*) AS bills_due FROM bill WHERE Duedate <= CURDATE() AND PaymentStatus = 'Unpaid'";
$billsDueResult = $conn->query($billsDueQuery);
$billsDueData = $billsDueResult->fetch_assoc();
$billsDue = $billsDueData['bills_due'];

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      margin: 0;
      transition: background-color 0.3s;
      overflow: hidden;
    }

    .sidebar {
      width: 220px;
      background-color: #04384c;
      color: white;
      padding: 20px;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      transition: background-color 0.3s;
    }

    .sidebar h2 {
      font-size: 18px;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .sidebar ul {
      list-style: none;
    }

    .sidebar ul li {
      margin: 10px 0;
    }

    .sidebar ul li a {
      color: white;
      text-decoration: none;
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 10px;
      transition: 0.3s;
      padding: 8px;
      border-radius: 6px;
    }

    .sidebar ul li a:hover {
      background-color: #00ffaa;
    }

    .main {
      margin-left: 220px;
      padding: 20px;
      flex: 1;
      transition: background-color 0.3s;
    }

    .header h1 {
      font-size: 22px;
      color: #04384c;
      margin-bottom: 15px;
    }

    .dashboard-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 15px;
      margin-bottom: 20px;
    }

    .card {
      background: linear-gradient(to bottom right, #02566b, #04384c);
      color: white;
      border-radius: 12px;
      padding: 15px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
      text-align: center;
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card i {
      font-size: 2rem;
      margin-bottom: 10px;
      color: #00ffaa;
    }

    .card h3 {
      font-size: 1rem;
      margin-bottom: 5px;
    }

    .card p {
      font-size: 1.4rem;
      font-weight: bold;
    }

    .table-section h2 {
      font-size: 18px;
      margin-bottom: 8px;
      color: #04384c;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    th, td {
      padding: 10px 12px;
      text-align: left;
    }

    thead {
      background-color: #04384c;
      color: white;
    }

    tbody tr:nth-child(even) {
      background-color: #f0f0f0;
    }

    /* Notice Board Styling */
    .notice-board {
      width: 280px;
      height: 280px;
      margin-top: 20px;
      background: #ffffff;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
      transition: background-color 0.3s;
      display: inline-block;
      vertical-align: top;
    }

    .notice-board h2 {
      font-size: 18px;
      color: #04384c;
      margin-bottom: 12px;
    }

    .notice-board ul {
      list-style-type: disc;
      padding-left: 20px;
      font-size: 14px;
    }

    .notice-board li {
      margin-bottom: 8px;
    }

    /* Chart container */
    .chart-container {
      margin-top: 20px;
      width: 55%;
      height: 280px;
      background: #ffffff;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
      display: inline-block;
      vertical-align: top;
    }

    /* Dark mode styles */
    body.dark-mode {
      background-color: #121212;
      color: white;
    }

    body.dark-mode .sidebar {
      background-color: #1f1f1f;
    }

    body.dark-mode .card {
      background: linear-gradient(to bottom right, #333, #222);
    }

    body.dark-mode .card i {
      color: #00ffaa;
    }

    body.dark-mode .notice-board {
      background-color: #1f1f1f;
      color: white;
    }

    body.dark-mode .notice-board h2 {
      color: #00ffaa;
    }

    .toggle-button {
      position: absolute;
      top: 20px;
      right: 20px;
      background-color: #00ffaa;
      color: white;
      border: none;
      padding: 8px 15px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      transition: background-color 0.3s;
    }

    .toggle-button:hover {
      background-color: #28b16d;
    }
  </style>
</head>
<body>

  <div class="sidebar">
    <h2><i class="fas fa-building"></i> Billing System</h2>
    <ul>
      <li><a href="dashboard.html"><i class="fas fa-home"></i> Dashboard</a></li>
      <li><a href="resident.php"><i class="fas fa-users"></i> View Residents</a></li>
      <li><a href="bill1.php"><i class="fas fa-file-invoice"></i> Bills</a></li>
      <li><a href="payment.php"><i class="fas fa-money-bill-wave"></i> Payments</a></li>
      <li><a href="meterreading.html"><i class="fas fa-tachometer-alt"></i> Meter Readings</a></li>
        <li><a href="generatebill.php"><i class="fas fa-file-alt"></i> Generate Bill</a></li>
     <a href="logout.php" style="float:right; padding:10px; color: white; background: #e74c3c; border-radius: 5px; text-decoration: none;">Logout</a>

    </ul>
  </div>

  <div class="main">
    <div class="header">
      <h1>Welcome, Admin</h1>
    </div>

    <div class="dashboard-cards">
      <div class="card">
        <i class="fas fa-users"></i>
        <h3>Total Residents</h3>
        <p><?php echo $totalResidents; ?></p>
      </div>
      <div class="card">
        <i class="fas fa-file-invoice-dollar"></i>
        <h3>Unpaid Bills</h3>
        <p><?php echo $unpaidBills; ?></p>
      </div>
      <div class="card">
        <i class="fas fa-coins"></i>
        <h3>Payments This Month</h3>
        <p>Rs. <?php echo number_format($totalPayments, 2); ?></p>
      </div>
      <div class="card">
        <i class="fas fa-calendar-alt"></i>
        <h3>Bills Due</h3>
        <p><?php echo $billsDue; ?></p>
      </div>
    </div>

    <!-- Residents Overview Section -->
    <div class="table-section">
      <h2>Residents Overview</h2>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Flat No</th>
            <th>Block</th>
            <th>Contact</th>
            <th>Email</th>
            <th>Occupation</th>
            <th>Joining Date</th>
          </tr>
        </thead>
        <tbody>
          <?php
            // Fetch residents data to display in the table
            $conn = new mysqli($servername, $username, $password, $dbname);
            $residentsQuery = "SELECT * FROM residents";
            $residentsResult = $conn->query($residentsQuery);
            while ($row = $residentsResult->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row['FullName'] . "</td>";
              echo "<td>" . $row['FlatNumber'] . "</td>";
              echo "<td>" . $row['Block'] . "</td>";
              echo "<td>" . $row['ContactNumber'] . "</td>";
              echo "<td>" . $row['Email'] . "</td>";
              echo "<td>" . $row['Occupation'] . "</td>";
              echo "<td>" . $row['JoinDate'] . "</td>";
              echo "</tr>";
            }
            $conn->close();
          ?>
        </tbody>
      </table>
    </div>
    <!-- Notice Board -->
    <div class="notice-board">
      <h2><i class="fas fa-bullhorn"></i> Notice Board</h2>
      <ul>
        <li><strong>03-May-2025:</strong> Water supply will be interrupted from 9AM to 12PM.</li>
        <li><strong>01-May-2025:</strong> Maintenance work scheduled in Block B.</li>
        <li><strong>29-Apr-2025:</strong> Parking slots to be reassigned – contact admin.</li>
      </ul>
    </div>
    <!-- Chart Container -->
    <div class="chart-container">
      <canvas id="myChart"></canvas>
    </div>
  </div>

    <!-- Chart and Notice Board sections can be added here if needed -->

  </div>
  
  <button class="toggle-button" id="toggleButton">Switch to Dark Mode</button>

  <script>
    const toggleButton = document.getElementById('toggleButton');
    const body = document.body;

    toggleButton.addEventListener('click', () => {
      body.classList.toggle('dark-mode');
      toggleButton.textContent = body.classList.contains('dark-mode')
        ? 'Switch to Light Mode'
        : 'Switch to Dark Mode';
    });

    // Chart.js Configuration for Bills
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['January', 'February', 'March', 'April', 'May'],
        datasets: [{
          label: 'Bills',
          data: [1500, 1200, 1300, 1600, 1800],
          backgroundColor: '#00ffaa',
          borderColor: '#00ffaa',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
</body>
</html>
