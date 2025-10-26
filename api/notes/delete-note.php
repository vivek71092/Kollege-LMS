<?php
// /api/notes/delete-note.php

header('Content-Type: application/json');
require_once '../../../config.php';
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensures user is logged in

// --- JSON Response Helper ---
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit;
}
// --- End Helper ---

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Invalid request method.'], 405);
}

try {
    $user = get_session_user();
    require_role(['teacher', 'admin']);
    
    $data = json_decode(file_get_contents('php://input'), true);
    $note_id = filter_var($data['note_id'], FILTER_SANITIZE_NUMBER_INT);

    if (empty($note_id)) {
        json_response(['success' => false, 'error' => 'Note ID is required.'], 400);
    }

    // --- Placeholder Logic ---
    // 1. Get file path
    // $sql = "SELECT file_path FROM Notes WHERE id = ?";
    // if ($user['role'] === 'teacher') $sql .= " AND uploaded_by = " . $user['id'];
    // $stmt = $pdo->prepare($sql);
    // $stmt->execute([$note_id]);
    // $file_path = $stmt->fetchColumn();
    $file_path = 'public/uploads/notes/mock.pdf'; // Simulate path
    
    // if ($file_path) {
    //    // 2. Delete file from server
    //    // if (file_exists('../../../' . $file_path)) {
    //    //     unlink('../../../' . $file_path);
    //    // }
    //    // 3. Delete from database
    //    // $delete_stmt = $pdo->prepare("DELETE FROM Notes WHERE id = ?");
    //    // $delete_stmt->execute([$note_id]);
    //    $success = true;
    // } else {
    //    $success = false;
    // }
    $success = true; // Simulate success
    // --- End Placeholder ---

    if ($success) {
        json_response(['success' => true, 'message' => 'Note deleted successfully.']);
    } else {
        json_response(['success' => false, 'error' => 'Failed to delete note or unauthorized.'], 500);
    }

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>