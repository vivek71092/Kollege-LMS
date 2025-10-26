<?php
// /auth/check_auth.php

// This file is intended to be included at the *very top* of all
// pages within the /dashboard/ directory.
// It assumes config.php (which starts the session) has already been included.

if (!isset($_SESSION)) {
    // Fail safe in case config.php wasn't loaded first
    session_start();
}

// Use the function from functions.php
if (!is_logged_in()) {
    // Store the intended page to redirect back after login (optional)
    // $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    
    $_SESSION['error_message'] = "You must be logged in to access that page.";
    
    // Use the redirect function from functions.php
    // We need to ensure functions.php is loaded *before* this file.
    // A better design is to have the calling script (e.g., /dashboard/student/dashboard.php)
    // include config.php, functions.php, and *then* this file.
    
    // For safety, we'll define BASE_URL and redirect here if not set.
    if (!defined('BASE_URL')) {
        // A basic fallback if config.php wasn't included
        $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $base_url = rtrim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $base_url), '/');
        define('BASE_URL', $base_url . '/'); 
    }
    
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

// At this point, the user is confirmed to be logged in.
// Individual pages can now perform more specific role checks, e.g.:
// require_role(['admin', 'teacher']);
?>