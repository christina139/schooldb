<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once "school.php";

// Default query to fetch all pupils
$query = "SELECT * FROM PUPILS";

// Check if search query is provided
if (isset($_POST['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_POST['search']);
    $query = "SELECT * FROM PUPILS WHERE First_Name LIKE '%$searchTerm%' OR Last_Name LIKE '%$searchTerm%'";
}

$pupils = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Pupils</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            background-color: #4CAF50;
            color: white;
            padding: 20px 0;
            margin: 0;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .actions {
            margin-top: 10px;
        }

        .search-bar {
            width: 80%;
            margin: 20px auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .search-bar input[type="text"] {
            padding: 10px;
            width: 80%;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .search-bar button {
            padding: 10px;
            margin-left: 130px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<h2>Pupil List</h2>

<!-- Search Form -->
<div class="search-bar">
    <form method="POST">
        <input type="text" name="search" placeholder="Search by first or last name..." value="<?= isset($_POST['search']) ? $_POST['search'] : '' ?>">
        <button type="submit">Search</button>
    </form>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Year Group</th>
        <th>Class ID</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($pupils)) { ?>
        <tr>
            <td><?php echo $row['Pupil_ID']; ?></td>
            <td><?php echo $row['First_Name']; ?> <?php echo $row['Last_Name']; ?></td>
            <td><?php echo $row['Year_Group']; ?></td>
            <td><?php echo $row['Class_ID']; ?></td>
            <td class="actions">
                <a href="pupil_edit.php?id=<?php echo $row['Pupil_ID']; ?>">Edit</a> |
                <a href="pupil_delete.php?id=<?php echo $row['Pupil_ID']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
