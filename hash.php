<?php
$plain_password = "John@Doe123";
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

echo "Generated Hash: " . $hashed_password . "<br>";  // Copy this hash into your DB for testing.
?>
