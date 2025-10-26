<?php
// /api/marks/add-marks.php

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
    require_role(['teacher', 'admin']); // Only teachers/admins can add marks
    
    $data = json_decode(file_get_contents('php://input'), true);
    $student_id = filter_var($data['student_id'], FILTER_SANITIZE_NUMBER_INT);
    $subject_id = filter_var($data['subject_id'], FILTER_SANITIZE_NUMBER_INT);
    
    // Get marks (allow them to be null)
    $assignment_marks = $data['assignment_marks'] ? filter_var($data['assignment_marks'], FILTER_SANITIZE_NUMBER_INT) : null;
    $midterm_marks = $data['midterm_marks'] ? filter_var($data['midterm_marks'], FILTER_SANITIZE_NUMBER_INT) : null;
    $final_marks = $data['final_marks'] ? filter_var($data['final_marks'], FILTER_SANITIZE_NUMBER_INT) : null;

    if (empty($student_id) || empty($subject_id)) {
        json_response(['success' => false, 'error' => 'Student ID and Subject ID are required.'], 400);
    }
    
    // --- Placeholder Logic ---
    // 1. Calculate total marks and grade
    // $total_marks = ($assignment_marks ?? 0) + ($midterm_marks ?? 0) + ($final_marks ?? 0);
    // $grade = 'B+'; // Placeholder: Add a function to calculate grade
    
    // 2. Use INSERT ... ON DUPLICATE KEY UPDATE
    // $stmt = $pdo->prepare(
    //     "INSERT INTO Marks (student_id, subject_id, assignment_marks, midterm_marks, final_marks, total_marks, grade, teacher_id, created_at)
    //      VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    //      ON DUPLICATE KEY UPDATE 
    //      assignment_marks = VALUES(assignment_marks), 
    //      midterm_marks = VALUES(midterm_marks), 
    //      final_marks = VALUES(final_marks), 
    //      total_marks = VALUES(total_marks), 
    //      grade = VALUES(grade),
    //      teacher_id = VALUES(teacher_id)"
    // );
    // $success = $stmt->execute([$student_id, $subject_id, $assignment_marks, $midterm_marks, $final_marks, $total_marks, $grade, $user['id']]);
    $success = true;
    // --- End Placeholder ---

    if ($success) {
        json_response(['success' => true, 'message' => 'Marks updated successfully.']);
    } else {
        json_response(['success' => false, 'error' => 'Failed to update marks.'], 500);
    }

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>