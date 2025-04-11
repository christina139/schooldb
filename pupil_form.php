<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once "school.php"; // Database connection
require_once "auth.php";  // Assuming you have authentication logic here

// Initialize variables to avoid undefined variable warnings
$first_name = $last_name = $dob = $gender = $street_address = $town_city = $postcode = $medical_info = $year_group = $class_id = $guardian1_id = $guardian2_id = "";

// Fetch guardians from the database
$guardianQuery = "SELECT Guardian_ID, First_Name, Last_Name FROM GUARDIANS";
$guardianResult = mysqli_query($conn, $guardianQuery);

// Fetch classes from the database (to use for filtering)
$classQuery = "SELECT Class_ID, Name, Year_Group FROM CLASSES";
$classResult = mysqli_query($conn, $classQuery);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $dob = trim($_POST["dob"]);
    $gender = $_POST["gender"];
    $street_address = trim($_POST["street_address"]);
    $town_city = trim($_POST["town_city"]);
    $postcode = trim($_POST["postcode"]);
    $medical_info = trim($_POST["medical_info"]);
    $year_group = $_POST["year_group"];
    $class_id = $_POST["class_id"];
    $guardian1_id = $_POST["guardian1_id"];
    $guardian2_id = !empty($_POST["guardian2_id"]) ? $_POST["guardian2_id"] : null;

    // Validation (you can extend this as needed)
    $errors = [];
    if (!$first_name) $errors[] = "First name is required.";
    if (!$last_name) $errors[] = "Last name is required.";
    if (!$dob) $errors[] = "Date of birth is required.";
    if (!$gender) $errors[] = "Gender is required.";
    if (!$street_address) $errors[] = "Street address is required.";
    if (!$town_city) $errors[] = "Town/City is required.";
    if (!$postcode) $errors[] = "Postcode is required.";
    if (!$year_group) $errors[] = "Year group is required.";
    if (!$class_id) $errors[] = "Class is required.";
    if (!$guardian1_id) $errors[] = "Guardian 1 is required.";

    if (empty($errors)) {
        // Insert pupil data into the PUPILS table
        $sql = "INSERT INTO PUPILS (First_Name, Last_Name, Dob, Gender, Street_Address, Town_City, Postcode, Medical_Information, Year_Group, Class_ID, Guardian1_ID, Guardian2_ID) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssii", $first_name, $last_name, $dob, $gender, $street_address, $town_city, $postcode, $medical_info, $year_group, $class_id, $guardian1_id, $guardian2_id);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Pupil registered successfully.</p>";
            // Reset form fields after success
            $first_name = $last_name = $dob = $gender = $street_address = $town_city = $postcode = $medical_info = $year_group = $class_id = $guardian1_id = $guardian2_id = "";
        } else {
            echo "<p style='color: red;'>Error adding pupil: " . $conn->error . "</p>";
        }
        $stmt->close();
    } else {
        // Display errors
        echo "<p style='color: red;'>" . implode('<br>', $errors) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pupil Registration</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 700px; margin: auto; }
        label { display: block; margin-top: 12px; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 20px; padding: 10px 25px; }
        .error { color: red; margin-top: 10px; }
    </style>
    <script>
        // JavaScript to dynamically update class options based on selected year group
        function updateClasses() {
            var yearGroup = document.getElementById('year_group').value;
            var classSelect = document.getElementById('class_id');
            classSelect.innerHTML = '<option value="">--Select Class--</option>'; // Reset class options

            <?php
            // Store the class data in a JavaScript variable so we can filter it
            $classData = [];
            while ($class = mysqli_fetch_assoc($classResult)) {
                $classData[] = $class;
            }
            ?>

            var classes = <?php echo json_encode($classData); ?>;
            for (var i = 0; i < classes.length; i++) {
                if (classes[i].Year_Group === yearGroup) {
                    var option = document.createElement("option");
                    option.value = classes[i].Class_ID;
                    option.textContent = classes[i].Name;
                    classSelect.appendChild(option);
                }
            }
        }
    </script>
