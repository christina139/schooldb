<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "school.php";  // Ensure DB connection

// Check if pupil ID is provided
if (isset($_GET['id'])) {
    $pupilID = intval($_GET['id']);  // Get the pupil ID from the URL

    // Prepare SQL query to delete pupil record
    $deleteQuery = "DELETE FROM PUPILS WHERE Pupil_ID = ?";

    // Prepare the query
    $stmt = mysqli_prepare($conn, $deleteQuery);
    
    // Bind parameters (this prevents SQL injection)
    mysqli_stmt_bind_param($stmt, 'i', $pupilID);
    
    // Execute the delete query
    if (mysqli_stmt_execute($stmt)) {
        echo "Pupil deleted successfully!";
    } else {
        echo "Error deleting pupil: " . mysqli_error($conn);
    }
    
    // Redirect back to the pupil list after deletion (optional)
    header("Location: view_pupils.php");
    exit;
} else {
    echo "No pupil ID provided for deletion.";
}
?>
