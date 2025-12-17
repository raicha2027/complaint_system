<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy remember me cookie
if (isset($_COOKIE['user_email'])) {
    setcookie('user_email', '', time() - 3600, '/');
}

// Destroy session
session_destroy();

// Redirect to homepage
header('Location: ../index.php?success=Logged out successfully');
exit();
