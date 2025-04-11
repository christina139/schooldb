<?php
// Correct variable names and assign them values
$db_server = "localhost";
$db_username = "root";
$db_password = ""; // or "your_password" if you've set one
$db_name = "St_Alphonsus_Primary_School"; // replace with your actual DB name

// Create the connection
$conn = new mysqli($db_server, $db_username, $db_password, $db_name);




// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    //echo "✅ Connected successfully!";
}
?>

