<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "GIKONKO_TSS");

// Handle insert
if (isset($_POST['save'])) {
    $tradeName = $_POST['trade_name'];
    $stmt = $conn->prepare("INSERT INTO Trades (Trade_Name) VALUES (?)");
    $stmt->bind_param("s", $tradeName);
    $stmt->execute();
    header("Location: trades.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM Trades WHERE Trade_Id=$id");
    header("Location: trades.php");
    exit;
}

// Fetch trades
$trades = $conn->query("SELECT * FROM Trades");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Trades Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php @include 'navbar.php'; ?>

<div class="container mt-4">
  <h3 class="mb-4">Manage Trades</h3>

  <!-- Form to Add Trade -->
  <form method="post" class="row g-3 mb-4">
    <div class="col-md-6">
      <input type="text" name="trade_name" class="form-control" placeholder="Trade Name (e.g., ICT, Accounting)" required>
    </div>
    <div class="col-md-2">
      <button type="submit" name="save" class="btn btn-success w-100">Add Trade</button>
    </div>
  </form>

  <!-- Table of Trades -->
  <table class="table table-bordered bg-white shadow-sm">
    <thead class="table-success">
      <tr>
        <th>ID</th>
        <th>Trade Name</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $trades->fetch_assoc()): ?>
      <tr>
        <td><?= $row['Trade_Id'] ?></td>
        <td><?= $row['Trade_Name'] ?></td>
        <td>
          <a href="edit_trade.php?id=<?= $row['Trade_Id'] ?>" class="btn btn-sm btn-primary">Edit</a>
          <a href="trades.php?delete=<?= $row['Trade_Id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this trade?')">Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
