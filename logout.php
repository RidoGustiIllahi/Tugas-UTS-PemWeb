<?php
session_start();

// Unset all session variables related to user login
unset($_SESSION['role']);
unset($_SESSION['status']);
unset($_SESSION['username']);

// Destroy the session to clear all session data
session_destroy();

// Redirect to index.html after logging out
header("Location: index.html");
exit();
?>
