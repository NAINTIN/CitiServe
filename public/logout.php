<?php
// Start the session so we can destroy it
session_start();

// Remove all session variables
session_unset();

// Destroy the session completely
session_destroy();

// Redirect the user to the login page
header('Location: /CitiServe/public/login.php');
exit;