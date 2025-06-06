<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "GIKONKO_TSS");

if (!isset($_GET['id'])) {
    header("Location: modules.php");
    exit;
}

$id = $_GET['id'];
$module = $conn->query("SELECT * FROM Modules WHERE Module_Id = $id")->fetch_assoc();
$trades = $conn->query("SELECT * FROM Trades");

if (!$module) {
    echo "Module not found!";
    exit;
}

if (isset($_POST['update'])) {
    $name = $_POST['module_name'];
    $trade_id = $_POST['trade_id'];

    $stmt = $conn->prepare("UPDATE Modules SET Module_Name = ?, Trade_Id = ? WHERE Module_Id = ?");
    $stmt->bind_param("sii", $name, $trade_id, $id);
    $stmt->execute();

    header("Location: modules.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Module</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
  <h3>Edit Module</h3>

  <form method="post" class="mt-3">
    <div class="mb-3">
      <label class="form-label">Module Name</label>
      <input type="text" name="module_name" class="form-control" value="<?= htmlspecialchars($module['Module_Name']) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Trade</label>
      <select name="trade_id" class="form-select" required>
        <?php while($t = $trades->fetch_assoc()): ?>
          <option value="<?= $t['Trade_Id'] ?>" <?= $t['Trade_Id'] == $module['Trade_Id'] ? 'selected' : '' ?>>
            <?= $t['Trade_Name'] ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <button type="submit" name="update" class="btn btn-success">Update</button>
    <a href="modules.php" class="btn btn-secondary">Back</a>
  </form>
</div>

</body>
</html>
