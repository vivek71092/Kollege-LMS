<?php
// /dashboard/teacher/upload-notes.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['teacher']); // Only teachers can upload notes here
$user = get_session_user(); // Get logged-in teacher info
$teacher_id = $user['id'];

$page_title = "Upload Notes";

// --- File Upload Processing (POST request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize text inputs
    $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_SANITIZE_NUMBER_INT);
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description'] ?? '');

    // --- Basic Input Validation ---
    if (empty($subject_id) || empty($title)) {
        $_SESSION['error_message'] = "Please select a subject and enter a title for the note.";
        redirect('dashboard/teacher/upload-notes.php');
    }

    // --- File Validation ---
    if (!isset($_FILES['note_file']) || $_FILES['note_file']['error'] !== UPLOAD_ERR_OK) {
        // Handle specific upload errors
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds MAX_FILE_SIZE directive specified in HTML form.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
        ];
        $error_code = $_FILES['note_file']['error'] ?? UPLOAD_ERR_NO_FILE;
        $_SESSION['error_message'] = "File upload failed: " . ($upload_errors[$error_code] ?? 'Unknown error.');
        redirect('dashboard/teacher/upload-notes.php');
    }

    $file = $_FILES['note_file'];
    $max_size = 10 * 1024 * 1024; // 10MB
    // Define allowed MIME types (adjust as needed)
    $allowed_types = [
        'application/pdf',
        'application/vnd.ms-powerpoint', // .ppt
        'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .pptx
        'application/msword', // .doc
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
        'text/plain', // .txt
        'application/zip', // .zip
        // Add others like image types if allowed: 'image/jpeg', 'image/png'
    ];
    $file_mime_type = mime_content_type($file['tmp_name']); // Get actual MIME type

    if ($file['size'] > $max_size) {
        $_SESSION['error_message'] = "File is too large. Maximum size allowed is 10MB.";
        redirect('dashboard/teacher/upload-notes.php');
    }

    if (!in_array($file_mime_type, $allowed_types)) {
        $_SESSION['error_message'] = "Invalid file type ('" . htmlspecialchars($file_mime_type) . "'). Allowed types: PDF, PPT(X), DOC(X), TXT, ZIP.";
        redirect('dashboard/teacher/upload-notes.php');
    }

    // --- File Upload Logic ---
    // Create a unique filename to prevent overwrites and issues with special characters
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safe_filename = uniqid('note_', true) . '.' . strtolower($file_extension); // Generate unique name

    // Define upload directory relative to this script's location
    $upload_dir = '../../public/uploads/notes/'; // Path from this file to the target dir
    $db_path = 'public/uploads/notes/' . $safe_filename; // Relative path from project root for DB

    // Ensure upload directory exists and is writable
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true) && !is_dir($upload_dir)) { // Create recursively if doesn't exist
            log_error("Failed to create notes upload directory: {$upload_dir}", __FILE__, __LINE__);
             $_SESSION['error_message'] = "Server error: Cannot create upload directory.";
             redirect('dashboard/teacher/upload-notes.php');
        }
    }
     if (!is_writable($upload_dir)) {
        log_error("Notes upload directory is not writable: {$upload_dir}", __FILE__, __LINE__);
        $_SESSION['error_message'] = "Server error: Upload directory is not writable.";
        redirect('dashboard/teacher/upload-notes.php');
    }

    $destination = $upload_dir . $safe_filename;

    // --- ACTUAL DATABASE INSERT LOGIC ---
    try {
        // Optional: Verify teacher authorization for the subject (similar to create-assignment check)
        // ... (add check here if needed) ...

        // Move the uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // File moved successfully, now insert into database
            $sql = "INSERT INTO Notes (subject_id, title, description, file_path, uploaded_by, upload_date, file_type, file_size)
                    VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)";
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([
                $subject_id,
                $title,
                $description,
                $db_path, // Save the relative path from project root
                $teacher_id,
                $file_mime_type,
                $file['size']
            ]);

            if ($success) {
                $_SESSION['success_message'] = "Note uploaded successfully!";
                redirect('dashboard/teacher/manage-notes.php'); // Redirect to the list view
            } else {
                 $_SESSION['error_message'] = "Failed to save note details to database after upload.";
                 // Attempt to delete the orphaned uploaded file
                 if (file_exists($destination)) unlink($destination);
                 redirect('dashboard/teacher/upload-notes.php');
            }
        } else {
             $_SESSION['error_message'] = "Failed to move uploaded file to destination.";
             redirect('dashboard/teacher/upload-notes.php');
        }
    } catch (PDOException $e) {
        log_error("Error saving note to database: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "A database error occurred while saving the note.";
        // Attempt to delete the uploaded file if DB insert failed
        if (isset($destination) && file_exists($destination)) unlink($destination);
        redirect('dashboard/teacher/upload-notes.php');
     } catch (Exception $e) { // Catch other potential errors (e.g., directory creation)
        log_error("Error during note upload process: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "An unexpected error occurred during upload: " . $e->getMessage();
        redirect('dashboard/teacher/upload-notes.php');
    }
    // --- END ACTUAL DATABASE LOGIC ---
}
// --- End POST Processing ---


// --- Fetch teacher's subjects for dropdown (GET request part) ---
$subjects = [];
$fetch_error = null;
try {
    // Fetch subjects assigned to this teacher
     $sql_subjects = "SELECT DISTINCT s.id, s.subject_name
                      FROM Subjects s
                      JOIN Courses c ON s.course_id = c.id
                      WHERE c.teacher_id = ?
                         OR s.id IN (SELECT DISTINCT cs.subject_id FROM ClassSchedule cs WHERE cs.teacher_id = ?)
                      ORDER BY s.subject_name";
    $stmt_subjects = $pdo->prepare($sql_subjects);
    $stmt_subjects->execute([$teacher_id, $teacher_id]);
    $subjects = $stmt_subjects->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
     log_error("Error fetching subjects for teacher ID {$teacher_id}: " . $e->getMessage(), __FILE__, __LINE__);
     $fetch_error = "Could not load subjects for selection.";
     $_SESSION['error_message'] = $fetch_error;
}
// --- End Fetch ---

// Pre-select subject if ID is in URL
$selected_subject = filter_input(INPUT_GET, 'subject_id', FILTER_SANITIZE_NUMBER_INT);

// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0"><?php echo $page_title; ?></h4>
            </div>
            <div class="card-body">

                <?php
                // Display messages (including fetch error)
                display_flash_message('success_message', 'alert-success');
                display_flash_message('error_message', 'alert-danger');
                ?>
                 <?php if ($fetch_error && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
                    <div class="alert alert-warning"><?php echo htmlspecialchars($fetch_error); ?></div>
                <?php endif; ?>

                <form action="upload-notes.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Subject</label>
                        <select class="form-select" id="subject_id" name="subject_id" required>
                            <option value="">Select a subject...</option>
                            <?php if (!empty($subjects)): ?>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?php echo $subject['id']; ?>" <?php if ($subject['id'] == $selected_subject) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($subject['subject_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                 <option value="" disabled>No subjects assigned to you</option>
                            <?php endif; ?>
                        </select>
                        <div class="invalid-feedback">Please select a subject.</div>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Note Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                        <div class="invalid-feedback">Please provide a title for the note.</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="note_file" class="form-label">Note File</label>
                        <input class="form-control" type="file" id="note_file" name="note_file" required>
                        <div class="form-text">Allowed types: PDF, PPT(X), DOC(X), TXT, ZIP. Max size: 10MB.</div>
                        <div class="invalid-feedback">Please choose a file to upload.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Note</button>
                    <a href="manage-notes.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>