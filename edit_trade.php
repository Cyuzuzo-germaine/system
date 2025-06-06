<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "GIKONKO_TSS");

// Fetch the trade by ID
if (!isset($_GET['id'])) {
    header("Location: trades.php");
    exit;
}

$id = $_GET['id'];
$trade = $conn->query("SELECT * FROM Trades WHERE Trade_Id = $id")->fetch_assoc();

if (!$trade) {
    echo "Trade not found!";
    exit;
}

// Handle update
if (isset($_POST['update'])) {
    $trade_name = $_POST['trade_name'];

    $stmt = $conn->prepare("UPDATE Trades SET Trade_Name = ? WHERE Trade_Id = ?");
    $stmt->bind_param("si", $trade_name, $id);
    $stmt->execute();

    header("Location: trades.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Trade</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
  <h3>Edit Trade</h3>

  <form method="post" class="mt-3">
    <div class="mb-3">
      <label for="trade_name" class="form-label">Trade Name</label>
      <input type="text" id="trade_name" name="trade_name" class="form-control" value="<?= htmlspecialchars($trade['Trade_Name']) ?>" required>
    </div>
    <button type="submit" name="update" class="btn btn-success">Update</button>
    <a href="trades.php" class="btn btn-secondary">Back</a>
  </form>
</div>

</body>
</html>
