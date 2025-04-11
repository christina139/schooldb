<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Assuming you have a connection to the database
require_once "school.php"; // This file should contain your database connection

// Initialize the search query variable
$search_query = '';

// Check if there's a search request
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
}

// Query to get classes (with search filter if provided)
$query = "SELECT * FROM CLASSES";
if ($search_query !== '') {
    $query .= " WHERE Name LIKE ?";
}

$stmt = $conn->prepare($query);
if ($search_query !== '') {
    $search_query = "%" . $search_query . "%"; // Add wildcards for the LIKE search
    $stmt->bind_param("s", $search_query);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Classes</title>
</head>
<body>
    <h2>Classes</h2>

    <!-- Search Bar -->
    <form method="POST" action="">
        <input type="text" name="search" placeholder="Search by Class Name" value="<?= htmlspecialchars($search_query) ?>" />
        <button type="submit">Search</button>
    </form>

    <!-- Classes Table -->
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Class ID</th>
                <th>Class Name</th>
                <th>Year Group</th>
                <th>Capacity</th>
                <th>Teacher ID</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Class_ID']) ?></td>
                    <td><?= htmlspecialchars($row['Name']) ?></td>
                    <td><?= htmlspecialchars($row['Year_Group']) ?></td>
                    <td><?= htmlspecialchars($row['Capacity']) ?></td>
                    <td><?= htmlspecialchars($row['Teacher_ID']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php $stmt->close(); ?>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
