<?php
// /api/attendance/mark-attendance.php

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
    require_role(['teacher', 'admin']); // Only teachers/admins can mark
    
    $data = json_decode(file_get_contents('php://input'), true);
    $subject_id = filter_var($data['subject_id'], FILTER_SANITIZE_NUMBER_INT);
    $date = sanitize_input($data['date']);
    $attendance_list = $data['attendance']; // Expects an array: [{student_id: 1, status: 'present'}, ...]

    if (empty($subject_id) || empty($date) || empty($attendance_list) || !is_array($attendance_list)) {
        json_response(['success' => false, 'error' => 'Invalid data. Subject, date, and attendance list are required.'], 400);
    }

    // --- Placeholder Logic ---
    // $pdo->beginTransaction();
    // $stmt = $pdo->prepare("INSERT INTO Attendance (student_id, subject_id, date, status, teacher_id, remarks, created_at) 
    //                      VALUES (?, ?, ?, ?, ?, ?, NOW()) 
    //                      ON DUPLICATE KEY UPDATE status = VALUES(status), remarks = VALUES(remarks)");
    
    // foreach ($attendance_list as $record) {
    //    $stmt->execute([
    //        $record['student_id'],
    //        $subject_id,
    //        $date,
    //        $record['status'],
    //        $user['id'],
    //        $record['remarks'] ?? ''
    //    ]);
    // }
    // $pdo->commit();
    $success = true;
    // --- End Placeholder ---

    if ($success) {
        json_response(['success' => true, 'message' => 'Attendance saved successfully.']);
    } else {
        // $pdo->rollBack();
        json_response(['success' => false, 'error' => 'Failed to save attendance.'], 500);
    }

} catch (Exception $e) {
    // if ($pdo->inTransaction()) { $pdo->rollBack(); }
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>