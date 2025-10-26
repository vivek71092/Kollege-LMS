<?php
// /api/notifications/mark-notification-read.php

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
    $notification_id = $data['notification_id'] ?? null; // Can be null or 'all'

    // --- Placeholder Logic ---
    // if ($notification_id === 'all') {
    //     $stmt = $pdo->prepare("UPDATE Notifications SET read_status = 1 WHERE user_id = ?");
    //     $success = $stmt->execute([$user['id']]);
    // } elseif (!empty($notification_id)) {
    //     $stmt = $pdo->prepare("UPDATE Notifications SET read_status = 1 WHERE id = ? AND user_id = ?");
    //     $success = $stmt->execute([$notification_id, $user['id']]);
    // } else {
    //     json_response(['success' => false, 'error' => 'Notification ID is required.'], 400);
    // }
    $success = true;
    // --- End Placeholder ---

    if ($success) {
        json_response(['success' => true, 'message' => 'Notification(s) marked as read.']);
    } else {
        json_response(['success' => false, 'error' => 'Failed to mark as read.'], 500);
    }

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>