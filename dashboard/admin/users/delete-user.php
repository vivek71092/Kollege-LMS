<?php
// /dashboard/admin/users/delete-user.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);
$admin_user = get_session_user(); // Logged in admin

// **Get user ID to delete from GET parameter (from the modal link)**
$user_id_to_delete = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// --- Deletion Logic ---
if (!$user_id_to_delete) {
    $_SESSION['error_message'] = "Invalid user ID provided for deletion.";
    redirect('dashboard/admin/users/list-users.php');
}

// Prevent admin from deleting themselves
if ($user_id_to_delete == $admin_user['id']) {
    $_SESSION['error_message'] = "Error: You cannot delete your own account.";
    redirect('dashboard/admin/users/list-users.php');
}

// --- ACTUAL DATABASE DELETE LOGIC ---
try {
    // Check if user exists before attempting delete
    $stmt_check = $pdo->prepare("SELECT id FROM Users WHERE id = ?");
    $stmt_check->execute([$user_id_to_delete]);
    if (!$stmt_check->fetch()) {
        $_SESSION['error_message'] = "User (ID: $user_id_to_delete) not found.";
        redirect('dashboard/admin/users/list-users.php');
    }

    // Attempt deletion
    $sql = "DELETE FROM Users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$user_id_to_delete]);

    if ($success && $stmt->rowCount() > 0) {
        $_SESSION['success_message'] = "User (ID: $user_id_to_delete) deleted successfully.";
        // Optional: Log this action
        // Logger::info("Admin (ID: {$admin_user['id']}) deleted user (ID: $user_id_to_delete)");
    } else {
        // This case might happen if the user was deleted between check and delete (unlikely)
        // or if execute() returned false for another reason.
        $_SESSION['error_message'] = "Failed to delete user (ID: $user_id_to_delete). An error occurred or user not found.";
    }

} catch (PDOException $e) {
    log_error("Error deleting user ID $user_id_to_delete: " . $e->getMessage(), __FILE__, __LINE__);
    // Check for specific foreign key constraint violation (MySQL code '23000')
    if ($e->getCode() == '23000') {
         $_SESSION['error_message'] = "Cannot delete user (ID: $user_id_to_delete). They may be assigned to courses or have other dependent records. Please reassign or remove dependencies first.";
    } else {
        $_SESSION['error_message'] = "A database error occurred while deleting the user.";
    }
}
// --- END ACTUAL DATABASE LOGIC ---

// Redirect back to the user list regardless of outcome (message will show status)
redirect('dashboard/admin/users/list-users.php');
?>