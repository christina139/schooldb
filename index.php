<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "auth.php"; // Include the authentication logic
require_login(); // Ensure the user is logged in
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>St Alphonsus Primary School Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    /* Set the background image */
    body {
      background-image: url('back5.jpg'); /* Replace with your image URL */
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      color: white;
    }
    /* Custom CSS for the container */
.custom-container {
  max-width: 600px;           /* Limit the width of the container */
  margin-left: auto;          /* Center horizontally */
  margin-right: auto;         /* Center horizontally */
  padding: 20px;              /* Add padding to the container */
  background-color: rgba(0, 0, 0, 0.6); /* Dark background with some transparency */
  border-radius: 15px;        /* Rounded corners for the container */
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Subtle shadow for separation */
  color: white;               /* White text color for contrast */
  text-align: center;         /* Center text inside the container */
}

/* Circle container for "Hello, Admin" */
.hello-container {
  position: fixed;           /* Fix the position to the top-left corner */
  top: 20px;                 /* Distance from the top */
  left: 20px;                /* Distance from the left */
  padding: 10px 20px;        /* Some padding around the text */
  background-color: #007bff; /* Blue background color */
  border-radius: 50px;       /* Make it a circle with rounded corners */
  color: white;              /* White text color */
  font-weight: bold;         /* Bold text */
  font-size: 14px;           /* Set the font size */
  display: flex;             /* Use Flexbox to center the text */
  justify-content: center;   /* Center the text horizontally */
  align-items: center;       /* Center the text vertically */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Optional: Adds a small shadow */
}



    .logout-btn {
      position: absolute;
      top: 5px;
      right: 5px;
      background-color:white; 
      font-size: 14px;
        font-weight: bold;
      
    }

    .navbar {
      background-color: rgba(0, 0, 0, 0.7) !important; /* Make navbar background slightly transparent */
    }

    .container {
      background-color: rgba(0, 0, 0, 0.6); /* Add a dark overlay to the content container */
      border-radius: 10px;
      padding: 30px;
    }

    h1 {
      font-family: 'Arial', sans-serif;
      font-weight: bold;
      font-size: 2.5rem;
    }

    .navbar-brand,
    .nav-link {
      font-family: 'Arial', sans-serif;
      font-weight: bold;
    }

    .navbar-toggler-icon {
      background-color: white;
    }

    .nav-link {
      font-size: 1rem;
    }

    .navbar-nav .nav-item {
      margin-right: 15px;
    }

    .navbar-nav .nav-item a {
      transition: color 0.3s;
    }

    .navbar-nav .nav-item a:hover {
      color: #FFD700; /* Gold color on hover */
    }

    .nav-item span {
      font-weight: bold;
    }
  </style>
</head>
<body>
<div class="custom-container">
    <h1 class="mb-4">St Alphonsus Primary School Management System</h1>
</div>


      <a class="btn btn-light logout-btn" href="logout.php">Logout</a>
    
      <!-- User info -->
<ul class="navbar-nav">
  <li class="nav-item">
    <!-- Create a container around the "Hello, Admin" statement -->
    <div class="hello-container">
      <?php echo "Hello, " . $_SESSION["username"] . " (" . ucfirst($_SESSION["role"]) . ")"; ?>
    </div>
  </li>
</ul>
 
    <nav class="navbar navbar-expand-lg navbar-dark">
      <div class="container-fluid">

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">

            <!-- Pupil Management -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Pupil Management</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="pupil_form.php">Add New Pupil</a></li>
                <li><a class="dropdown-item" href="update_delete_pupil.php">Update/Delete Pupil Information</a></li>
                <li><a class="dropdown-item" href="view_pupils.php">View All Pupils</a></li>
              </ul>
            </li>

            <!-- Teacher Management -->
            <?php if ($_SESSION["role"] === 'admin'): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Teacher Management</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="teachers_form.php">Add New Teacher</a></li>
                <li><a class="dropdown-item" href="update_delete_teacher.php">Update/Delete Teacher Information</a></li>
                <li><a class="dropdown-item" href="view_teachers.php">View Teacher Details</a></li>
              </ul>
            </li>
            <?php endif; ?>

            <!-- Class Management -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Class Management</a>
              <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="add_class.php">Add Classes </a></li>
                <li><a class="dropdown-item" href="assign_class.php">Add/Assign Classes</a></li>
                <li><a class="dropdown-item" href="view_classes.php">View Classes</a></li>
              </ul>
            </li>

            <!-- Attendance Tracking -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Attendance Tracking</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="attendance.php">Mark Attendance</a></li>
                <li><a class="dropdown-item" href="view_attendance.php">View Attendance History</a></li>
              </ul>
            </li>

            <!-- Support Staff Management -->
            <?php if ($_SESSION["role"] === 'admin' || $_SESSION["role"] === 'clerk'): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Support Staff</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="support.php">Add Support Staff</a></li>
                <li><a class="dropdown-item" href="update_delete_support.php">Update/Delete Support Staff</a></li>
                <li><a class="dropdown-item" href="view_support.php">View Support Staff</a></li>
              </ul>
            </li>
            <?php endif; ?>

            <!-- Guardian Management -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Guardian Management</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="guardians.php">Add Guardians</a></li>
                <li><a class="dropdown-item" href="update_delete_guardian.php">Update/Delete Guardians</a></li>
                <li><a class="dropdown-item" href="view_guardian.php">View Guardians</a></li>
              </ul>
            </li>

            <!-- Reports -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Reports</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="add_update_grades.php">Add/Update Grades</a></li>
                <li><a class="dropdown-item" href="view_grades.php">View Grades</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

