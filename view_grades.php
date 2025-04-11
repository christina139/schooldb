<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once "school.php"; // Database connection

// Assuming you have an admin logged in, you can set a flag or role to check if they are an admin
$is_admin = true;  // Set this flag to true if the user is an admin

// Fetch all students
$students = [];
$subjects = [];
$grades = [];

// Fetch all students
$student_stmt = $conn->prepare("SELECT * FROM PUPILS");
$student_stmt->execute();
$student_result = $student_stmt->get_result();

while ($row = $student_result->fetch_assoc()) {
    $students[] = $row;
}
$student_stmt->close();

// Fetch all subjects
$subject_stmt = $conn->prepare("SELECT * FROM SUBJECTS");
$subject_stmt->execute();
$subject_result = $subject_stmt->get_result();

while ($subject_row = $subject_result->fetch_assoc()) {
    $subjects[] = $subject_row;
}
$subject_stmt->close();

// Fetch grades for each student and each subject
foreach ($students as $student) {
    foreach ($subjects as $subject) {
        $grade_stmt = $conn->prepare("SELECT Grade FROM GRADES WHERE Pupil_ID = ? AND Subject_ID = ?");
        $grade_stmt->bind_param("ii", $student['Pupil_ID'], $subject['Subject_ID']); // Change here to use "Pupil_ID"
        $grade_stmt->execute();
        $grade_result = $grade_stmt->get_result();
        
        // Check if a grade exists, otherwise mark as "N/A"
        if ($grade_result->num_rows > 0) {
            $grade = $grade_result->fetch_assoc();
            $grades[$student['Pupil_ID']][$subject['Subject_ID']] = $grade['Grade']; // Use "Pupil_ID" here
        } else {
            $grades[$student['Pupil_ID']][$subject['Subject_ID']] = "N/A"; // Use "Pupil_ID" here
        }
    }
}
$grade_stmt->close();
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Grades</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 900px; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
    </style>
</head>
<body>

<h2>View All Students' Grades</h2>

<table>
    <thead>
        <tr>
            <th>Student Name</th>
            <?php foreach ($subjects as $subject) { ?>
                <th><?php echo $subject['Subject_Name']; ?></th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($students as $student) { ?>
            <tr>
                <td><?php echo $student['First_Name'] . " " . $student['Last_Name']; ?></td>
                <?php foreach ($subjects as $subject) { ?>
                    <td><?php echo isset($grades[$student['Pupil_ID']][$subject['Subject_ID']]) ? $grades[$student['Pupil_ID']][$subject['Subject_ID']] : 'N/A'; ?></td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>

</body>
</html>

<?php
$conn->close();
?>
