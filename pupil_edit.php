<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "school.php"; // Ensure the connection is correctly made

// Check if pupil ID is provided
if (!isset($_GET['id'])) {
    echo "No pupil ID provided.";
    exit;
}

$pupilID = intval($_GET['id']);

// Fetch pupil information from database
$pupilQuery = "SELECT * FROM PUPILS WHERE Pupil_ID = $pupilID";
$pupilResult = mysqli_query($conn, $pupilQuery);
$pupil = mysqli_fetch_assoc($pupilResult);

if (!$pupil) {
    echo "Pupil not found.";
    exit;
}

// Fetch guardians from database
$guardianQuery = "SELECT Guardian_ID, First_Name, Last_Name FROM Guardians";
$guardianResult = mysqli_query($conn, $guardianQuery);

// Fetch classes from database
$classQuery = "SELECT Class_ID, Name FROM Classes";
$classResult = mysqli_query($conn, $classQuery);

// Handle form submission (update pupil)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $townCity = $_POST['town_city'];
    $postcode = $_POST['postcode'];
    $medicalInfo = $_POST['medical_info'];
    $yearGroup = $_POST['year_group'];
    $classID = $_POST['class_id'];
    $guardian1ID = $_POST['guardian1_id'];
    $guardian2ID = empty($_POST['guardian2_id']) ? NULL : $_POST['guardian2_id']; // Ensure this is NULL if empty

    // Update query
    $updateQuery = "UPDATE PUPILS SET
        First_Name = ?, Last_Name = ?, Dob = ?, Gender = ?, Street_Address = ?, Town_City = ?, 
        Postcode = ?, Medical_Information = ?, Year_Group = ?, Class_ID = ?, Guardian1_ID = ?, Guardian2_ID = ?
        WHERE Pupil_ID = ?";

    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, 'ssssssssssiii',
        $firstName, $lastName, $dob, $gender, $address, $townCity, $postcode, $medicalInfo,
        $yearGroup, $classID, $guardian1ID, $guardian2ID, $pupilID
    );

    if (mysqli_stmt_execute($stmt)) {
        echo "<p>Pupil updated successfully! <a href='pupil_list.php'>Back to list</a></p>";
    } else {
        echo "<p>Error updating pupil: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Pupil</title>
</head>
<body>
<h2>Edit Pupil</h2>
<form method="POST">
    <label>First Name:</label>
    <input type="text" name="first_name" value="<?= $pupil['First_Name'] ?>" required><br>

    <label>Last Name:</label>
    <input type="text" name="last_name" value="<?= $pupil['Last_Name'] ?>" required><br>

    <label>Date of Birth:</label>
    <input type="date" name="dob" value="<?= $pupil['Dob'] ?>" required><br>
    
    <label>Gender:</label>
    <select name="gender" required>
        <option value="">--Select--</option>
        <option value="Male" <?= $pupil['Gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $pupil['Gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
    </select><br>

    <label>Street Address:</label>
    <input type="text" name="address" value="<?= $pupil['Street_Address'] ?>" required><br>

    <label>Town/City:</label>
    <input type="text" name="town_city" value="<?= $pupil['Town_City'] ?>" required><br>

    <label>Postcode:</label>
    <input type="text" name="postcode" value="<?= $pupil['Postcode'] ?>" required><br>

    <label>Medical Information:</label>
    <textarea name="medical_info"><?= $pupil['Medical_Information'] ?></textarea><br>

    <label>Year Group:</label>
    <select name="year_group" required>
        <?php
        $yearGroups = ['Reception', 'Year 1', 'Year 2', 'Year 3', 'Year 4', 'Year 5', 'Year 6'];
        foreach ($yearGroups as $group) {
            $selected = $pupil['Year_Group'] == $group ? 'selected' : '';
            echo "<option value='$group' $selected>$group</option>";
        }
        ?>
    </select><br>

    <label>Class:</label>
    <select name="class_id" required>
        <?php
        mysqli_data_seek($classResult, 0);
        while ($row = mysqli_fetch_assoc($classResult)) {
            $selected = $pupil['Class_ID'] == $row['Class_ID'] ? 'selected' : '';
            echo "<option value='{$row['Class_ID']}' $selected>{$row['Name']}</option>";
        }
        ?>
    </select><br>

    <label>Guardian 1:</label>
    <select name="guardian1_id" required>
        <?php
        mysqli_data_seek($guardianResult, 0);
        while ($row = mysqli_fetch_assoc($guardianResult)) {
            $selected = $pupil['Guardian1_ID'] == $row['Guardian_ID'] ? 'selected' : '';
            echo "<option value='{$row['Guardian_ID']}' $selected>{$row['First_Name']} {$row['Last_Name']}</option>";
        }
        ?>
    </select><br>

    <label>Guardian 2 (optional):</label>
    <select name="guardian2_id">
        <option value="">--None--</option>
        <?php
        mysqli_data_seek($guardianResult, 0);
        while ($row = mysqli_fetch_assoc($guardianResult)) {
            $selected = $pupil['Guardian2_ID'] == $row['Guardian_ID'] ? 'selected' : '';
            echo "<option value='{$row['Guardian_ID']}' $selected>{$row['First_Name']} {$row['Last_Name']}</option>";
        }
        ?>
    </select><br>

    <button type="submit">Update Pupil</button>
</form>
</body>
</html>
