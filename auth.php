<?php
session_start();

function require_login() {
  if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
  }
}

function require_role($allowed_roles = []) {
  require_login();
  if (!in_array($_SESSION["role"], $allowed_roles)) {
    echo "<h2>Access Denied</h2><p>You do not have permission to view this page.</p>";
    exit;
  }
}
?>
