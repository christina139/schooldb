<?php
require_once "school.php"; // make sure this contains your DB connection

// Fetch pupils
$pupil_result = mysqli_query($conn, "SELECT Pupil_ID, First_Name, Last_Name FROM PUPILS");
$class_result = mysqli_query($conn, "SELECT Class_ID, Name FROM CLASSES");

$success = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pupil_id = $_POST["pupil_id"];
    $class_id = $_POST["class_id"];

    if ($pupil_id && $class_id) {
        $stmt = $conn->prepare("INSERT INTO PUPIL_CLASSES (Pupil_ID, Class_ID) VALUES (?, ?)");
        $stmt->bind_param("ii", $pupil_id, $class_id);

        if ($stmt->execute()) {
            $success = "✅ Pupil assigned to class successfully!";
        } else {
            $error = "❌ Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error = "Please select both a pupil and a class.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Pupil to Class</title>
    <style>
        body { font-family: Arial; max-width: 600px; margin: 40px auto; background: #f9f9f9; padding: 30px; }
        label { display: block; margin-top: 15px; }
        select, button { width: 100%; padding: 10px; margin-top: 5px; }
        button { background: #3498db; color: white; border: none; cursor: pointer; margin-top: 20px; }
        button:hover { background: #2980b9; }
        .message { margin-top: 20px; padding: 10px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<h2>Assign Pupil to Class</h2>

<?php if ($success): ?>
    <div class="message success"><?= $success ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="message error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="">
    <label for="pupil_id">Select Pupil</label>
    <select name="pupil_id" required>
        <option value="">-- Choose Pupil --</option>
        <?php while ($pupil = mysqli_fetch_assoc($pupil_result)): ?>
            <option value="<?= $pupil['Pupil_ID'] ?>">
                <?= $pupil['First_Name'] . " " . $pupil['Last_Name'] ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label for="class_id">Select Class</label>
    <select name="class_id" required>
        <option value="">-- Choose Class --</option>
        <?php while ($class = mysqli_fetch_assoc($class_result)): ?>
            <option value="<?= $class['Class_ID'] ?>"><?= $class['Name'] ?></option>
        <?php endwhile; ?>
    </select>

    <button type="submit">Assign to Class</button>
</form>

</body>
</html>
