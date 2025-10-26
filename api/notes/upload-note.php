<?php
// /api/notes/upload-note.php

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
    require_role(['teacher', 'admin']); // Only teachers/admins can upload notes
    
    $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_SANITIZE_NUMBER_INT);
    $title = sanitize_input($_POST['title']);
    
    if (empty($subject_id) || empty($title) || !isset($_FILES['note_file']) || $_FILES['note_file']['error'] != 0) {
        json_response(['success' => false, 'error' => 'Invalid data. Subject, title, and file are required.'], 400);
    }

    // --- Placeholder Logic ---
    // 1. Verify teacher owns this subject_id (if role is teacher)
    // 2. Validate file (size, type)
    // 3. Move file to `public/uploads/notes/`
    // $file = $_FILES['note_file'];
    // $file_name = time() . '_' . basename($file['name']);
    // $db_path = 'public/uploads/notes/' . $file_name;
    // $upload_path = '../../../' . $db_path;
    // move_uploaded_file($file['tmp_name'], $upload_path);
    //
    // 4. Insert into `Notes` table
    // $stmt = $pdo->prepare("INSERT INTO Notes (subject_id, title, description, file_path, uploaded_by, upload_date) VALUES (?, ?, ?, ?, ?, NOW())");
    // $success = $stmt->execute([$subject_id, $title, sanitize_input($_POST['description'] ?? ''), $db_path, $user['id']]);
    $success = true; // Simulate success
    // --- End Placeholder ---

    if ($success) {
        json_response(['success' => true, 'message' => 'Note uploaded successfully.']);
    } else {
        json_response(['success' => false, 'error' => 'Failed to upload note.'], 500);
    }

} catch (Exception $e) {
    log_error($e->getMessage(), __FILE__, __LINE__);
    json_response(['success' => false, 'error' => 'A server error occurred.'], 500);
}
?>