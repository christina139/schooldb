<?php
require_once "school.php";
require_once "auth.php";
require_role(['admin']);

$search = "";
$teachers = [];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET["search"])) {
        $search = trim($_GET["search"]);
        $like = "%$search%";
        $stmt = $conn->prepare("SELECT * FROM teachers WHERE First_Name LIKE ? OR Last_Name LIKE ?");
        $stmt->bind_param("ss", $like, $like);
    } else {
        $stmt = $conn->prepare("SELECT * FROM teachers");
    }

    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Teachers</title>
    <style>
        body {
            font-family: Arial;
            margin: 40px;
            background-color: #f9f9f9;
        }

        h2 {
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 8px;
            width: 250px;
        }

        button {
            padding: 8px 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #e0e0e0;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .no-data {
            margin-top: 20px;
            color: #666;
        }
    </style>
</head>
<body>

<h2>Teacher Records</h2>

<form method="get" action="view_teachers.php">
    <input type="text" name="search" placeholder="Search by name..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit">Search</button>
</form>

<?php if (count($teachers) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Teacher ID</th>
                <th>Name</th>
                <th>Town/City</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Phone</th>
                <th>Salary</th>
                <th>Background Check</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($teachers as $t): ?>
            <tr>
                <td><?php echo $t['Teacher_ID']; ?></td>
                <td><?php echo $t['First_Name'] . " " . $t['Last_Name']; ?></td>
                <td><?php echo $t['Town_City']; ?></td>
                <td><?php echo $t['Email']; ?></td>
                <td><?php echo $t['Subject']; ?></td>
                <td><?php echo $t['Phone_Number']; ?></td>
                <td>£<?php echo number_format($t['Salary'], 2); ?></td>
                <td><?php echo $t['Background_Check'] ? '✔️ Yes' : '❌ No'; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="no-data">No teachers found.</p>
<?php endif; ?>

</body>
</html>
