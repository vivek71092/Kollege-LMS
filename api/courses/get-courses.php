<?php
// /api/courses/get-courses.php

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
    $courses = [];

    // --- Placeholder Logic ---
    // This logic would differ based on user role
    if ($user['role'] === 'student') {
        // $stmt = $pdo->prepare("SELECT s.id, s.subject_name, s.subject_code FROM Subjects s JOIN Enrollments e ON s.id = e.subject_id WHERE e.student_id = ?");
        // $stmt->execute([$user['id']]);
        // $courses = $stmt->fetchAll();
        $courses = [
            ['id' => 1, 'subject_name' => 'Web Development', 'subject_code' => 'CS305'],
            ['id' => 2, 'subject_name' => 'Data Science', 'subject_code' => 'CS306']
        ];
    } elseif ($user['role'] === 'teacher') {
        // $stmt = $pdo->prepare("SELECT s.id, s.subject_name, s.subject_code FROM Subjects s JOIN Courses c ON s.course_id = c.id WHERE c.teacher_id = ?");
        // $stmt->execute([$user['id']]);
        // $courses = $stmt->fetchAll();
        $courses = [
            ['id' => 1, 'subject_name' => 'Web Development', 'subject_code' => 'CS305'],
        ];
    } elseif ($user['role'] === 'admin') {
        // $courses = $pdo->query("SELECT id, subject_name, subject_code FROM Subjects")->fetchAll();
        $courses = [
            ['id' => 1, 'subject_name' => 'Web Development', 'subject_code' => 'CS305'],
            ['id' => 2, 'subject_name' => 'Data Science', 'subject_code' => 'CS306'],
            ['id' => 3, 'subject_name' => 'Business Analytics', 'subject_code' => 'MBA501']
        ];
    }
    // --- End Placeholder ---

    json_response(['success' => true, 'courses' => $courses]);

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>