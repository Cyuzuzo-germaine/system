<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "GIKONKO_TSS");

// Fetch trainees and modules for the form
$trainees = $conn->query("SELECT Trainee_Id, FirstNames, LastName FROM Trainees");
$modules = $conn->query("SELECT Module_Id, Module_Name FROM Modules");

// Handle insert
if (isset($_POST['save'])) {
    $trainee = $_POST['trainee_id'];
    $module = $_POST['module_id'];
    $formative = $_POST['formative'];
    $summative = $_POST['summative'];
    $total = $formative + $summative;

    $stmt = $conn->prepare("INSERT INTO Marks (Trainee_Id, Module_Id, Formative_Assessment, Summative_Assessment, Total_Marks)
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiii", $trainee, $module, $formative, $summative, $total);
    $stmt->execute();
    header("Location: marks.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM Marks WHERE Mark_Id = $id");
    header("Location: marks.php");
    exit;
}

// Fetch marks list
$marks = $conn->query("SELECT m.Mark_Id, t.FirstNames, t.LastName, mo.Module_Name, 
                              m.Formative_Assessment, m.Summative_Assessment, m.Total_Marks
                       FROM Marks m
                       JOIN Trainees t ON m.Trainee_Id = t.Trainee_Id
                       JOIN Modules mo ON m.Module_Id = mo.Module_Id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Marks Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php @include 'navbar.php'; ?>

<div class="container mt-4">
  <h3 class="mb-4">Manage Marks</h3>

  <!-- Form to Add Marks -->
  <form method="post" class="row g-3 mb-4">
    <div class="col-md-3">
      <select name="trainee_id" class="form-select" required>
        <option value="">Select Trainee</option>
        <?php while($t = $trainees->fetch_assoc()): ?>
          <option value="<?= $t['Trainee_Id'] ?>"><?= $t['FirstNames'] . " " . $t['LastName'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-3">
      <select name="module_id" class="form-select" required>
        <option value="">Select Module</option>
        <?php while($m = $modules->fetch_assoc()): ?>
          <option value="<?= $m['Module_Id'] ?>"><?= $m['Module_Name'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-2">
      <input type="number" name="formative" min="0" max="50" class="form-control" placeholder="Formative (/50)" required>
    </div>
    <div class="col-md-2">
      <input type="number" name="summative" min="0" max="50" class="form-control" placeholder="Summative (/50)" required>
    </div>
    <div class="col-md-2">
      <button type="submit" name="save" class="btn btn-success w-100">Add Marks</button>
    </div>
  </form>

  <!-- Table of Marks -->
  <table class="table table-bordered bg-white shadow-sm">
    <thead class="table-info">
      <tr>
        <th>#</th>
        <th>Trainee</th>
        <th>Module</th>
        <th>Formative</th>
        <th>Summative</th>
        <th>Total</th>
        <th>Competency</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $marks->fetch_assoc()): ?>
      <tr>
        <td><?= $row['Mark_Id'] ?></td>
        <td><?= $row['FirstNames'] . ' ' . $row['LastName'] ?></td>
        <td><?= $row['Module_Name'] ?></td>
        <td><?= $row['Formative_Assessment'] ?></td>
        <td><?= $row['Summative_Assessment'] ?></td>
        <td><?= $row['Total_Marks'] ?></td>
        <td>
          <?= $row['Total_Marks'] >= 70 ? "<span class='badge bg-success'>Competent</span>" : "<span class='badge bg-danger'>NYC</span>" ?>
        </td>
        <td>
          <a href="edit_mark.php?id=<?= $row['Mark_Id'] ?>" class="btn btn-sm btn-primary">Edit</a>
          <a href="marks.php?delete=<?= $row['Mark_Id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
