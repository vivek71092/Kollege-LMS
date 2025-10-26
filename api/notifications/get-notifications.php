<?php
// /api/notifications/get-notifications.php

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
    
    // Optional: 'all' or 'unread'
    $filter = sanitize_input($_GET['filter'] ?? 'unread');
    
    // --- Placeholder Logic ---
    // $sql = "SELECT * FROM Notifications WHERE user_id = ?";
    // if ($filter === 'unread') {
    //     $sql .= " AND read_status = 0";
    // }
    // $sql .= " ORDER BY created_at DESC LIMIT 10";
    // $stmt = $pdo->prepare($sql);
    // $stmt->execute([$user['id']]);
    // $notifications = $stmt->fetchAll();
    
    $notifications = [
        ['id' => 1, 'title' => 'New Grade', 'message' => 'Your assignment "HTML/CSS Homepage" has been graded.', 'read_status' => 0, 'created_at' => '2025-10-23 08:10:00'],
        ['id' => 2, 'title' => 'New Note', 'message' => 'A new note "Lecture 3: JavaScript" was added to Web Development.', 'read_status' => 1, 'created_at' => '2025-10-22 10:00:00']
    ];
    // --- End Placeholder ---

    json_response(['success' => true, 'notifications' => $notifications]);

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>