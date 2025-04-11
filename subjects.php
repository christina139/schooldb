<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "school.php"; // Database connection

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_id = trim($_POST["teacher_id"]);
    $subject_name = trim($_POST["subject_name"]);

    // Basic field validation
    if (!$teacher_id || !$subject_name) {
        $message = "<p style='color: red;'>All fields are required.</p>";
    } else {
        // Check if subject already exists
        $check_sql = "SELECT * FROM SUBJECTS WHERE Subject_Name = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $subject_name);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $message = "<p style='color: red;'>Subject already exists!</p>";
        } else {
            // Insert into DB
            $sql = "INSERT INTO SUBJECTS (Teacher_ID, Subject_Name) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $teacher_id, $subject_name);

            if ($stmt->execute()) {
                $message = "<p style='color: green;'>Subject registered successfully!</p>";
            } else {
                $message = "<p style='color: red;'>Error: " . $stmt->error . "</p>";
            }

            $stmt->close();
        }

        $check_stmt->close();
    }
}

// Fetch teachers for the dropdown
$teachers_result = mysqli_query($conn, "SELECT Teacher_ID, First_Name, Last_Name FROM TEACHERS");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Subject</title>
    <style>
        body { font-family: Arial; padding: 20px; max-width: 600px; margin: auto; }
        label { display: block; margin-top: 15px; }
        input, select, button { width: 100%; padding: 8px; margin-top: 5px; }
        button { background-color: #4CAF50; color: white; border: none; margin-top: 20px; }
        h2 { text-align: center; }
    </style>
</head>
<body>

<h2>Register New Subject</h2>

<?= $message ?>

<form method="POST" action="">
    <label for="teacher_id">Teacher</label>
    <select name="teacher_id" id="teacher_id" required>
        <option value="">-- Select a Teacher --</option>
        <?php while ($row = mysqli_fetch_assoc($teachers_result)): ?>
            <option value="<?= $row['Teacher_ID'] ?>">
                <?= htmlspecialchars($row['First_Name'] . " " . $row['Last_Name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label for="subject_name">Subject Name</label>
    <input type="text" name="subject_name" id="subject_name" required>

    <button type="submit">Register Subject</button>
</form>

</body>
</html>

<?php mysqli_close($conn); ?>
