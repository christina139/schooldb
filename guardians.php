<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once "school.php"; // Database connection

// Initialize variables
$first_name = $last_name = $relationship = $street_address = $town_city = $postcode = "";
$mobile_phone = $home_phone = $email = "";
$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $relationship = $_POST["relationship"];
    $street_address = trim($_POST["street_address"]);
    $town_city = trim($_POST["town_city"]);
    $postcode = trim($_POST["postcode"]);
    $mobile_phone = preg_replace("/[^0-9+]/", "", trim($_POST["mobile_phone"]));
    $home_phone = trim($_POST["home_phone"]);
    $home_phone = $home_phone ? preg_replace("/[^0-9+]/", "", $home_phone) : null;
    
    $email = trim($_POST["email"]);

    // Required field validation
    if (!$first_name) $errors[] = "First name is required.";
    if (!$last_name) $errors[] = "Last name is required.";
    if (!$relationship) $errors[] = "Relationship is required.";
    if (!$street_address) $errors[] = "Street address is required.";
    if (!$town_city) $errors[] = "Town/City is required.";
    if (!$postcode) $errors[] = "Postcode is required.";

    // Email validation (optional)
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        $sql = "INSERT INTO guardians 
        (First_Name, Last_Name, Relationship, Street_Address, Town_City, Postcode, Mobile_Phone, Home_Phone, Email) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssss", $first_name, $last_name, $relationship, $street_address, $town_city, $postcode, $mobile_phone, $home_phone, $email);


        if ($stmt->execute()) {
            echo "<p style='color: green;'>Guardian registered successfully.</p>";
            // Reset form fields
            $first_name = $last_name = $relationship = $street_address = $town_city = $postcode = "";
            $mobile_phone = $home_phone = $email = "";
        } else {
            echo "<p style='color: red;'>Error adding guardian: " . $conn->error . "</p>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Guardian</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 700px; margin: auto; }
        label { display: block; margin-top: 12px; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 20px; padding: 10px 25px; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>

<h2>Register Guardian</h2>

<?php
if (!empty($errors)) {
    echo '<div class="error"><ul>';
    foreach ($errors as $error) echo "<li>$error</li>";
    echo '</ul></div>';
}
?>

<form method="post" action="guardians.php">
    <label for="first_name">First Name</label>
    <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>

    <label for="last_name">Last Name</label>
    <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>

    <label for="relationship">Relationship</label>
    <select name="relationship" id="relationship" required>
        <option value="">Select Relationship</option>
        <option value="Father" <?php if ($relationship == 'Father') echo 'selected'; ?>>Father</option>
        <option value="Mother" <?php if ($relationship == 'Mother') echo 'selected'; ?>>Mother</option>
        <option value="Guardian" <?php if ($relationship == 'Guardian') echo 'selected'; ?>>Guardian</option>
        <option value="Carer" <?php if ($relationship == 'Carer') echo 'selected'; ?>>Carer</option>
        <option value="Grandparent" <?php if ($relationship == 'Grandparent') echo 'selected'; ?>>Grandparent</option>
        <option value="Other" <?php if ($relationship == 'Other') echo 'selected'; ?>>Other</option>
    </select>

    <label for="street_address">Street Address</label>
    <textarea name="street_address" id="street_address" required><?php echo htmlspecialchars($street_address); ?></textarea>

    <label for="town_city">Town / City</label>
    <input type="text" name="town_city" id="town_city" value="<?php echo htmlspecialchars($town_city); ?>" required>

    <label for="postcode">Postcode</label>
    <input type="text" name="postcode" id="postcode" value="<?php echo htmlspecialchars($postcode); ?>" required>

    <label for="mobile_phone">Mobile Phone</label>
    <input type="text" name="mobile_phone" id="mobile_phone" value="<?php echo htmlspecialchars($mobile_phone); ?>">

    <label for="home_phone">Home Phone (Optional)</label>
    <input type="text" name="home_phone" id="home_phone" value="<?php echo htmlspecialchars($home_phone); ?>">


    <label for="email">Email</label>
    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>">

    <button type="submit">Register Guardian</button>
</form>

</body>
</html>
