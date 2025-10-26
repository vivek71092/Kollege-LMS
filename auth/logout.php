<?php
// /auth/logout.php

// Use __DIR__ for reliability
require_once __DIR__ . '/../config.php'; // Need config to start session if needed
require_once __DIR__ . '/../functions.php'; // Need redirect function

// Ensure session is started before trying to destroy it
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id_logged_out = $_SESSION['user_id'] ?? 'Unknown';

// 1. Unset all session variables specific to your application
// (More specific than $_SESSION = [] which might affect other apps on same domain if any)
unset($_SESSION['user_id']);
unset($_SESSION['role']);
unset($_SESSION['email']);
unset($_SESSION['first_name']);
// Add any other session variables your app uses

// 2. Destroy the session data on the server
session_destroy();

// 3. Delete the session cookie from the browser
// Recommended practice to ensure client side is cleared too
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, // Set expiry in the past
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Optional: Log the logout action
// Logger::info("User logout: ID=$user_id_logged_out");

// Add a success message (optional)
// $_SESSION['success_message'] = "You have been logged out successfully."; // Needs session_start() again if you want this

// Redirect to the login page using the reliable redirect function
redirect('auth/login.php');
exit; // exit() is already in redirect(), but good practice here too
?>