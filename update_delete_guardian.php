<?php
require_once "school.php"; // Database connection

// Initialize variables
$guardian = null;
$errors = [];
$success = "";

// Check if a Guardian ID is provided in the URL
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["guardian_id"])) {
    $guardian_id = $_GET["guardian_id"];

    // Query to fetch the guardian with the given ID
    $stmt = $conn->prepare("SELECT * FROM guardians WHERE Guardian_ID = ?");
    $stmt->bind_param("i", $guardian_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if the guardian exists
    if ($result->num_rows > 0) {
        $guardian = $result->fetch_assoc();
    } else {
        $errors[] = "❌ No guardian found with the provided ID.";
    }
    $stmt->close();
}

// Update guardian details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $guardian_id = $_POST["guardian_id"];
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $relationship = $_POST["relationship"];
    $street_address = trim($_POST["street_address"]);
    $town_city = trim($_POST["town_city"]);
    $postcode = trim($_POST["postcode"]);
    $mobile_phone = trim($_POST["mobile_phone"]);
    $home_phone = trim($_POST["home_phone"]);
    $email = trim($_POST["email"]);

    // Validate required fields
    if (!$first_name) $errors[] = "First name is required.";
    if (!$last_name) $errors[] = "Last name is required.";
    if (!$relationship) $errors[] = "Relationship is required.";
    if (!$street_address) $errors[] = "Street address is required.";
    if (!$town_city) $errors[] = "Town/City is required.";
    if (!$postcode) $errors[] = "Postcode is required.";

    if (empty($errors)) {
        $sql = "UPDATE guardians SET First_Name=?, Last_Name=?, Relationship=?, Street_Address=?, Town_City=?, Postcode=?, Mobile_Phone=?, Home_Phone=?, Email=? WHERE Guardian_ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssi", $first_name, $last_name, $relationship, $street_address, $town_city, $postcode, $mobile_phone, $home_phone, $email, $guardian_id);

        if ($stmt->execute()) {
            $success = "✅ Guardian updated successfully.";
        } else {
            $errors[] = "❌ Error updating guardian: " . $conn->error;
        }

        $stmt->close();
    }
}

// Delete guardian
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $guardian_id = $_POST["guardian_id"];
    $stmt = $conn->prepare("DELETE FROM guardians WHERE Guardian_ID = ?");
    $stmt->bind_param("i", $guardian_id);
    if ($stmt->execute()) {
        $success = "✅ Guardian deleted successfully.";
        $guardian = null;
    } else {
        $errors[] = "❌ Error deleting guardian: " . $conn->error;
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Update or Delete Guardian</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: auto; padding: 20px; }
        label { display: block; margin-top: 12px; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 20px; padding: 10px 25px; }
        .error { color: red; margin-top: 10px; }
        .success { color: green; margin-top: 10px; }
    </style>
</head>
<body>

<h2>Update or Delete Guardian</h2>

<!-- Check if guardian ID is not provided or the form is submitted -->
<?php if (!$guardian && empty($errors)): ?>
    <p>Please provide a Guardian ID to search for.</p>
    <form method="get">
        <label for="guardian_id">Enter Guardian ID</label>
        <input type="number" name="guardian_id" required>
        <button type="submit">Search</button>
    </form>
<?php endif; ?>

<?php if ($guardian): ?>
    <form method="post">
        <input type="hidden" name="guardian_id" value="<?php echo $guardian['Guardian_ID']; ?>">

        <label for="first_name">First Name</label>
        <input type="text" name="first_name" value="<?php echo $guardian['First_Name']; ?>" required>

        <label for="last_name">Last Name</label>
        <input type="text" name="last_name" value="<?php echo $guardian['Last_Name']; ?>" required>

        <label for="relationship">Relationship</label>
        <select name="relationship" required>
            <option value="">Select Relationship</option>
            <option value="Father" <?php echo $guardian['Relationship'] == "Father" ? 'selected' : ''; ?>>Father</option>
            <option value="Mother" <?php echo $guardian['Relationship'] == "Mother" ? 'selected' : ''; ?>>Mother</option>
            <option value="Guardian" <?php echo $guardian['Relationship'] == "Guardian" ? 'selected' : ''; ?>>Guardian</option>
            <option value="Carer" <?php echo $guardian['Relationship'] == "Carer" ? 'selected' : ''; ?>>Carer</option>
            <option value="Grandparent" <?php echo $guardian['Relationship'] == "Grandparent" ? 'selected' : ''; ?>>Grandparent</option>
            <option value="Other" <?php echo $guardian['Relationship'] == "Other" ? 'selected' : ''; ?>>Other</option>
        </select>

        <label for="street_address">Street Address</label>
        <textarea name="street_address" required><?php echo $guardian['Street_Address']; ?></textarea>

        <label for="town_city">Town / City</label>
        <input type="text" name="town_city" value="<?php echo $guardian['Town_City']; ?>" required>

        <label for="postcode">Postcode</label>
        <input type="text" name="postcode" value="<?php echo $guardian['Postcode']; ?>" required>

        <label for="mobile_phone">Mobile Phone</label>
        <input type="text" name="mobile_phone" value="<?php echo $guardian['Mobile_Phone']; ?>">

        <label for="home_phone">Home Phone</label>
        <input type="text" name="home_phone" value="<?php echo $guardian['Home_Phone']; ?>">

        <label for="email">Email</label>
        <input type="email" name="email" value="<?php echo $guardian['Email']; ?>">

        <button type="submit" name="update">Update Guardian</button>
        <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this guardian?');" style="background-color:red;color:white;">Delete Guardian</button>
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
