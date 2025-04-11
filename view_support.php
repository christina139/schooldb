<?php
require_once "school.php"; // Your DB connection

// Initialize variables
$search_query = "";

// Handle search query
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
}

// Fetch support staff from the database
$sql = "SELECT * FROM SUPPORT_STAFF WHERE First_Name LIKE ? OR Last_Name LIKE ?";
$stmt = $conn->prepare($sql);
$search_param = "%" . $search_query . "%";
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Support Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            margin-top: 20px;
        }
        .table {
            margin-top: 20px;
        }
        .search-form input {
            width: 250px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <h2 class="text-center mb-4">View Support Staff</h2>

        <!-- Search Form -->
        <form method="get" class="search-form d-flex justify-content-center">
            <input type="text" name="search" class="form-control" placeholder="Search by name..." value="<?php echo $search_query; ?>">
            <button type="submit" class="btn btn-primary ms-2">Search</button>
        </form>

        <!-- Support Staff Table -->
        <table class="table table-bordered mt-4">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // Output the rows
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['Support_Staff_ID'] . "</td>";
                        echo "<td>" . $row['First_Name'] . "</td>";
                        echo "<td>" . $row['Last_Name'] . "</td>";
                        echo "<td>" . $row['Role'] . "</td>";
                        echo "<td>" . $row['Phone_Number'] . "</td>";
                        echo "<td>" . $row['Email'] . "</td>";
                        echo "<td><a href='update_delete_support_staff.php?id=" . $row['Support_Staff_ID'] . "' class='btn btn-warning btn-sm'>Update</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No support staff found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$stmt->close();
$conn->close();
?>
