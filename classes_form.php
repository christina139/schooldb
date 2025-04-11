
<?php
// Include the database connection
require('school.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the values from the form
    $pupil_id = $_POST['pupil_id'];
    $class_id = $_POST['class_id'];

    // Insert the student into the class
    $sql = "INSERT INTO pupil_classes (Pupil_ID, Class_ID) VALUES ('$pupil_id', '$class_id')";
    if (mysqli_query($conn, $sql)) {
        echo "Student assigned to class successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Fetch all pupils from the database
$pupils_sql = "SELECT Pupil_ID, First_Name, Last_Name FROM PUPILS";
$pupils_result = mysqli_query($conn, $pupils_sql);

// Fetch all classes from the database
$classes_sql = "SELECT Class_ID, Name FROM CLASSES";
$classes_result = mysqli_query($conn, $classes_sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Registration</title>
</head>
<body>

<h1>Assign Student to Class</h1>

<form action="class_registration.php" method="POST">
    <label for="pupil_id">Select Pupil:</label>
    <select name="pupil_id" id="pupil_id" required>
        <?php
        // Display pupils as options in the select dropdown
        while ($row = mysqli_fetch_assoc($pupils_result)) {
            echo "<option value='{$row['Pupil_ID']}'>{$row['First_Name']} {$row['Last_Name']}</option>";
        }
        ?>
    </select><br><br>

    <label for="class_id">Select Class:</label>
    <select name="class_id" id="class_id" required>
        <?php
        // Display classes as options in the select dropdown
        while ($row = mysqli_fetch_assoc($classes_result)) {
            echo "<option value='{$row['Class_ID']}'>{$row['Name']}</option>";
        }
        ?>
    </select><br><br>

    <input type="submit" value="Assign Student to Class">
</form>

</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
