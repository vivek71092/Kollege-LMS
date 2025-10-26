<?php
// /dashboard/index.php

// Load core files (adjust path as necessary)
require_once __DIR__ . '/../config.php'; // Use __DIR__ for reliability
require_once __DIR__ . '/../functions.php';

// Check if the user is logged in. This MUST run after config (session start) and functions.
// Adjust path for check_auth.php relative to this file
require_once __DIR__ . '/../auth/check_auth.php';

// Get the user's role from the session (set during login)
$role = $_SESSION['role'] ?? 'guest'; // Default to guest if not set

// --- Redirect based on role ---
switch ($role) {
    case 'admin':
        // Redirects to BASE_URL + 'dashboard/admin/dashboard.php'
        redirect('dashboard/admin/dashboard.php');
        break; // exit is handled by redirect() function
    case 'teacher':
        // Redirects to BASE_URL + 'dashboard/teacher/dashboard.php'
        redirect('dashboard/teacher/dashboard.php');
        break;
    case 'student':
        // Redirects to BASE_URL + 'dashboard/student/dashboard.php'
        redirect('dashboard/student/dashboard.php');
        break;
    default:
        // If role is unknown or 'guest', something went wrong or session expired.
        // Log them out and send back to login.
        $_SESSION['error_message'] = "Invalid session or role. Please log in again.";
        redirect('auth/logout.php'); // Redirect to logout script
        break;
}

// No HTML output should ever be reached in this file.
// The redirect() function includes an exit().
?>