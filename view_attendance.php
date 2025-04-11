<?php
require_once "school.php"; // DB connection

$search_query = "";
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["search"])) {
    $search_query = trim($_GET["search"]);
}

// Fetch attendance records (optionally filtered by name)
$sql = "
    SELECT a.Attendance_ID, p.First_Name, p.Last_Name, c.Name AS Class_Name, 
           a.Date, a.Status, a.Reason
    FROM ATTENDANCES a
    JOIN PUPILS p ON a.Pupil_ID = p.Pupil_ID
    JOIN CLASSES c ON a.Class_ID = c.Class_ID
    WHERE p.First_Name LIKE ? OR p.Last_Name LIKE ?
    ORDER BY a.Date DESC
";

$stmt = $conn->prepare($sql);
$search_param = "%" . $search_query . "%";
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; padding: 30px; font-family: Arial, sans-serif; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 25px; }
        .search-form input { width: 250px; }
        table { margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Attendance History</h2>

    <!-- Search Form -->
    <form method="get" class="d-flex justify-content-center mb-4">
        <input type="text" name="search" class="form-control" placeholder="Search by pupil name..." value="<?= htmlspecialchars($search_query) ?>">
        <button type="submit" class="btn btn-primary ms-2">Search</button>
    </form>

    <!-- Attendance Table -->
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Pupil</th>
                <th>Class</th>
                <th>Date</th>
                <th>Status</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row["Attendance_ID"] ?></td>
                    <td><?= $row["First_Name"] . " " . $row["Last_Name"] ?></td>
                    <td><?= $row["Class_Name"] ?></td>
                    <td><?= $row["Date"] ?></td>
                    <td><?= $row["Status"] ?></td>
                    <td><?= $row["Reason"] ?: "-" ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">No attendance records found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
