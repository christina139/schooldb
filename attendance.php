<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once "school.php";  // Assuming this file contains your database connection

$message = "";

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["class_id"]) && isset($_POST["pupil_id"])) {
        $class_id = $_POST["class_id"];
        $pupil_id = $_POST["pupil_id"];
        $date = $_POST["date"];
        $status = $_POST["status"];
        $reason = $_POST["reason"] ?? null;

        // Insert attendance into database
        $stmt = $conn->prepare("INSERT INTO ATTENDANCES (pupil_ID, Class_ID, Date, Status, Reason) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $pupil_id, $class_id, $date, $status, $reason);

        if ($stmt->execute()) {
            $message = "✅ Attendance recorded successfully.";
        } else {
            $message = "❌ Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "❌ Please select a class and a pupil.";
    }
}

// Fetch classes
$classes_result = mysqli_query($conn, "SELECT Class_ID, Name FROM CLASSES");

// Fetch pupils if class is selected
$pupils_result = [];
if (isset($_POST["class_id"])) {
    $class_id = $_POST["class_id"];
    $pupils_query = "SELECT p.Pupil_ID, p.First_Name, p.Last_Name
                     FROM PUPILS p
                     JOIN PUPIL_CLASSES pc ON p.Pupil_ID = pc.Pupil_ID
                     WHERE pc.Class_ID = ?";
    $stmt = $conn->prepare($pupils_query);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $pupils_result = $stmt->get_result();
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        label { display: block; margin-top: 15px; font-weight: bold; }
        select, input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 25px;
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            border-radius: 5px;
        }
        .msg {
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .success { background-color: #e6ffed; color: #1d643b; }
        .error { background-color: #ffe6e6; color: #a00; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Record Attendance</h2>

        <?php if (!empty($message)): ?>
            <div class="msg <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Step 1: Select Class -->
        <form method="POST" action="">
            <label for="class_id">Class</label>
            <select name="class_id" id="class_id" required onchange="this.form.submit()">
                <option value="">-- Select Class --</option>
                <?php while ($class = mysqli_fetch_assoc($classes_result)): ?>
                    <option value="<?= $class['Class_ID'] ?>" <?= isset($class_id) && $class_id == $class['Class_ID'] ? 'selected' : '' ?>>
                        <?= $class['Name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        <!-- Step 2: Select Pupil (Only after class is selected) -->
        <?php if (isset($class_id)): ?>
            <form method="POST" action="">
                <input type="hidden" name="class_id" value="<?= $class_id ?>" />
                <label for="pupil_id">Pupil</label>
                <select name="pupil_id" id="pupil_id" required>
                    <option value="">-- Select Pupil --</option>
                    <?php while ($pupil = mysqli_fetch_assoc($pupils_result)): ?>
                        <option value="<?= $pupil['Pupil_ID'] ?>"><?= $pupil['First_Name'] . " " . $pupil['Last_Name'] ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="date">Date</label>
                <input type="date" name="date" required>

                <label for="status">Status</label>
                <select name="status" required>
                    <option value="">-- Select Status --</option>
                    <option value="Present">Present</option>
                    <option value="Illness">Illness</option>
                    <option value="Medical Appointment">Medical Appointment</option>
                    <option value="Authorised Absence">Authorised Absence</option>
                    <option value="Unauthorised Absence">Unauthorised Absence</option>
                    <option value="Late">Late</option>
                </select>

                <label for="reason">Reason (optional)</label>
                <textarea name="reason" rows="3" placeholder="Enter reason if applicable"></textarea>

                <button type="submit">Submit Attendance</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
