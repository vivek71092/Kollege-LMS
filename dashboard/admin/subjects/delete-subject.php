<?php
// /dashboard/admin/subjects/delete-subject.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);
$admin_user = get_session_user();

// Get subject ID from URL (from the modal confirm link)
$subject_id_to_delete = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// --- Deletion Logic ---
if (!$subject_id_to_delete) {
    $_SESSION['error_message'] = "Invalid subject ID provided for deletion.";
    redirect('dashboard/admin/subjects/list-subjects.php');
}

// --- ACTUAL DATABASE DELETE LOGIC ---
try {
    // WARNING: Assumes ON DELETE CASCADE is set correctly on all FKs referencing Subjects.id.
    // If not, manual deletion of dependent records is required first.

    // Check if subject exists before attempting delete
    $stmt_check = $pdo->prepare("SELECT id FROM Subjects WHERE id = ?");
    $stmt_check->execute([$subject_id_to_delete]);
    if (!$stmt_check->fetch()) {
        $_SESSION['error_message'] = "Subject (ID: $subject_id_to_delete) not found.";
        redirect('dashboard/admin/subjects/list-subjects.php');
    }

    // Attempt deletion
    $sql = "DELETE FROM Subjects WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$subject_id_to_delete]);

    if ($success && $stmt->rowCount() > 0) {
        $_SESSION['success_message'] = "Subject (ID: $subject_id_to_delete) and potentially related data deleted successfully.";
        // Optional: Log action
        // Logger::info("Admin (ID: {$admin_user['id']}) deleted subject (ID: $subject_id_to_delete)");
    } else {
        $_SESSION['error_message'] = "Failed to delete subject (ID: $subject_id_to_delete). An error occurred or subject not found.";
    }

} catch (PDOException $e) {
    log_error("Error deleting subject ID $subject_id_to_delete: " . $e->getMessage(), __FILE__, __LINE__);
    // Catch potential foreign key constraint violations if CASCADE wasn't set up properly
    if ($e->getCode() == '23000') {
         $_SESSION['error_message'] = "Cannot delete subject (ID: $subject_id_to_delete) due to existing dependent records (e.g., enrollments, assignments). Ensure database constraints allow cascading deletes or remove dependencies manually.";
    } else {
        $_SESSION['error_message'] = "A database error occurred while deleting the subject.";
    }
}
// --- END ACTUAL DATABASE LOGIC ---

// Redirect back to the subject list
redirect('dashboard/admin/subjects/list-subjects.php');
?>