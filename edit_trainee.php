<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "GIKONKO_TSS");

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: trainees.php");
    exit;
}

// Get trades for the dropdown
$trades = $conn->query("SELECT * FROM Trades");

// Fetch trainee info
$stmt = $conn->prepare("SELECT * FROM Trainees WHERE Trainee_Id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$trainee = $result->fetch_assoc();

if (!$trainee) {
    echo "Trainee not found.";
    exit;
}

// Handle update
if (isset($_POST['update'])) {
    $fname = $_POST['firstnames'];
    $lname = $_POST['lastname'];
    $gender = $_POST['gender'];
    $trade_id = $_POST['trade_id'];

    $update = $conn->prepare("UPDATE Trainees SET FirstNames=?, LastName=?, Gender=?, Trade_Id=? WHERE Trainee_Id=?");
    $update->bind_param("sssii", $fname, $lname, $gender, $trade_id, $id);
    $update->execute();

    header("Location: trainees.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Trainee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
  <h3>Edit Trainee</h3>
  <form method="post" class="row g-3">
    <div class="col-md-4">
      <label>First Names</label>
      <input type="text" name="firstnames" class="form-control" value="<?= $trainee['FirstNames'] ?>" required>
    </div>
    <div class="col-md-4">
      <label>Last Name</label>
      <input type="text" name="lastname" class="form-control" value="<?= $trainee['LastName'] ?>" required>
    </div>
    <div class="col-md-4">
      <label>Gender</label>
      <select name="gender" class="form-select" required>
        <option value="Male" <?= $trainee['Gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $trainee['Gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
      </select>
    </div>
    <div class="col-md-6">
      <label>Trade</label>
      <select name="trade_id" class="form-select" required>
        <?php while ($t = $trades->fetch_assoc()): ?>
          <option value="<?= $t['Trade_Id'] ?>" <?= $trainee['Trade_Id'] == $t['Trade_Id'] ? 'selected' : '' ?>>
            <?= $t['Trade_Name'] ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-6 d-flex align-items-end">
      <button type="submit" name="update" class="btn btn-primary me-2">Update</button>
      <a href="trainees.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

</body>
</html>
