<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "school.php"; // Make sure this connects to your DB
require_once "auth.php";
require_role(['admin']);

// Initialize variables
$first_name = $last_name = $street_address = $town_city = $postcode = "";
$phone_number = $email = $subject = $salary = $background_check = "";
$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // Validation
    if (!$first_name) $errors[] = "First name is required.";
    if (!$last_name) $errors[] = "Last name is required.";
    if (!$street_address) $errors[] = "Street address is required.";
    if (!$town_city) $errors[] = "Town/City is required.";
    if (!$postcode) $errors[] = "Postcode is required.";
    if (!$email) $errors[] = "Email is required.";
    if (!$subject) $errors[] = "Subject is required.";
    if (!is_numeric($salary) && $salary !== "") $errors[] = "Salary must be a number.";

    if (empty($errors)) {
        $sql = "INSERT INTO teachers 
        (First_Name, Last_Name, Street_Address, Town_City, Postcode, Phone_Number, Email, Subject, Salary, Background_Check) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssdi", $first_name, $last_name, $street_address, $town_city, $postcode, $phone_number, $email, $subject, $salary, $background_check);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Teacher registered successfully.</p>";
        } else {
            echo "<p style='color: red;'>Error adding teacher: " . $conn->error . "</p>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Teacher</title>
    <style>
        body { font-family: Arial; max-width: 700px; margin: auto; padding: 20px; }
        label { display: block; margin-top: 10px; }
        input, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 20px; padding: 10px 25px; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>

<h2>Register Teacher</h2>

<?php
if (!empty($errors)) {
    echo '<div class="error"><ul>';
    foreach ($errors as $error) echo "<li>$error</li>";
    echo '</ul></div>';
}
?>

<form method="post" action="teachers_form.php">
   
    <label for="first_name">First Name</label>
    <input type="text" name="first_name" id="first_name" required>

    <label for="last_name">Last Name</label>
    <input type="text" name="last_name" id="last_name" required>

    <label for="street_address">Street Address</label>
    <textarea name="street_address" id="street_address" required></textarea>

    <label for="town_city">Town / City</label>
    <input type="text" name="town_city" id="town_city" required>

    <label for="postcode">Postcode</label>
    <input type="text" name="postcode" id="postcode" required>

    <label for="phone_number">Phone Number</label>
    <input type="text" name="phone_number" id="phone_number">

    <label for="email">Email</label>
    <input type="email" name="email" id="email" required>

    <label for="subject">Subject</label>
    <input type="text" name="subject" id="subject" required>

    <label for="salary">Salary</label>
    <input type="number" step="1" name="salary" id="salary">

    <label>
        <input type="checkbox" name="background_check" value="1"> Background Check Completed
    </label>

    <button type="submit">Register Teacher</button>
</form>

</body>
</html>
