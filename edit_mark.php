<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "GIKONKO_TSS");

// Get the mark ID from the URL
if (!isset($_GET['id'])) {
    header("Location: marks.php");
    exit;
}

$id = $_GET['id'];

// Fetch the existing mark data
$result = $conn->query("SELECT * FROM Marks WHERE Mark_Id = $id");
$mark = $result->fetch_assoc();

// Update the record
if (isset($_POST['update'])) {
    $formative = $_POST['formative'];
    $summative = $_POST['summative'];
    $total = $formative + $summative;

    $stmt = $conn->prepare("UPDATE Marks SET Formative_Assessment = ?, Summative_Assessment = ?, Total_Marks = ? WHERE Mark_Id = ?");
    $stmt->bind_param("iiii", $formative, $summative, $total, $id);
    $stmt->execute();

    header("Location: marks.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Marks</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light ">

<?php @include 'navbar.php'; ?>

<div class="container mt-5">
  <div class="card shadow mt-5  ">
    <div class="card-header bg-primary text-white">
      <h4>Edit Marks for Mark ID: <?= $mark['Mark_Id'] ?></h4>
    </div>
    <div class="card-body">
      <form method="post" >
        <div class="mb-3">
          <label for="formative" class="form-label">Formative Assessment (/50)</label>
          <input type="number" name="formative" id="formative" class="form-control" value="<?= $mark['Formative_Assessment'] ?>" min="0" max="50" required>
        </div>
        <div class="mb-3">
          <label for="summative" class="form-label">Summative Assessment (/50)</label>
          <input type="number" name="summative" id="summative" class="form-control" value="<?= $mark['Summative_Assessment'] ?>" min="0" max="50" required>
        </div>
        <button type="submit" name="update" class="btn btn-success">Update Marks</button>
        <a href="marks.php" class="btn btn-secondary ms-2">Cancel</a>
      </form>
    </div>
  </div>
</div>
</body>
</html>
