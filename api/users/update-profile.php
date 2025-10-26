<?php
// /api/users/update-profile.php

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

    // Sanitize
    $first_name = sanitize_input($data['first_name'] ?? '');
    $last_name = sanitize_input($data['last_name'] ?? '');
    $phone = sanitize_input($data['phone'] ?? '');
    $bio = sanitize_input($data['bio'] ?? '');

    if (empty($first_name) || empty($last_name)) {
        json_response(['success' => false, 'error' => 'First and last name are required.'], 400);
    }

    // --- Placeholder Logic ---
    // Note: Add logic for file upload (profile_image) separately
    // $stmt = $pdo->prepare("UPDATE Users SET first_name = ?, last_name = ?, phone = ?, bio = ? WHERE id = ?");
    // $success = $stmt->execute([$first_name, $last_name, $phone, $bio, $user['id']]);
    $success = true; // Simulate success
    // --- End Placeholder ---

    if ($success) {
        // Update session
        $_SESSION['first_name'] = $first_name;
        json_response(['success' => true, 'message' => 'Profile updated successfully.']);
    } else {
        json_response(['success' => false, 'error' => 'Failed to update profile.'], 500);
    }

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>