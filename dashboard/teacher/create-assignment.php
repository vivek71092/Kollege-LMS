<?php
// /dashboard/teacher/create-assignment.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['teacher']); // Only teachers can create assignments here
$user = get_session_user(); // Get logged-in teacher info
$teacher_id = $user['id'];

$page_title = "Create Assignment";

// --- Assignment Creation Processing (POST request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_SANITIZE_NUMBER_INT);
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']); // Allow more content, maybe sanitize differently if allowing HTML
    $instructions = sanitize_input($_POST['instructions']); // Same as description
    $due_date = sanitize_input($_POST['due_date']); // Should be in 'YYYY-MM-DDTHH:MM' format from datetime-local
    $max_marks = filter_input(INPUT_POST, 'max_marks', FILTER_SANITIZE_NUMBER_INT);

    // Basic Validation
    if (empty($subject_id) || empty($title) || empty($due_date) || empty($max_marks) || $max_marks <= 0) {
        $_SESSION['error_message'] = "Please select a subject, provide a title, set a valid due date, and enter positive max marks.";
        redirect('dashboard/teacher/create-assignment.php'); // Redirect back to form
    } else {
        // --- ACTUAL DATABASE INSERT LOGIC ---
        try {
            // Optional: Verify that the logged-in teacher is actually assigned to this subject_id
            $stmt_check = $pdo->prepare("SELECT c.id FROM Courses c JOIN Subjects s ON c.id = s.course_id WHERE s.id = ? AND c.teacher_id = ?");
            $stmt_check->execute([$subject_id, $teacher_id]);
            if (!$stmt_check->fetch()) {
                 // Or check ClassSchedule if teachers can teach subjects outside their main course
                 $stmt_check_schedule = $pdo->prepare("SELECT cs.id FROM ClassSchedule cs WHERE cs.subject_id = ? AND cs.teacher_id = ?");
                 $stmt_check_schedule->execute([$subject_id, $teacher_id]);
                 if (!$stmt_check_schedule->fetch()) {
                     $_SESSION['error_message'] = "Error: You are not authorized to create assignments for this subject.";
                     redirect('dashboard/teacher/create-assignment.php');
                 }
            }

            // Prepare the INSERT statement
            $sql = "INSERT INTO Assignments (subject_id, title, description, instructions, due_date, max_marks, created_by, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'published', NOW(), NOW())";
            $stmt = $pdo->prepare($sql);

            // Execute the statement
            $success = $stmt->execute([
                $subject_id,
                $title,
                $description,
                $instructions,
                $due_date, // Store directly from datetime-local input
                $max_marks,
                $teacher_id // ID of the logged-in teacher
            ]);

            if ($success) {
                $_SESSION['success_message'] = "Assignment created successfully!";
                redirect('dashboard/teacher/manage-assignments.php'); // Redirect to the list view
            } else {
                $_SESSION['error_message'] = "Failed to create assignment. Please try again.";
                redirect('dashboard/teacher/create-assignment.php');
            }
        } catch (PDOException $e) {
            log_error("Error creating assignment: " . $e->getMessage(), __FILE__, __LINE__);
            // Provide more specific error in development?
            $error_details = (ENVIRONMENT === 'development') ? $e->getMessage() : 'Please try again later.';
            $_SESSION['error_message'] = "A database error occurred while creating the assignment. " . $error_details;
            redirect('dashboard/teacher/create-assignment.php');
        }
        // --- END ACTUAL DATABASE LOGIC ---
    }
}
// --- End Processing ---


// --- Fetch teacher's subjects for dropdown (GET request part) ---
$subjects = [];
$fetch_error = null;
try {
    // Fetch subjects assigned to this teacher (either via Course or ClassSchedule)
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
     $_SESSION['error_message'] = $fetch_error; // Show error on page load
}
// --- End Fetch ---

// Pre-select subject if ID is in URL from manage-course page
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

                <form action="create-assignment.php" method="POST" class="needs-validation" novalidate>
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
                        <label for="title" class="form-label">Assignment Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                        <div class="invalid-feedback">Please provide a title for the assignment.</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5" placeholder="Provide details about the assignment..."></textarea>
                    </div>
                     <div class="mb-3">
                        <label for="instructions" class="form-label">Instructions (Optional)</label>
                        <textarea class="form-control" id="instructions" name="instructions" rows="3" placeholder="Specific instructions for submission..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="due_date" class="form-label">Due Date & Time</label>
                            <input type="datetime-local" class="form-control" id="due_date" name="due_date" required>
                            <div class="invalid-feedback">Please set a due date and time.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="max_marks" class="form-label">Max Marks</label>
                            <input type="number" class="form-control" id="max_marks" name="max_marks" min="1" required>
                            <div class="invalid-feedback">Please set the maximum marks (must be > 0).</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Assignment</button>
                    <a href="manage-assignments.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>