<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once "school.php"; // Your DB connection file

$errors = [];
$success = "";
$teacher_id = 1; // Replace this with session or logged-in user ID

// Get pupil ID from form or URL
$pupil_id = isset($_GET['Pupil_id']) ? intval($_GET['Pupil_id']) : (isset($_POST['Pupil_id']) ? intval($_POST['Pupil_id']) : 0);
$grade_id = isset($_POST["grade_id"]) ? intval($_POST["grade_id"]) : 0;

// Get all pupils
$students = [];
$result = $conn->query("SELECT * FROM PUPILS");
while ($row = $result->fetch_assoc()) $students[] = $row;

// Get all teachers
$teachers = [];
$result = $conn->query("SELECT * FROM TEACHERS");
while ($row = $result->fetch_assoc()) $teachers[] = $row;

// Fetch all subjects (will be filtered by teacher after selection)
$subjects = [];
if (isset($_POST['teacher_id'])) {
    $teacher_id = intval($_POST['teacher_id']);
    $stmt = $conn->prepare("SELECT * FROM SUBJECTS WHERE Teacher_ID = ?");
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_id = intval($_POST["subject_id"]);
    $grade = trim($_POST["grade"]);

    if (!$subject_id || !$grade || !$pupil_id) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        // Check if a grade already exists for this pupil + subject
        $check_stmt = $conn->prepare("SELECT Grade_ID FROM GRADES WHERE Pupil_ID = ? AND Subject_ID = ?");
        $check_stmt->bind_param("ii", $pupil_id, $subject_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) {
            // Grade exists, update it
            $check_stmt->bind_result($existing_grade_id);
            $check_stmt->fetch();
            $update_stmt = $conn->prepare("UPDATE GRADES SET Grade = ?, Date_Graded = CURDATE() WHERE Grade_ID = ?");
            $update_stmt->bind_param("si", $grade, $existing_grade_id);
            $update_stmt->execute();
            $update_stmt->close();
            $success = "Grade updated successfully.";
        } else {
            // Insert new grade
            $insert_stmt = $conn->prepare("INSERT INTO GRADES (Pupil_ID, Subject_ID, Grade, Date_Graded, Teacher_ID) VALUES (?, ?, ?, CURDATE(), ?)");
            $insert_stmt->bind_param("iisi", $pupil_id, $subject_id, $grade, $teacher_id);
            $insert_stmt->execute();
            $insert_stmt->close();
            $success = "Grade added successfully.";
        }
        $check_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add or Update Grade</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: auto; padding: 20px; }
        label { margin-top: 12px; display: block; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { padding: 10px 25px; margin-top: 20px; }
        .error { color: red; margin-top: 10px; }
        .success { color: green; margin-top: 10px; }
    </style>
</head>
<body>

<h2>Add or Update Grade</h2>

<?php if (!empty($errors)) { ?>
    <div class="error"><ul><?php foreach ($errors as $e) echo "<li>$e</li>"; ?></ul></div>
<?php } ?>

<?php if ($success) { echo "<div class='success'>$success</div>"; } ?>

<form method="POST">
    <!-- Select Teacher -->
    <label for="teacher_id">Select Teacher</label>
    <select name="teacher_id" id="teacher_id" onchange="this.form.submit()" required>
        <option value="">Select Teacher</option>
        <?php foreach ($teachers as $teacher): ?>
            <option value="<?php echo $teacher['Teacher_ID']; ?>" <?php if ($teacher_id == $teacher['Teacher_ID']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($teacher['First_Name'] . " " . $teacher['Last_Name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- Select Pupil -->
    <label for="Pupil_id">Select Pupil</label>
    <select name="Pupil_id" id="Pupil_id" required>
        <option value="">Select Student</option>
        <?php foreach ($students as $student): ?>
            <option value="<?php echo $student['Pupil_ID']; ?>" <?php if ($pupil_id == $student['Pupil_ID']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($student['First_Name'] . " " . $student['Last_Name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- Select Subject -->
    <label for="subject_id">Select Subject</label>
    <select name="subject_id" id="subject_id" required>
        <option value="">Select Subject</option>
        <?php foreach ($subjects as $subject): ?>
            <option value="<?php echo $subject['Subject_ID']; ?>" <?php if (isset($subject_id) && $subject_id == $subject['Subject_ID']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($subject['Subject_Name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- Select Grade -->
    <label for="grade">Grade</label>
    <select name="grade" id="grade" required>
        <option value="">Select Grade</option>
        <option value="Emerging" <?php if (isset($grade) && $grade == 'Emerging') echo 'selected'; ?>>Emerging</option>
        <option value="Expected" <?php if (isset($grade) && $grade == 'Expected') echo 'selected'; ?>>Expected</option>
        <option value="Exceeding" <?php if (isset($grade) && $grade == 'Exceeding') echo 'selected'; ?>>Exceeding</option>
    </select>

    <button type="submit">Save Grade</button>
</form>

</body>
</html>
