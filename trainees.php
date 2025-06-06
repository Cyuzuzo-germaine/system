<?php
session_start();




if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
    if ($_SESSION['role'] !== 'DOS') {
        die("Access denied: You do not have permission to delete trainees.");
    }
    $id = intval($_GET['delete']);
    // proceed with deletion
}




if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$conn = new mysqli("localhost", "root", "", "GIKONKO_TSS");
$trades = $conn->query("SELECT * FROM Trades");

// Handle insert
if (isset($_POST['save'])) {
    $fname = $_POST['firstnames'];
    $lname = $_POST['lastname'];
    $gender = $_POST['gender'];
    $trade = $_POST['trade_id'];

    $stmt = $conn->prepare("INSERT INTO Trainees (FirstNames, LastName, Gender, Trade_Id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $fname, $lname, $gender, $trade);
    $stmt->execute();
    header("Location: trainees.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM Trainees WHERE Trainee_Id=$id");
    header("Location: trainees.php");
    exit;
}

// Fetch trainees
$trainees = $conn->query("SELECT t.Trainee_Id, t.FirstNames, t.LastName, t.Gender, tr.Trade_Name 
                          FROM Trainees t JOIN Trades tr ON t.Trade_Id = tr.Trade_Id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Trainees Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php @include 'navbar.php'; 






// optional, if you extracted the nav ?>

<div class="container mt-4">
  <h3 class="mb-4">Manage Trainees</h3>

  <!-- Form to Add Trainee -->
  <form method="post" class="row g-3 mb-4">
    <div class="col-md-3">
      <input type="text" name="firstnames" class="form-control" placeholder="First Names" required>
    </div>
    <div class="col-md-3">
      <input type="text" name="lastname" class="form-control" placeholder="Last Name" required>
    </div>
    <div class="col-md-2">
      <select name="gender" class="form-select" required>
        <option value="">Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>
    </div>
    <div class="col-md-2">
      <select name="trade_id" class="form-select" required>
        <option value="">Trade</option>
        <?php while($t = $trades->fetch_assoc()): ?>
          <option value="<?= $t['Trade_Id'] ?>"><?= $t['Trade_Name'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-2">
      <button type="submit" name="save" class="btn btn-success w-100">Add Trainee</button>
    </div>
  </form>

  <!-- Table of Trainees -->
  <table class="table table-bordered bg-white shadow-sm">
    <thead class="table-success">
      <tr>
        <th>ID</th>
        <th>First Names</th>
        <th>Last Name</th>
        <th>Gender</th>
        <th>Trade</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $trainees->fetch_assoc()): ?>
      <tr>
        <td><?= $row['Trainee_Id'] ?></td>
        <td><?= $row['FirstNames'] ?></td>
        <td><?= $row['LastName'] ?></td>
        <td><?= $row['Gender'] ?></td>
        <td><?= $row['Trade_Name'] ?></td>
        <td>
          <a href="edit_trainee.php?id=<?= $row['Trainee_Id'] ?>" class="btn btn-sm btn-primary">Edit</a>
          <a href="trainees.php?delete=<?= $row['Trainee_Id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this trainee?')">Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
