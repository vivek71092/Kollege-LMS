<?php
// /api/messages/send-message.php

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
    $sender_id = $user['id'];
    
    $data = json_decode(file_get_contents('php://input'), true);
    $receiver_id = filter_var($data['receiver_id'], FILTER_SANITIZE_NUMBER_INT);
    $subject = sanitize_input($data['subject']);
    $message = sanitize_input($data['message']);

    if (empty($receiver_id) || empty($subject) || empty($message)) {
        json_response(['success' => false, 'error' => 'Receiver, subject, and message are required.'], 400);
    }

    // --- Placeholder Logic ---
    // 1. Verify receiver_id exists
    // $stmt = $pdo->prepare("INSERT INTO Messages (sender_id, receiver_id, subject, message, sent_date, read_status) VALUES (?, ?, ?, ?, NOW(), 0)");
    // $success = $stmt->execute([$sender_id, $receiver_id, $subject, $message]);
    $success = true; // Simulate success
    // --- End Placeholder ---

    if ($success) {
        json_response(['success' => true, 'message' => 'Message sent successfully.']);
    } else {
        json_response(['success' => false, 'error' => 'Failed to send message.'], 500);
    }

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>