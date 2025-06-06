<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "GIKONKO_TSS");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$trainees = $conn->query("SELECT COUNT(*) AS total FROM Trainees")->fetch_assoc()['total'];
$modules = $conn->query("SELECT COUNT(*) AS total FROM Modules")->fetch_assoc()['total'];
$competent = $conn->query("SELECT COUNT(*) AS total FROM Marks WHERE (Formative_Assessment + Summative_Assessment) >= 70")->fetch_assoc()['total'];
$nyc = $conn->query("SELECT COUNT(*) AS total FROM Marks WHERE (Formative_Assessment + Summative_Assessment) < 70")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>GIKONKO TSS Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    
    body {
      margin: 0;
      padding: 0;
      overflow-x: hidden;
      background-image: url(FvUKNfeXsAEVjFW.jpg);
      background-repeat: no-repeat;
      background-size: cover;
      opacity: 0.8;

    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      width: 220px;
      background-color:rgb(154, 161, 159);
      color: white;
      transition: all 0.3s ease;
      z-index: 1000;
    }

    .sidebar.collapsed {
      width: 60px;
    }

    .sidebar h4 {
      font-size: 1.2rem;
    }

    .sidebar a {
      color: white;
      padding: 12px 20px;
      display: block;
      text-decoration: none;
      transition: 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color:rgb(11, 54, 75);
    }

    .sidebar.collapsed a span {
      display: none;
    }

    .sidebar.collapsed h4 {
      display: none;
    }

    .main {
      margin-left: 220px;
      padding: 20px;
      transition: all 0.3s ease;
    }

    .main.collapsed {
      margin-left: 60px;
    }

    #toggleBtn {
      position: fixed;
      top: 15px;
      left: 230px;
      background-color:rgb(22, 86, 56);
      color: white;
      border: none;
      z-index: 1100;
      transition: left 0.3s ease;
    }

    #toggleBtn.collapsed {
      left: 70px;
    }
  </style>
</head>
<body>

  <div class="sidebar" id="sidebar">
    <h4 class="text-center py-5 MT">DOS MANAGES SYSTEM</h4>
    <a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> <span>Dashboard</span></a>
    <a href="trainees.php"><i class="bi bi-person"></i> <span>Trainees</span></a>
    <a href="trades.php"><i class="bi bi-tools"></i> <span>Trades</span></a>
    <a href="modules.php"><i class="bi bi-journals"></i> <span>Modules</span></a>
    <a href="marks.php"><i class="bi bi-card-checklist"></i> <span>Marks</span></a>
    <a href="report.php"><i class="bi bi-file-earmark-text"></i> <span>Reports</span></a>
    <a href="auth.php"><i class="bi bi-box-arrow-left"></i> <span>Logouts</span></a>
    <hr class="text-white">
    <div class="text-center text-white px-2">
      Welcome, <?= htmlspecialchars($_SESSION['username']) ?><br>
     <a href="auth.php"><i class="bi bi-box-arrow-left"></i> <span>Logouts</span></a>
    </div>
  </div>

  <button id="toggleBtn" class="btn btn-sm">
    ☰
  </button>

  <div class="main" id="mainContent " >
    <h2 class="mb-5 mx-5" style="margin-top: 0px;">Dashboard</h2>
    <div class="row text-center mx-5"  style="margin-top: 300px;">
      <div class="col-md-3">
        <div class="card  text-dark mb-3">
          <div class="card-body">
            <h5>Total Trainees</h5>
            <h2><?= $trainees ?></h2>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text- mb-3">
          <div class="card-body">
            <h5>Total Modules</h5>
            <h2><?= $modules ?></h2>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card  text-dark mb-3">
          <div class="card-body">
            <h5>Competent</h5>
            <h2><?= $competent ?></h2>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card  text-dark mb-3">
          <div class="card-body">
            <h5>Not Yet Competent</h5>
            <h2><?= $nyc ?></h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    const toggleBtn = document.getElementById('toggleBtn');
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('mainContent');

    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      main.classList.toggle('collapsed');
      toggleBtn.classList.toggle('collapsed');
    });
  </script>

  <!-- Optionally include Bootstrap icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</body>
</html>
