<?php
// /api/assignments/submit-assignment.php

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
        json_response(['success' => false, 'error' => 'Only students can submit assignments.'], 403);
    }
    
    $student_id = $user['id'];
    $assignment_id = filter_input(INPUT_POST, 'assignment_id', FILTER_SANITIZE_NUMBER_INT);
    
    if (empty($assignment_id) || !isset($_FILES['submission_file']) || $_FILES['submission_file']['error'] != 0) {
        json_response(['success' => false, 'error' => 'Invalid submission. Please select a file.'], 400);
    }

    // --- Placeholder Logic ---
    // 1. Validate file (size, type)
    // 2. Check if due date has passed
    // 3. Move file to `public/uploads/submissions/`
    // $file = $_FILES['submission_file'];
    // $file_name = $student_id . '_' . $assignment_id . '_' . basename($file['name']);
    // $db_path = 'public/uploads/submissions/' . $file_name;
    // $upload_path = '../../../' . $db_path;
    // move_uploaded_file($file['tmp_name'], $upload_path);
    //
    // 4. Insert into `Submissions` table
    // $stmt = $pdo->prepare("INSERT INTO Submissions (assignment_id, student_id, submission_date, file_path, status) VALUES (?, ?, NOW(), ?, 'submitted')");
    // $success = $stmt->execute([$assignment_id, $student_id, $db_path]);
    $success = true; // Simulate success
    // --- End Placeholder ---

    if ($success) {
        json_response(['success' => true, 'message' => 'Assignment submitted successfully.']);
    } else {
        json_response(['success' => false, 'error' => 'Failed to submit assignment.'], 500);
    }

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>