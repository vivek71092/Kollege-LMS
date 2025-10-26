<?php
// /dashboard/student/view-course.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['student']);

$user = get_session_user(); // Get logged-in student info
$student_id = $user['id'];

// Get subject ID from URL
$subject_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$subject_id) {
    $_SESSION['error_message'] = "Invalid subject ID provided.";
    redirect('dashboard/student/courses.php');
}

// --- Fetch Data ---
$subject = null;
$notes = [];
$assignments = [];
$fetch_error = null;

try {
    // 1. Verify student is enrolled in this subject
    $stmt_enroll = $pdo->prepare(
        "SELECT 1 FROM Enrollments WHERE student_id = ? AND subject_id = ? AND status = 'enrolled'"
    );
    $stmt_enroll->execute([$student_id, $subject_id]);
    if (!$stmt_enroll->fetch()) {
        $_SESSION['error_message'] = "You are not enrolled in this subject or access is denied.";
        redirect('dashboard/student/courses.php');
    }

    // 2. Fetch subject details (join Course for program name, Users for teacher name)
    $stmt_subject = $pdo->prepare(
       "SELECT s.subject_name, s.subject_code, c.course_name, s.description AS subject_description, 
               CONCAT(u.first_name, ' ', u.last_name) AS teacher_name
        FROM Subjects s
        JOIN Courses c ON s.course_id = c.id
        LEFT JOIN Users u ON c.teacher_id = u.id 
        WHERE s.id = ?"
    );
    $stmt_subject->execute([$subject_id]);
    $subject = $stmt_subject->fetch(PDO::FETCH_ASSOC);

    if (!$subject) {
        // Should not happen if enrollment check passed, but handle defensively
        throw new Exception("Subject details not found even though enrollment exists.");
    }

    // 3. Fetch recent notes for this subject (limit 5)
    $stmt_notes = $pdo->prepare(
        "SELECT id, title, description, upload_date, file_path 
         FROM Notes 
         WHERE subject_id = ? 
         ORDER BY upload_date DESC 
         LIMIT 5"
    );
    $stmt_notes->execute([$subject_id]);
    $notes = $stmt_notes->fetchAll(PDO::FETCH_ASSOC);

    // 4. Fetch recent assignments for this subject (limit 5)
    $stmt_assignments = $pdo->prepare(
        "SELECT id, title, due_date 
         FROM Assignments 
         WHERE subject_id = ? AND status = 'published'
         ORDER BY due_date DESC 
         LIMIT 5"
    );
    $stmt_assignments->execute([$subject_id]);
    $assignments = $stmt_assignments->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    log_error("Error fetching subject details for student ID {$student_id}, subject ID {$subject_id}: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not load subject details from the database.";
    $_SESSION['error_message'] = $fetch_error;
    // Redirect if critical data (subject) is missing
    if (!$subject) {
        redirect('dashboard/student/courses.php');
    }
} catch (Exception $e) { // Catch other errors like the defensive throw above
    log_error("Error on view-course page: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "An unexpected error occurred: " . $e->getMessage();
     $_SESSION['error_message'] = $fetch_error;
     if (!$subject) {
        redirect('dashboard/student/courses.php');
    }
}
// --- End Data Fetching ---

$page_title = $subject ? htmlspecialchars($subject['subject_name']) : "View Subject";
// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<?php if ($fetch_error && $subject): // Show non-critical errors only if subject loaded ?>
    <div class="alert alert-warning"><?php echo htmlspecialchars($fetch_error); ?></div>
<?php endif; ?>

<?php if ($subject): // Only proceed if subject details were loaded ?>
<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h4"><?php echo htmlspecialchars($subject['subject_name']); ?> (<?php echo htmlspecialchars($subject['subject_code']); ?>)</h2>
                <p class="text-muted mb-1">
                    Part of: <?php echo htmlspecialchars($subject['course_name']); ?><br>
                    Instructor: <?php echo htmlspecialchars($subject['teacher_name'] ?? 'N/A'); ?>
                </p>
                <?php if (!empty($subject['subject_description'])): ?>
                    <p><?php echo nl2br(htmlspecialchars($subject['subject_description'])); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <h5 class="mb-0"><i class="fas fa-book-open me-2"></i> Latest Notes</h5>
                <a href="notes.php?subject_id=<?php echo $subject_id; ?>" class="btn btn-outline-secondary btn-sm">View All Notes</a>
            </div>
            <?php if (empty($notes)): ?>
                <div class="card-body text-center text-muted">
                    No notes have been uploaded for this subject yet.
                </div>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($notes as $note): ?>
                        <li class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo htmlspecialchars($note['title']); ?></h6>
                                <a href="<?php echo BASE_URL . $note['file_path']; ?>" class="btn btn-sm btn-outline-success" target="_blank" download title="Download Note">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                            <?php if(!empty($note['description'])): ?>
                            <p class="mb-1 small text-muted"><?php echo htmlspecialchars($note['description']); ?></p>
                            <?php endif; ?>
                            <small class="text-muted">Uploaded: <?php echo format_date($note['upload_date'], 'M d, Y'); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <h5 class="mb-0"><i class="fas fa-tasks me-2"></i> Recent Assignments</h5>
                <a href="assignments.php?subject_id=<?php echo $subject_id; ?>" class="btn btn-outline-secondary btn-sm">View All Assignments</a>
            </div>
             <?php if (empty($assignments)): ?>
                <div class="card-body text-center text-muted">
                    No assignments have been posted for this subject yet.
                </div>
            <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($assignments as $assignment): ?>
                         <a href="view-assignment.php?id=<?php echo $assignment['id']; ?>" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo htmlspecialchars($assignment['title']); ?></h6>
                                <small class="text-danger">Due: <?php echo format_date($assignment['due_date'], 'M d, Y'); ?></small>
                            </div>
                         </a>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; // End check for $subject ?>

<?php
require_once $path_prefix . 'includes/footer.php';
?>