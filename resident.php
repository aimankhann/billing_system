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

// 3) HANDLE FORM SUBMISSION: ADD RESIDENT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name       = $conn->real_escape_string($_POST['FullName']);
    $flat       = $conn->real_escape_string($_POST['FlatNumber']);
    $block      = $conn->real_escape_string($_POST['Block']);
    $contact    = $conn->real_escape_string($_POST['ContactNumber']);
    $email      = $conn->real_escape_string($_POST['Email']);
    $occupation = $conn->real_escape_string($_POST['Occupation']);
    $joinDate   = $conn->real_escape_string($_POST['JoinDate']);

    $sql = "INSERT INTO residents
            (FullName, FlatNumber, Block, ContactNumber, Email, Occupation, JoinDate)
            VALUES
            ('$name','$flat','$block','$contact','$email','$occupation','$joinDate')";

    $conn->query($sql);
    header("Location: resident.php");
    exit();
}

// 4) HANDLE DELETE
if (isset($_GET['delete_id'])) {
    $delId = intval($_GET['delete_id']);
    $conn->query("DELETE FROM residents WHERE ResidentID = $delId");
    header("Location: resident.php");
    exit();
}

// 5) FETCH ALL RESIDENTS
$result = $conn->query("SELECT * FROM residents ORDER BY JoinDate DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>View Residents | Billing System</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <style>
    * { margin:0; padding:0; box-sizing:border-box }
    body {
      font-family:'Roboto',sans-serif;
      background:url('buildingresident.jpg') no-repeat center center fixed;
      background-size:cover;
      color:#555;
      padding:20px;
    }
    h1 { text-align:center; color:#04384c; margin-bottom:20px }
    .container { display:flex; flex-wrap:wrap; gap:20px; justify-content:center; }

    /* Form Styles */
    form {
      background:#b2d1dd; padding:20px; border-radius:10px;
      width:350px; box-shadow:0 4px 8px rgba(0,0,0,0.1);
    }
    form input, form button {
      width:100%; padding:10px; margin:8px 0; border:1px solid #ddd; border-radius:8px;
      font-size:16px; transition:0.3s;
    }
    form input:focus { outline:2px solid #00ffaa; border-color:#00ffaa }
    form button {
      background:#04384c; color:#fff; border:none; cursor:pointer;
    }
    form button:hover { background:#00ffaa; color:#04384c }

    /* Table Styles */
    table {
      border-collapse:collapse; background:#fff; border-radius:10px;
      box-shadow:0 4px 8px rgba(0,0,0,0.1); width:100%; max-width:800px;
      overflow-x:auto;
    }
    th, td {
      padding:12px; border:1px solid #ddd; text-align:center; font-size:16px;
    }
    th { background:#04384c; color:#fff; }
    td a {
      padding:6px 12px; margin:0 4px; border-radius:5px; text-decoration:none;
      color:#fff; font-size:14px;
    }
    a.edit { background:#00ffaa; color:#04384c; }
    a.delete { background:#f57c00; }

    /* Scrollable Table */
    .table-container {
      max-height: 400px; /* Adjust the height as needed */
      overflow-y: auto;
    }

    @media (max-width:768px) {
      .container { flex-direction:column; align-items:center }
      table, form { width:100% }
      th, td { font-size:14px }
    }
  </style>
</head>
<body>

  <h1>Residents Management</h1>
  <div class="container">

    <!-- ADD RESIDENT FORM -->
    <form method="POST" action="resident.php">
      <input type="hidden" name="action" value="add">
      <input type="text"   name="FullName"      placeholder="Full Name"     required>
      <input type="text"   name="FlatNumber"    placeholder="Flat Number"   required>
      <input type="text"   name="Block"         placeholder="Block"         required>
      <input type="text"   name="ContactNumber" placeholder="Contact Number"required>
      <input type="email"  name="Email"         placeholder="Email"         required>
      <input type="text"   name="Occupation"    placeholder="Occupation"    required>
      <input type="date"   name="JoinDate"      placeholder="Joining Date"  required>
      <button type="submit">Add Resident</button>
    </form>

    <!-- RESIDENTS TABLE -->
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>Name</th><th>Flat No</th><th>Block</th><th>Contact</th>
            <th>Email</th><th>Occupation</th><th>Join Date</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['FullName'])      ?></td>
              <td><?= htmlspecialchars($row['FlatNumber'])    ?></td>
              <td><?= htmlspecialchars($row['Block'])         ?></td>
              <td><?= htmlspecialchars($row['ContactNumber']) ?></td>
              <td><?= htmlspecialchars($row['Email'])         ?></td>
              <td><?= htmlspecialchars($row['Occupation'])    ?></td>
              <td><?= htmlspecialchars($row['JoinDate'])      ?></td>
              <td>
                <a href="edit_resident.php?id=<?= $row['ResidentID'] ?>" class="edit">Edit</a>
                <a href="resident.php?delete_id=<?= $row['ResidentID'] ?>" class="delete"
                   onclick="return confirm('Delete this resident?')">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

  </div>
</body>
</html>
