<?php
// /api/search.php

header('Content-Type: application/json');
require_once '../config.php';
require_once '../functions.php';
require_once '../auth/check_auth.php'; // Ensures user is logged in

// --- JSON Response Helper ---
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit;
}
// --- End Helper ---

try {
    $user = get_session_user();
    $term = sanitize_input($_GET['q'] ?? '');

    if (strlen($term) < 3) {
        json_response(['success' => false, 'error' => 'Search term must be at least 3 characters.'], 400);
    }

    $results = [];
    
    // --- Placeholder Logic ---
    // This is a complex query. You'd search multiple tables based on user role.
    
    if ($user['role'] === 'student') {
        // Search `Notes`, `Assignments`, `Subjects`
        // $stmt = $pdo->prepare("SELECT title, 'Note' as type, file_path as url FROM Notes WHERE (title LIKE ? OR description LIKE ?) AND subject_id IN (SELECT subject_id FROM Enrollments WHERE student_id = ?)");
        // $stmt->execute(["%$term%", "%$term%", $user['id']]);
        // $results = $stmt->fetchAll();
        $results = [
            ['title' => 'Lecture 1: Intro to HTML', 'type' => 'Note', 'url' => 'dashboard/student/notes.php#note-1']
        ];
    } elseif ($user['role'] === 'admin') {
        // Search `Users`, `Courses`, `Subjects`
        // $stmt = $pdo->prepare("SELECT CONCAT(first_name, ' ', last_name) as title, 'User' as type, CONCAT('dashboard/admin/users/edit-user.php?id=', id) as url FROM Users WHERE email LIKE ? OR first_name LIKE ? OR last_name LIKE ?");
        // $stmt->execute(["%$term%", "%$term%", "%$term%"]);
        // $results = $stmt->fetchAll();
        $results = [
            ['title' => 'Alice Smith', 'type' => 'User', 'url' => 'dashboard/admin/users/edit-user.php?id=10']
        ];
    }
    // --- End Placeholder ---

    json_response(['success' => true, 'results' => $results]);

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>