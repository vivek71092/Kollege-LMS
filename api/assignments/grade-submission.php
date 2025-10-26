<?php
// /api/assignments/grade-submission.php

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
    require_role(['teacher', 'admin']); // Only teachers/admins can grade
    
    $data = json_decode(file_get_contents('php://input'), true);
    $submission_id = filter_var($data['submission_id'], FILTER_SANITIZE_NUMBER_INT);
    $marks = filter_var($data['marks'], FILTER_SANITIZE_NUMBER_INT);
    $feedback = sanitize_input($data['feedback'] ?? '');

    if (empty($submission_id) || $marks === false) {
        json_response(['success' => false, 'error' => 'Submission ID and marks are required.'], 400);
    }

    // --- Placeholder Logic ---
    // 1. Verify teacher owns the course for this submission
    // 2. Update the Submissions table
    // $stmt = $pdo->prepare("UPDATE Submissions SET marks_obtained = ?, feedback = ?, status = 'graded', graded_date = NOW(), graded_by = ? WHERE id = ?");
    // $success = $stmt->execute([$marks, $feedback, $user['id'], $submission_id]);
    $success = true;
    
    // 3. (Complex) Update/Aggregate marks in the main `Marks` table
    
    // --- End Placeholder ---

    if ($success) {
        json_response(['success' => true, 'message' => 'Submission graded successfully.']);
    } else {
        json_response(['success' => false, 'error' => 'Failed to grade submission.'], 500);
    }

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>