</head>
<body>

<h2>Pupil Registration Form</h2>

<form method="POST" action="pupil_form.php">
    <!-- First Name -->
    <label for="first_name">First Name:</label>
    <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>

    <!-- Last Name -->
    <label for="last_name">Last Name:</label>
    <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>

    <!-- Date of Birth -->
    <label for="dob">Date of Birth:</label>
    <input type="date" name="dob" value="<?php echo htmlspecialchars($dob); ?>" required>

    <!-- Gender -->
    <label for="gender">Gender:</label>
    <select name="gender" required>
        <option value="Male" <?php if ($gender == 'Male') echo 'selected'; ?>>Male</option>
        <option value="Female" <?php if ($gender == 'Female') echo 'selected'; ?>>Female</option>
    </select>

    <!-- Street Address -->
    <label for="street_address">Street Address:</label>
    <input type="text" name="street_address" value="<?php echo htmlspecialchars($street_address); ?>" required>

    <!-- Town / City -->
    <label for="town_city">Town / City:</label>
    <input type="text" name="town_city" value="<?php echo htmlspecialchars($town_city); ?>" required>

    <!-- Postcode -->
    <label for="postcode">Postcode:</label>
    <input type="text" name="postcode" value="<?php echo htmlspecialchars($postcode); ?>" required>

    <!-- Medical Information -->
    <label for="medical_info">Medical Information (optional):</label>
    <textarea name="medical_info"><?php echo htmlspecialchars($medical_info); ?></textarea>

    <!-- Year Group -->
    <label for="year_group">Year Group:</label>
    <select name="year_group" id="year_group" required onchange="updateClasses()">
        <option value="">--Select Year Group--</option>
        <option value="Reception" <?php if ($year_group == 'Reception') echo 'selected'; ?>>Reception</option>
        <option value="Year 1" <?php if ($year_group == 'Year 1') echo 'selected'; ?>>Year 1</option>
        <option value="Year 2" <?php if ($year_group == 'Year 2') echo 'selected'; ?>>Year 2</option>
        <option value="Year 3" <?php if ($year_group == 'Year 3') echo 'selected'; ?>>Year 3</option>
        <option value="Year 4" <?php if ($year_group == 'Year 4') echo 'selected'; ?>>Year 4</option>
        <option value="Year 5" <?php if ($year_group == 'Year 5') echo 'selected'; ?>>Year 5</option>
        <option value="Year 6" <?php if ($year_group == 'Year 6') echo 'selected'; ?>>Year 6</option>
    </select>

    <!-- Class (Filtered by Year Group) -->
    <label for="class_id">Class:</label>
    <select name="class_id" id="class_id" required>
        <option value="">--Select Class--</option>
    </select>

    <!-- Guardian 1 -->
    <label for="guardian1_id">Guardian 1:</label>
    <select name="guardian1_id" required>
        <option value="">--Select Guardian--</option>
        <?php
        mysqli_data_seek($guardianResult, 0);
        while ($guardian = mysqli_fetch_assoc($guardianResult)) {
            echo "<option value='" . $guardian['Guardian_ID'] . "'>" . $guardian['First_Name'] . " " . $guardian['Last_Name'] . "</option>";
        }
        ?>
    </select>

    <!-- Guardian 2 (Optional) -->
    <label for="guardian2_id">Guardian 2 (optional):</label>
    <select name="guardian2_id">
        <option value="">--None--</option>
        <?php
        mysqli_data_seek($guardianResult, 0);
        while ($guardian = mysqli_fetch_assoc($guardianResult)) {
            echo "<option value='" . $guardian['Guardian_ID'] . "'>" . $guardian['First_Name'] . " " . $guardian['Last_Name'] . "</option>";
        }
        ?>
    </select>

    <button type="submit">Register Pupil</button>
</form>

</body>
</html>
