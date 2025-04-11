<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once "school.php";
require_once "auth.php";
require_role(['admin']);

// Initialize
$teacher = null;
$errors = [];
$success = "";

// Fetch teacher details by ID
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE Teacher_ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();
    $stmt->close();
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $id = intval($_POST["id"]);
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $street_address = trim($_POST["street_address"]);
    $town_city = trim($_POST["town_city"]);
    $postcode = trim($_POST["postcode"]);
    $phone_number = trim($_POST["phone_number"]);
    $email = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $salary = trim($_POST["salary"]);
    $background_check = isset($_POST["background_check"]) ? 1 : 0;

    if (!$first_name) $errors[] = "First name is required.";
    if (!$last_name) $errors[] = "Last name is required.";
    if (!$email) $errors[] = "Email is required.";
    if (!is_numeric($salary) && $salary !== "") $errors[] = "Salary must be numeric.";

    if (empty($errors)) {
        $sql = "UPDATE teachers SET First_Name=?, Last_Name=?, Street_Address=?, Town_City=?, Postcode=?, Phone_Number=?, Email=?, Subject=?, Salary=?, Background_Check=? WHERE Teacher_ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssdii", $first_name, $last_name, $street_address, $town_city, $postcode, $phone_number, $email, $subject, $salary, $background_check, $id);
        if ($stmt->execute()) {
            $success = "Teacher updated successfully.";
        } else {
            $errors[] = "Update failed: " . $conn->error;
        }
        $stmt->close();
    }
}

// Handle delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $id = intval($_POST["id"]);
    $stmt = $conn->prepare("DELETE FROM teachers WHERE Teacher_ID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = "Teacher deleted successfully.";
        $teacher = null;
    } else {
        $errors[] = "Delete failed: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update/Delete Teacher</title>
    <style>
        body { font-family: Arial; max-width: 700px; margin: auto; padding: 20px; }
        label { display: block; margin-top: 10px; }
        input, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 20px; }
        .error { color: red; margin-top: 10px; }
        .success { color: green; margin-top: 10px; }
    </style>
</head>
<body>

<h2>Update or Delete Teacher</h2>

<form method="get">
    <label for="id">Enter Teacher ID</label>
    <input type="number" name="id" id="id" required>
    <button type="submit">Search</button>
</form>

<?php if ($teacher): ?>
    <form method="post">
        <input type="hidden" name="id" value="<?php echo $teacher['Teacher_ID']; ?>">

        <label>First Name</label>
        <input type="text" name="first_name" value="<?php echo $teacher['First_Name']; ?>" required>

        <label>Last Name</label>
        <input type="text" name="last_name" value="<?php echo $teacher['Last_Name']; ?>" required>

        <label>Street Address</label>
        <textarea name="street_address" required><?php echo $teacher['Street_Address']; ?></textarea>

        <label>Town / City</label>
        <input type="text" name="town_city" value="<?php echo $teacher['Town_City']; ?>" required>

        <label>Postcode</label>
        <input type="text" name="postcode" value="<?php echo $teacher['Postcode']; ?>" required>

        <label>Phone Number</label>
        <input type="text" name="phone_number" value="<?php echo $teacher['Phone_Number']; ?>">

        <label>Email</label>
        <input type="email" name="email" value="<?php echo $teacher['Email']; ?>" required>

        <label>Subject</label>
        <input type="text" name="subject" value="<?php echo $teacher['Subject']; ?>" required>

        <label>Salary</label>
        <input type="number" step="1" name="salary" value="<?php echo $teacher['Salary']; ?>">

        <label>
            <input type="checkbox" name="background_check" value="1" <?php if ($teacher['Background_Check']) echo "checked"; ?>>
            Background Check Completed
        </label>

        <button type="submit" name="update">Update</button>
        <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this teacher?');" style="background-color:red;color:white;">Delete</button>
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
