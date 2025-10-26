<?php
// /api/messages/get-messages.php

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

try {
    $user = get_session_user();
    
    // Optional: 'sent' or 'inbox'
    $box = sanitize_input($_GET['box'] ?? 'inbox');
    
    // --- Placeholder Logic ---
    // if ($box === 'sent') {
    //     $sql = "SELECT m.*, CONCAT(u.first_name, ' ', u.last_name) AS receiver_name 
    //             FROM Messages m JOIN Users u ON m.receiver_id = u.id 
    //             WHERE m.sender_id = ? ORDER BY m.sent_date DESC";
    // } else {
    //     $sql = "SELECT m.*, CONCAT(u.first_name, ' ', u.last_name) AS sender_name 
    //             FROM Messages m JOIN Users u ON m.sender_id = u.id 
    //             WHERE m.receiver_id = ? ORDER BY m.sent_date DESC";
    // }
    // $stmt = $pdo->prepare($sql);
    // $stmt->execute([$user['id']]);
    // $messages = $stmt->fetchAll();
    
    $messages = [
        ['id' => 1, 'subject' => 'RE: Question', 'sender_name' => 'Dr. Alan Smith', 'sent_date' => '2025-10-23 08:00:00', 'read_status' => 0]
    ];
    // --- End Placeholder ---

    json_response(['success' => true, 'messages' => $messages]);

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>