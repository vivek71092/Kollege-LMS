<?php
// /dashboard/admin/announcements/delete-announcement.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);
$admin_user = get_session_user();

// Get announcement ID from URL (from the modal confirm link)
$announcement_id_to_delete = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// --- Deletion Logic ---
if (!$announcement_id_to_delete) {
    $_SESSION['error_message'] = "Invalid announcement ID provided for deletion.";
    redirect('dashboard/admin/announcements/list-announcements.php');
}

// --- ACTUAL DATABASE DELETE LOGIC ---
try {
    // Optional: Get image path to delete file from server
    // $stmt_img = $pdo->prepare("SELECT image FROM Announcements WHERE id = ?");
    // $stmt_img->execute([$announcement_id_to_delete]);
    // $image_path = $stmt_img->fetchColumn();

    // Attempt deletion from database
    $sql = "DELETE FROM Announcements WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$announcement_id_to_delete]);

    if ($success && $stmt->rowCount() > 0) {
        $_SESSION['success_message'] = "Announcement (ID: $announcement_id_to_delete) deleted successfully.";
        // Optional: Delete image file
        // if ($image_path) {
        //     require_once '../../../classes/FileHandler.php';
        //     $fileHandler = new FileHandler(); // Assumes default base path
        //     $fileHandler->delete($image_path);
        // }
        // Optional: Log action
        // Logger::info("Admin (ID: {$admin_user['id']}) deleted announcement (ID: $announcement_id_to_delete)");
    } elseif ($success && $stmt->rowCount() == 0) {
        $_SESSION['error_message'] = "Announcement (ID: $announcement_id_to_delete) not found or already deleted.";
    } else {
        $_SESSION['error_message'] = "Failed to delete announcement (ID: $announcement_id_to_delete). An error occurred.";
    }

} catch (PDOException $e) {
    log_error("Error deleting announcement ID $announcement_id_to_delete: " . $e->getMessage(), __FILE__, __LINE__);
    $_SESSION['error_message'] = "A database error occurred while deleting the announcement.";
}
// --- END ACTUAL DATABASE LOGIC ---

// Redirect back to the announcement list
redirect('dashboard/admin/announcements/list-announcements.php');
?>