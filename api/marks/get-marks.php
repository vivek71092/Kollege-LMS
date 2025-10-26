<?php
// /api/marks/get-marks.php

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
    $subject_id = filter_input(INPUT_GET, 'subject_id', FILTER_SANITIZE_NUMBER_INT);
    $student_id_param = filter_input(INPUT_GET, 'student_id', FILTER_SANITIZE_NUMBER_INT);
    
    $marks = [];
    
    // --- Placeholder Logic ---
    if ($user['role'] === 'student') {
        // A student can only get their own marks
        // $sql = "SELECT * FROM Marks WHERE student_id = ?";
        // $params = [$user['id']];
        // if ($subject_id) { $sql .= " AND subject_id = ?"; $params[] = $subject_id; }
        // $stmt = $pdo->prepare($sql);
        // $stmt->execute($params);
        // $marks = $stmt->fetchAll();
        $marks = [['subject_id' => 1, 'total_marks' => 85, 'grade' => 'A-']];
        
    } elseif ($user['role'] === 'teacher' || $user['role'] === 'admin') {
        // Teacher/Admin can get marks for a subject or a specific student
        if (empty($subject_id) && empty($student_id_param)) {
             json_response(['success' => false, 'error' => 'Subject ID or Student ID is required.'], 400);
        }
        // $sql = "SELECT * FROM Marks WHERE 1=1";
        // if ($subject_id) { $sql .= " AND subject_id = $subject_id"; }
        // if ($student_id_param) { $sql .= " AND student_id = $student_id_param"; }
        // $marks = $pdo->query($sql)->fetchAll();
        $marks = [
            ['subject_id' => $subject_id, 'student_id' => 10, 'total_marks' => 85, 'grade' => 'A-'],
            ['subject_id' => $subject_id, 'student_id' => 11, 'total_marks' => 72, 'grade' => 'B']
        ];
    }
    // --- End Placeholder ---

    json_response(['success' => true, 'marks' => $marks]);

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>