<?php
// /api/attendance/get-attendance.php

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
    
    if (empty($subject_id)) {
        json_response(['success' => false, 'error' => 'Subject ID is required.'], 400);
    }
    
    $attendance = [];
    
    // --- Placeholder Logic ---
    // Add logic to restrict by user
    if ($user['role'] === 'student') {
        // $stmt = $pdo->prepare("SELECT * FROM Attendance WHERE subject_id = ? AND student_id = ?");
        // $stmt->execute([$subject_id, $user['id']]);
        // $attendance = $stmt->fetchAll();
        $attendance = [
            ['date' => '2025-10-01', 'status' => 'present', 'remarks' => ''],
            ['date' => '2025-10-03', 'status' => 'absent', 'remarks' => 'Sick'],
        ];
    } elseif ($user['role'] === 'teacher' || $user['role'] === 'admin') {
        // $stmt = $pdo->prepare("SELECT * FROM Attendance WHERE subject_id = ?");
        // $stmt->execute([$subject_id]);
        // $attendance = $stmt->fetchAll();
        $attendance = [
            ['date' => '2025-10-01', 'student_id' => 10, 'status' => 'present', 'remarks' => ''],
            ['date' => '2025-10-01', 'student_id' => 11, 'status' => 'present', 'remarks' => ''],
            ['date' => '2025-10-03', 'student_id' => 10, 'status' => 'absent', 'remarks' => 'Sick'],
        ];
    }
    // --- End Placeholder ---

    json_response(['success' => true, 'attendance' => $attendance]);

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>