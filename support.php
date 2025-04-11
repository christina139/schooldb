<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "school.php"; // connects to your DB
require_once "auth.php";

// DB connection
$db_server = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "St_Alphonsus_Primary_School";

$conn = new mysqli($db_server, $db_username, $db_password, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 🟢 Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $role = $_POST['role'];
    $phone = $_POST['phone_number'];
    $email = $_POST['email'];
    $address = $_POST['street_address'];
    $town = $_POST['town_city'];
    $postcode = $_POST['postcode'];
    $classID = !empty($_POST['class_id']) ? $_POST['class_id'] : null;
    $teacherID = !empty($_POST['teacher_id']) ? $_POST['teacher_id'] : null;

    $stmt = $conn->prepare("INSERT INTO Support_Staff (First_Name, Last_Name, Role, Phone_Number, Email, Street_Address, Town_City, Postcode, Class_ID, Teacher_ID) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssssssii", $firstName, $lastName, $role, $phone, $email, $address, $town, $postcode, $classID, $teacherID);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Support staff registered successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// 🔽 Fetch dropdowns
$teachers = [];
$classes = [];

$teacher_query = "SELECT Teacher_ID, First_Name, Last_Name FROM TEACHERS";
$class_query = "SELECT Class_ID, Name FROM CLASSES";

$teacher_result = $conn->query($teacher_query);
if ($teacher_result && $teacher_result->num_rows > 0) {
    while ($row = $teacher_result->fetch_assoc()) {
        $teachers[] = $row;
    }
}

$class_result = $conn->query($class_query);
if ($class_result && $class_result->num_rows > 0) {
    while ($row = $class_result->fetch_assoc()) {
        $classes[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Support Staff</title>
    <style>
        body { font-family: Arial; max-width: 700px; margin: auto; padding: 20px; }
        label { display: block; margin-top: 10px; }
        input, textarea, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 20px; padding: 10px 25px; background-color: #28a745; color: white; border: none; }
    </style>
</head>
<body>

<h2>Register Support Staff</h2>

<form method="POST" action="">
    <label>First Name</label>
    <input type="text" name="first_name" required>

    <label>Last Name</label>
    <input type="text" name="last_name" required>

    <label>Role</label>
    <select name="role" required>
        <option value="">-- Select Role --</option>
        <?php
        $roles = ['Teaching Assistant','School Nurse','Cleaner','Librarian','Caretaker','IT Support','Administrator','Janitor','Technician','Counselor','Other'];
        foreach ($roles as $r) {
            echo "<option value=\"$r\">$r</option>";
        }
        ?>
    </select>

    <label>Phone Number</label>
    <input type="text" name="phone_number">

    <label>Email</label>
    <input type="email" name="email">

    <label>Street Address</label>
    <textarea name="street_address" required></textarea>

    <label>Town / City</label>
    <input type="text" name="town_city" required>

    <label>Postcode</label>
    <input type="text" name="postcode" required>

    <label>Class (Optional)</label>
    <select name="class_id">
        <option value="">-- Select Class --</option>
        <?php foreach ($classes as $class): ?>
            <option value="<?= $class['Class_ID'] ?>"><?= $class['Name'] ?></option>
        <?php endforeach; ?>
    </select>

    <label>Teacher (Optional)</label>
    <select name="teacher_id">
        <option value="">-- Assign to Teacher --</option>
        <?php foreach ($teachers as $teacher): ?>
            <option value="<?= $teacher['Teacher_ID'] ?>">
                <?= $teacher['First_Name'] . " " . $teacher['Last_Name'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Register Staff</button>
</form>

</body>
</html>
