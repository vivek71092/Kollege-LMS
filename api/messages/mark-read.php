<?php
// /api/messages/mark-read.php

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
    
    $data = json_decode(file_get_contents('php://input'), true);
    $message_id = filter_var($data['message_id'], FILTER_SANITIZE_NUMBER_INT);

    if (empty($message_id)) {
        json_response(['success' => false, 'error' => 'Message ID is required.'], 400);
    }

    // --- Placeholder Logic ---
    // $stmt = $pdo->prepare("UPDATE Messages SET read_status = 1 WHERE id = ? AND receiver_id = ?");
    // $success = $stmt->execute([$message_id, $user['id']]);
    $success = true;
    // --- End Placeholder ---

    if ($success) {
        json_response(['success' => true, 'message' => 'Message marked as read.']);
    } else {
        json_response(['success' => false, 'error' => 'Failed to mark as read or unauthorized.'], 500);
    }

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>