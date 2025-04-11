<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once "school.php";
require_once "auth.php";
require_role(['admin']); // Ensure that only admin can access this page

$staff = null;
$errors = [];
$success = "";

// Search staff by ID
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    $stmt = $conn->prepare("SELECT * FROM SUPPORT_STAFF WHERE Staff_ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $staff = $result->fetch_assoc();
    $stmt->close();
}

// Update staff
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $id = intval($_POST["id"]);
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $role = trim($_POST["role"]);
    $email = trim($_POST["email"]);
    $phone_number = trim($_POST["phone_number"]);
    $street_address = trim($_POST["street_address"]);
    $town_city = trim($_POST["town_city"]);
    $postcode = trim($_POST["postcode"]);
    $class_id = $_POST["class_id"] ? intval($_POST["class_id"]) : null;
    $teacher_id = $_POST["teacher_id"] ? intval($_POST["teacher_id"]) : null;

    // Validate required fields
    if (!$first_name) $errors[] = "First name is required.";
    if (!$last_name) $errors[] = "Last name is required.";
    if (!$role) $errors[] = "Role is required.";

    if (empty($errors)) {
        $sql = "UPDATE SUPPORT_STAFF SET First_Name=?, Last_Name=?, Role=?, Email=?, Phone_Number=?, Street_Address=?, Town_City=?, Postcode=?, Class_ID=?, Teacher_ID=? WHERE Staff_ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssiii", $first_name, $last_name, $role, $email, $phone_number, $street_address, $town_city, $postcode, $class_id, $teacher_id, $id);

        if ($stmt->execute()) {
            $success = "✅ Support staff updated successfully.";
        } else {
            $errors[] = "❌ Update failed: " . $conn->error;
        }
        $stmt->close();
    }
}

// Delete staff
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $id = intval($_POST["id"]);
    $stmt = $conn->prepare("DELETE FROM SUPPORT_STAFF WHERE Staff_ID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = "✅ Support staff deleted successfully.";
        $staff = null;
    } else {
        $errors[] = "❌ Delete failed: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update or Delete Support Staff</title>
    <style>
        body { font-family: Arial; max-width: 700px; margin: auto; padding: 20px; }
        label { display: block; margin-top: 10px; }
        input, textarea, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 20px; }
        .error { color: red; margin-top: 10px; }
        .success { color: green; margin-top: 10px; }
    </style>
</head>
<body>

<h2>Update or Delete Support Staff</h2>

<!-- Search Form -->
<form method="get">
    <label for="id">Enter Support Staff ID</label>
    <input type="number" name="id" id="id" required>
    <button type="submit">Search</button>
</form>

<?php if ($staff): ?>
    <!-- Update/Delete Form -->
    <form method="post">
        <input type="hidden" name="id" value="<?php echo $staff['Staff_ID']; ?>">

        <label>First Name</label>
        <input type="text" name="first_name" value="<?php echo $staff['First_Name']; ?>" required>

        <label>Last Name</label>
        <input type="text" name="last_name" value="<?php echo $staff['Last_Name']; ?>" required>

        <label>Role</label>
        <input type="text" name="role" value="<?php echo $staff['Role']; ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo $staff['Email']; ?>">

        <label>Phone Number</label>
        <input type="text" name="phone_number" value="<?php echo $staff['Phone_Number']; ?>">

        <label>Street Address</label>
        <textarea name="street_address" required><?php echo $staff['Street_Address']; ?></textarea>

        <label>Town / City</label>
        <input type="text" name="town_city" value="<?php echo $staff['Town_City']; ?>" required>

        <label>Postcode</label>
        <input type="text" name="postcode" value="<?php echo $staff['Postcode']; ?>" required>

        <label>Class (Optional)</label>
        <input type="text" name="class_id" value="<?php echo $staff['Class_ID']; ?>">

        <label>Teacher (Optional)</label>
        <input type="text" name="teacher_id" value="<?php echo $staff['Teacher_ID']; ?>">

        <button type="submit" name="update">Update</button>
        <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this staff member?');" style="background-color:red;color:white;">Delete</button>
    </form>
<?php endif; ?>

<?php
if (!empty($errors)) {
    echo "<div class='error'><ul>";
    foreach ($errors as $e) echo "<li>$e</li>";
    echo "</ul></div>";
}

if ($success) {
    echo "<div class='success'>$success</div>";
}
?>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
