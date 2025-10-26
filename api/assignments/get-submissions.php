<?php
// /api/assignments/get-submissions.php

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
    $assignment_id = filter_input(INPUT_GET, 'assignment_id', FILTER_SANITIZE_NUMBER_INT);
    
    if (empty($assignment_id)) {
        json_response(['success' => false, 'error' => 'Assignment ID is required.'], 400);
    }

    $submissions = [];

    // --- Placeholder Logic ---
    if ($user['role'] === 'student') {
        // $stmt = $pdo->prepare("SELECT * FROM Submissions WHERE assignment_id = ? AND student_id = ?");
        // $stmt->execute([$assignment_id, $user['id']]);
        // $submissions = $stmt->fetchAll();
        $submissions = [
            ['id' => 1, 'assignment_id' => $assignment_id, 'student_id' => $user['id'], 'status' => 'graded', 'marks_obtained' => 45]
        ];
    } elseif ($user['role'] === 'teacher' || $user['role'] === 'admin') {
        // $stmt = $pdo->prepare("SELECT * FROM Submissions WHERE assignment_id = ?");
        // $stmt->execute([$assignment_id]);
        // $submissions = $stmt->fetchAll();
        $submissions = [
            ['id' => 1, 'assignment_id' => $assignment_id, 'student_id' => 10, 'status' => 'graded', 'marks_obtained' => 45],
            ['id' => 2, 'assignment_id' => $assignment_id, 'student_id' => 11, 'status' => 'submitted', 'marks_obtained' => null]
        ];
    }
    // --- End Placeholder ---

    json_response(['success' => true, 'submissions' => $submissions]);

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>