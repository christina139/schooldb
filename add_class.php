<?php
require_once "school.php"; // DB connection
require_once "auth.php";
require_role(['admin']);

$success = "";
$error = "";

// Get teachers for dropdown
$teacher_result = $conn->query("SELECT Teacher_ID, First_Name, Last_Name FROM teachers");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $class_name = trim($_POST["class_name"]);
    $year_group = trim($_POST["year_group"]);
    $teacher_id = $_POST["teacher_id"];
    $capacity = $_POST["capacity"];

    if (!empty($class_name) && !empty($year_group) && !empty($teacher_id) && is_numeric($capacity)) {
        $stmt = $conn->prepare("INSERT INTO classes (Name, Year_Group, Teacher_ID, Capacity) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $class_name, $year_group, $teacher_id, $capacity);

        if ($stmt->execute()) {
            $success = "✅ Class added successfully!";
        } else {
            $error = "❌ Error adding class: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error = "Please fill in all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Class</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
            margin: auto;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Add New Class</h2>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="class_name" class="form-label">Class Name</label>
                <input type="text" class="form-control" id="class_name" name="class_name" required>
            </div>

            <div class="mb-3">
                <label for="year_group" class="form-label">Year Group</label>
                <select name="year_group" id="year_group" class="form-select" required>
                    <option value="">-- Select Year Group --</option>
                    <option value="Reception">Reception</option>
               <?php
                for ($i = 1; $i <= 6; $i++) {
                 echo "<option value='Year $i'>Year $i</option>";
                 }
                  ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="teacher_id" class="form-label">Assign Teacher</label>
                <select name="teacher_id" id="teacher_id" class="form-select" required>
                    <option value="">-- Select Teacher --</option>
                    <?php
                    while ($teacher = $teacher_result->fetch_assoc()) {
                        $tid = $teacher['Teacher_ID'];
                        $name = $teacher['First_Name'] . " " . $teacher['Last_Name'];
                        echo "<option value='$tid'>$name (ID: $tid)</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="capacity" class="form-label">Class Capacity</label>
                <input type="number" name="capacity" id="capacity" class="form-control" min="1" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Add Class</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
