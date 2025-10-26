<?php
// /dashboard/admin/users/change-role.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);
$admin_user = get_session_user(); // Logged in admin

// Get data from POST (assuming a form submission, though GET might also be used with care)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    $new_role = sanitize_input($_POST['new_role'] ?? '');
} else {
    // Allow GET for simple links, but POST is generally safer for state changes
    $user_id = filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    $new_role = sanitize_input($_GET['new_role'] ?? '');
}

$allowed_roles = ['student', 'teacher', 'admin'];

// --- Validation and Logic ---
if (empty($user_id) || empty($new_role) || !in_array($new_role, $allowed_roles)) {
    $_SESSION['error_message'] = "Invalid user ID or role provided.";
    redirect('dashboard/admin/users/list-users.php');
}

// Prevent admin from changing their own role via this script
if ($user_id == $admin_user['id']) {
    $_SESSION['error_message'] = "Error: You cannot change your own role using this action.";
    redirect('dashboard/admin/users/list-users.php');
}

// --- ACTUAL DATABASE UPDATE LOGIC ---
try {
    $sql = "UPDATE Users SET role = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$new_role, $user_id]);

    if ($success && $stmt->rowCount() > 0) {
        $_SESSION['success_message'] = "User's role (ID: $user_id) updated to '$new_role'.";
        // Log this action
        // Logger::info("Admin (ID: {$admin_user['id']}) changed role of user (ID: $user_id) to $new_role");
    } elseif ($success && $stmt->rowCount() == 0) {
        $_SESSION['error_message'] = "User (ID: $user_id) not found or role was already '$new_role'. No changes made.";
    } else {
         $_SESSION['error_message'] = "Failed to update user role. An error occurred.";
    }

} catch (PDOException $e) {
    log_error("Error changing role for user ID $user_id: " . $e->getMessage(), __FILE__, __LINE__);
    $_SESSION['error_message'] = "A database error occurred while changing the user's role.";
}
// --- END ACTUAL DATABASE LOGIC ---

// Redirect back to the user list
redirect('dashboard/admin/users/list-users.php');
?>