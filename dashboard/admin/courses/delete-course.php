<?php
// /dashboard/admin/courses/delete-course.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);
$admin_user = get_session_user();

// Get course ID from URL (from the modal confirm link)
$course_id_to_delete = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// --- Deletion Logic ---
if (!$course_id_to_delete) {
    $_SESSION['error_message'] = "Invalid course ID provided for deletion.";
    redirect('dashboard/admin/courses/list-courses.php');
}

// --- ACTUAL DATABASE DELETE LOGIC ---
try {
    // WARNING: This assumes ON DELETE CASCADE is set for Subjects.course_id
    // If not, you must manually delete Subjects, Notes, Assignments etc. first.
    // Check if course exists before attempting delete
    $stmt_check = $pdo->prepare("SELECT id FROM Courses WHERE id = ?");
    $stmt_check->execute([$course_id_to_delete]);
    if (!$stmt_check->fetch()) {
        $_SESSION['error_message'] = "Course (ID: $course_id_to_delete) not found.";
        redirect('dashboard/admin/courses/list-courses.php');
    }

    // Attempt deletion
    $sql = "DELETE FROM Courses WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$course_id_to_delete]);

    if ($success && $stmt->rowCount() > 0) {
        $_SESSION['success_message'] = "Course (ID: $course_id_to_delete) and all associated subjects/data deleted successfully.";
        // Optional: Log action
        // Logger::info("Admin (ID: {$admin_user['id']}) deleted course (ID: $course_id_to_delete)");
    } else {
        // This might happen if deleted between check and delete, or execute returned false
        $_SESSION['error_message'] = "Failed to delete course (ID: $course_id_to_delete). An error occurred or course was already deleted.";
    }

} catch (PDOException $e) {
    log_error("Error deleting course ID $course_id_to_delete: " . $e->getMessage(), __FILE__, __LINE__);
    // Catch potential foreign key constraint violations if CASCADE wasn't set up properly
    if ($e->getCode() == '23000') {
         $_SESSION['error_message'] = "Cannot delete course (ID: $course_id_to_delete). Ensure all dependent records (like subjects without CASCADE delete) are removed first.";
    } else {
        $_SESSION['error_message'] = "A database error occurred while deleting the course.";
    }
}
// --- END ACTUAL DATABASE LOGIC ---

// Redirect back to the course list
redirect('dashboard/admin/courses/list-courses.php');
?>