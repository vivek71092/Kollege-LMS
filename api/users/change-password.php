<?php
// /api/users/change-password.php

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

    $current_password = $data['current_password'] ?? '';
    $new_password = $data['new_password'] ?? '';

    if (strlen($new_password) < 8) {
        json_response(['success' => false, 'error' => 'Password must be at least 8 characters.'], 400);
    }

    // --- Placeholder Logic ---
    // $stmt = $pdo->prepare("SELECT password FROM Users WHERE id = ?");
    // $stmt->execute([$user['id']]);
    // $hash = $stmt->fetchColumn();
    $hash = password_hash('old_password_placeholder', PASSWORD_BCRYPT); // Simulate hash

    // Simulate checking the old password
    // if ($hash && password_verify($current_password, $hash)) {
    if ($current_password === 'old_password_placeholder') { // Simulate success
        // $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
        // $update_stmt = $pdo->prepare("UPDATE Users SET password = ? WHERE id = ?");
        // $update_stmt->execute([$new_hash, $user['id']]);
        json_response(['success' => true, 'message' => 'Password changed successfully.']);
    } else {
        json_response(['success' => false, 'error' => 'Incorrect current password.'], 401);
    }
    // --- End Placeholder ---

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>