<?php
// /dashboard/student/submit-assignment.php

// Load core files
require_once '../../config.php';
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['student']);

$user = get_session_user();
$student_id = $user['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- Basic Validation ---
    $assignment_id = filter_input(INPUT_POST, 'assignment_id', FILTER_SANITIZE_NUMBER_INT);
    $comments = sanitize_input($_POST['comments'] ?? '');
    
    if (empty($assignment_id) || !isset($_FILES['assignment_file']) || $_FILES['assignment_file']['error'] != 0) {
        $_SESSION['error_message'] = "Invalid submission. Please select a file to upload.";
        redirect('dashboard/student/view-assignment.php?id=' . $assignment_id);
    }

    $file = $_FILES['assignment_file'];
    
    // --- File Validation ---
    $max_size = 10 * 1024 * 1024; // 10MB
    $allowed_types = ['application/pdf', 'application/zip', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'application/sql'];
    
    if ($file['size'] > $max_size) {
        $_SESSION['error_message'] = "File is too large. Max size is 10MB.";
        redirect('dashboard/student/view-assignment.php?id=' . $assignment_id);
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        $_SESSION['error_message'] = "Invalid file type. Allowed types: PDF, DOCX, ZIP, SQL, TXT.";
        redirect('dashboard/student/view-assignment.php?id=' . $assignment_id);
    }

    // --- File Upload Logic ---
    // Create a unique filename: studentID_assignmentID_originalName
    $file_name = $student_id . '_' . $assignment_id . '_' . basename($file['name']);
    $upload_dir = '../../public/uploads/submissions/';
    $file_path = $upload_dir . $file_name;
    $db_path = 'public/uploads/submissions/' . $file_name; // Path to store in DB

    // Ensure upload directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // --- PLACEHOLDER LOGIC ---
    // In a real app, you would:
    // 1. Check if submission is not overdue.
    // 2. Check if student is enrolled for this assignment's subject.
    // 3. Move the uploaded file
    // if (move_uploaded_file($file['tmp_name'], $file_path)) {
    //    // 4. Insert into 'Submissions' table
    //    $stmt = $pdo->prepare(
    //        "INSERT INTO Submissions (assignment_id, student_id, submission_date, file_path, status, feedback) 
    //         VALUES (?, ?, NOW(), ?, 'submitted', ?)"
    //    );
    //    $stmt->execute([$assignment_id, $student_id, $db_path, $comments]);
    //    $_SESSION['success_message'] = "Assignment submitted successfully!";
    // } else {
    //    $_SESSION['error_message'] = "Failed to upload file.";
    // }
    
    // For this template, we just simulate success
    $_SESSION['success_message'] = "Assignment submitted successfully! (Simulated)";
    redirect('dashboard/student/view-assignment.php?id=' . $assignment_id);

} else {
    // If accessed via GET, redirect
    redirect('dashboard/student/assignments.php');
}
?>