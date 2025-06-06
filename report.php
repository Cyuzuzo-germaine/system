<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "GIKONKO_TSS");

// Query to get trainees with their total marks summed up per trainee
$query = "
    SELECT 
        t.Trainee_Id,
        t.FirstNames,
        t.LastName,
        tr.Trade_Name,
        IFNULL(SUM(m.Total_Marks), 0) AS TotalMarks
    FROM Trainees t
    LEFT JOIN Trades tr ON t.Trade_Id = tr.Trade_Id
    LEFT JOIN Marks m ON t.Trainee_Id = m.Trainee_Id
    GROUP BY t.Trainee_Id
    ORDER BY t.LastName, t.FirstNames
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Trainee Performance Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<?php @include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Trainee Performance Report</h2>
    <table class="table table-bordered bg-white shadow-sm">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Trade</th>
                <th>Total Marks (out of 100)</th>
                <th>Classification</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): 
                $classification = ($row['TotalMarks'] >= 70) ? "Competent" : "Not Yet Competent (NYC)";
            ?>
            <tr>
                <td><?= $row['Trainee_Id'] ?></td>
                <td><?= htmlspecialchars($row['FirstNames'] . ' ' . $row['LastName']) ?></td>
                <td><?= htmlspecialchars($row['Trade_Name']) ?></td>
                <td><?= $row['TotalMarks'] ?></td>
                <td>
                    <span class="badge <?= ($classification === "Competent") ? 'bg-success' : 'bg-danger' ?>">
                        <?= $classification ?>
                    </span>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

</body>
</html>

                