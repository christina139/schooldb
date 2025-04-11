<?php
session_start();
require_once "school.php";  // Ensure this has your DB connection

error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {

        // Debugging: Output entered username and password
        echo "Username entered: $username<br>";
        echo "Password entered: " . htmlspecialchars($password) . "<br>"; // Properly display the entered password

        // Query to fetch user by username
        $stmt = $conn->prepare("SELECT id, password, role FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        // Debugging: Check if the user is found
        echo "Rows found: " . $stmt->num_rows . "<br>";

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $hashed_password, $role);
            $stmt->fetch();

            // Trim the password and hash to ensure no extra spaces are affecting the match
            $password = trim($password);
            $hashed_password = trim($hashed_password);

            // Debugging: Output the entered password and the hash from the DB
            echo "Entered password: " . htmlspecialchars($password) . "<br>"; // Display entered password
            echo "Hash from DB: " . htmlspecialchars($hashed_password) . "<br>"; // Display hash from DB

            // Compare entered password with the hash
            $password_verified = password_verify($password, $hashed_password);

            // Debugging: Output the result of password verification
            echo "Password verification result: " . ($password_verified ? "Success" : "Failed") . "<br>";

            if ($password_verified) {
                $_SESSION["user_id"] = $id;
                $_SESSION["username"] = $username;
                $_SESSION["role"] = $role;
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid credentials.";
            }
        } else {
            $error = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - St Alphonsus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Login to St Alphonsus Primary School</h2>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <form method="post" class="p-4 border bg-white rounded shadow">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
