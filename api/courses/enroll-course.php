<?php
// /api/courses/enroll-course.php

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
    if ($user['role'] !== 'student') {
        json_response(['success' => false, 'error' => 'Only students can enroll.'], 403);
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $subject_id = filter_var($data['subject_id'], FILTER_SANITIZE_NUMBER_INT);

    if (empty($subject_id)) {
        json_response(['success' => false, 'error' => 'Invalid subject ID.'], 400);
    }

    // --- Placeholder Logic ---
    // 1. Check if already enrolled
    // $stmt = $pdo->prepare("SELECT id FROM Enrollments WHERE student_id = ? AND subject_id = ?");
    // $stmt->execute([$user['id'], $subject_id]);
    // if ($stmt->fetch()) {
    //    json_response(['success' => false, 'error' => 'Already enrolled.'], 409);
    // }
    
    // 2. Insert new enrollment
    // $insert_stmt = $pdo->prepare("INSERT INTO Enrollments (student_id, subject_id, enrollment_date, status) VALUES (?, ?, NOW(), 'enrolled')");
    // $success = $insert_stmt->execute([$user['id'], $subject_id]);
    $success = true; // Simulate success
    // --- End Placeholder ---

    if ($success) {
        json_response(['success' => true, 'message' => 'Successfully enrolled in subject.']);
    } else {
        json_response(['success' => false, 'error' => 'Failed to enroll.'], 500);
    }

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>