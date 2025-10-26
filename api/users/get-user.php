<?php
// /api/users/get-user.php

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
    $user_id_to_fetch = $user['id']; // Default to self

    // Allow admin to fetch a specific user
    if ($user['role'] === 'admin' && isset($_GET['id'])) {
        $user_id_to_fetch = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    }

    // --- Placeholder Logic ---
    // $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, phone, role, status, profile_image, bio FROM Users WHERE id = ?");
    // $stmt->execute([$user_id_to_fetch]);
    // $user_data = $stmt->fetch();
    
    // Simulating a fetch
    $user_data = [
        'id' => $user_id_to_fetch,
        'first_name' => 'Mock',
        'last_name' => $user['role'] === 'admin' ? 'Admin' : 'User',
        'email' => 'mock@example.com',
        'phone' => '123-456-7890',
        'role' => $user['role'],
        'status' => 'active',
        'profile_image' => 'public/images/placeholders/default-profile.png',
        'bio' => 'This is a mock bio.'
    ];
    // --- End Placeholder ---
    
    if ($user_data) {
        // Unset sensitive data before sending
        unset($user_data['password']);
        json_response(['success' => true, 'user' => $user_data]);
    } else {
        json_response(['success' => false, 'error' => 'User not found.'], 404);
    }

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>