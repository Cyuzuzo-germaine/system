<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "GIKONKO_TSS");

// Fetch trades for dropdown
$trades = $conn->query("SELECT * FROM Trades");

// Handle insert
if (isset($_POST['save'])) {
    $name = $_POST['module_name'];
    $trade_id = $_POST['trade_id'];

    $stmt = $conn->prepare("INSERT INTO Modules (Module_Name, Trade_Id) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $trade_id);
    $stmt->execute();

    header("Location: modules.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM Modules WHERE Module_Id = $id");
    header("Location: modules.php");
    exit;
}

// Fetch modules
$modules = $conn->query("SELECT m.Module_Id, m.Module_Name, t.Trade_Name 
                         FROM Modules m JOIN Trades t ON m.Trade_Id = t.Trade_Id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Modules</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
  <h3>Manage Modules</h3>

  <!-- Form to Add Module -->
  <form method="post" class="row g-3 mb-4">
    <div class="col-md-6">
      <input type="text" name="module_name" class="form-control" placeholder="Module Name" required>
    </div>
    <div class="col-md-4">
      <select name="trade_id" class="form-select" required>
        <option value="">Select Trade</option>
        <?php while ($t = $trades->fetch_assoc()): ?>
          <option value="<?= $t['Trade_Id'] ?>"><?= $t['Trade_Name'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" name="save" class="btn btn-success w-100">Add Module</button>
    </div>
  </form>

  <!-- Table of Modules -->
  <table class="table table-bordered bg-white shadow-sm">
    <thead class="table-success">
      <tr>
        <th>ID</th>
        <th>Module Name</th>
        <th>Trade</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $modules->fetch_assoc()): ?>
      <tr>
        <td><?= $row['Module_Id'] ?></td>
        <td><?= $row['Module_Name'] ?></td>
        <td><?= $row['Trade_Name'] ?></td>
        <td>
          <a href="edit_module.php?id=<?= $row['Module_Id'] ?>" class="btn btn-sm btn-primary">Edit</a>
          <a href="modules.php?delete=<?= $row['Module_Id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this module?')">Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

</body>
</html>
        