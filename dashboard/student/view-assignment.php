<?php
// /dashboard/student/view-assignment.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['student']);

$user = get_session_user(); // Get logged-in student info
$student_id = $user['id'];

// Get assignment ID from URL
$assignment_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$assignment_id) {
    $_SESSION['error_message'] = "Invalid assignment ID provided.";
    redirect('dashboard/student/assignments.php');
}

// --- Fetch Data ---
$assignment = null;
$submission = null;
$fetch_error = null;
$is_enrolled = false; // Flag to check enrollment

try {
    // 1. Fetch assignment details and join Subject to get name
    $stmt_assign = $pdo->prepare(
        "SELECT a.*, s.subject_name, s.id as subject_id
         FROM Assignments a
         JOIN Subjects s ON a.subject_id = s.id
         WHERE a.id = ? AND a.status = 'published'" // Ensure assignment is published
    );
    $stmt_assign->execute([$assignment_id]);
    $assignment = $stmt_assign->fetch(PDO::FETCH_ASSOC);

    if (!$assignment) {
        throw new Exception("Assignment not found or is not currently published.");
    }

    // 2. Verify student is enrolled in the subject this assignment belongs to
    $stmt_enroll = $pdo->prepare(
        "SELECT 1 FROM Enrollments WHERE student_id = ? AND subject_id = ? AND status = 'enrolled'"
    );
    $stmt_enroll->execute([$student_id, $assignment['subject_id']]);
    $is_enrolled = (bool) $stmt_enroll->fetchColumn();

    if (!$is_enrolled) {
         throw new Exception("You are not enrolled in the subject for this assignment.");
    }

    // 3. Fetch the student's submission for this assignment, if it exists
    $stmt_sub = $pdo->prepare(
        "SELECT * FROM Submissions WHERE assignment_id = ? AND student_id = ?"
    );
    $stmt_sub->execute([$assignment_id, $student_id]);
    $submission = $stmt_sub->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    log_error("Error fetching assignment/submission details for student ID {$student_id}, assignment ID {$assignment_id}: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not load assignment details from the database.";
    $_SESSION['error_message'] = $fetch_error;
    // Redirect if critical data (assignment) is missing
    if (!$assignment) {
        redirect('dashboard/student/assignments.php');
    }
} catch (Exception $e) { // Catch authorization or not found errors
     log_error("Error on view-assignment page: " . $e->getMessage(), __FILE__, __LINE__);
     $fetch_error = $e->getMessage(); // Show specific error (like not enrolled)
     $_SESSION['error_message'] = $fetch_error;
     // Redirect if not enrolled or assignment missing
     redirect('dashboard/student/assignments.php');
}
// --- End Data Fetching ---

// --- Determine Submission Status ---
$is_overdue = false;
$can_submit = false;
if ($assignment) {
    $due_date = new DateTime($assignment['due_date']);
    $now = new DateTime();
    $is_overdue = ($now > $due_date);

    // Student can submit if:
    // 1. They haven't submitted yet AND it's not overdue
    // 2. OR their submission status allows resubmission (e.g., status = 'resubmit') - add this status if needed
    $can_submit = (!$submission && !$is_overdue);
    // if ($submission && $submission['status'] == 'resubmit' && !$is_overdue) { $can_submit = true; } // Example resubmit logic
}
// ---

$page_title = $assignment ? htmlspecialchars($assignment['title']) : "View Assignment";
// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<?php if ($fetch_error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($fetch_error); ?></div>
<?php endif; ?>


<?php if ($assignment && $is_enrolled): // Only display if assignment loaded and student is enrolled ?>
<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h4 class="mb-0">Assignment Details</h4>
            </div>
            <div class="card-body">
                <h3 class="h5"><?php echo htmlspecialchars($assignment['subject_name']); ?></h3>
                <h2 class="h4 mb-3"><?php echo htmlspecialchars($assignment['title']); ?></h2>
                <hr>
                <?php if (!empty($assignment['description'])): ?>
                    <p><strong>Description:</strong><br> <?php echo nl2br(htmlspecialchars($assignment['description'])); ?></p>
                <?php endif; ?>
                <?php if (!empty($assignment['instructions'])): ?>
                    <p><strong>Instructions:</strong><br> <?php echo nl2br(htmlspecialchars($assignment['instructions'])); ?></p>
                <?php endif; ?>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <strong>Due Date:</strong>
                    <span class="<?php echo $is_overdue ? 'text-danger fw-bold' : ''; ?>"><?php echo format_date($assignment['due_date']); ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <strong>Max Marks:</strong>
                    <span><?php echo htmlspecialchars($assignment['max_marks']); ?></span>
                </li>
            </ul>
        </div>
    </div>

    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light">
                <h4 class="mb-0">Submission Status</h4>
            </div>
            <div class="card-body d-flex flex-column">

                <?php display_flash_message('success_message', 'alert-success'); ?>
                <?php display_flash_message('error_message', 'alert-danger'); ?>

                <?php if ($submission): ?>
                    <h5 class="mb-3">Your Submission</h5>
                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Status:</strong>
                            <?php
                            $sub_status = $submission['status'];
                            $sub_badge = 'bg-secondary';
                            if ($sub_status == 'graded') $sub_badge = 'bg-success';
                            elseif ($sub_status == 'submitted' || $sub_status == 'resubmit') $sub_badge = 'bg-info';
                            elseif ($sub_status == 'late') $sub_badge = 'bg-danger';
                            ?>
                            <span class="badge <?php echo $sub_badge; ?>"><?php echo ucfirst($sub_status); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Submitted On:</strong>
                            <span><?php echo format_date($submission['submission_date']); ?></span>
                        </li>
                        <li class="list-group-item">
                            <strong>Submitted File:</strong><br>
                            <a href="<?php echo BASE_URL . htmlspecialchars($submission['file_path']); ?>" download class="btn btn-sm btn-outline-success mt-1">
                                <i class="fas fa-download me-1"></i> <?php echo basename($submission['file_path']); ?>
                            </a>
                        </li>
                    </ul>

                    <?php if ($submission['status'] == 'graded'): ?>
                        <h5 class="mt-2 mb-3">Feedback</h5>
                        <div class="alert alert-secondary"> <h6 class="alert-heading">Grade: <?php echo htmlspecialchars($submission['marks_obtained'] ?? 'N/A'); ?> / <?php echo htmlspecialchars($assignment['max_marks']); ?></h6>
                            <?php if (!empty($submission['feedback'])): ?>
                                <hr>
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($submission['feedback'])); ?></p>
                            <?php else: ?>
                                <p class="mb-0 fst-italic">No specific feedback provided.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                     <?php /* if ($can_submit && $submission['status'] == 'resubmit'): ?>
                        <div class="mt-auto">
                           <hr>
                           <h5>Resubmit Your Work</h5>
                           <form action="submit-assignment.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                                <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>"> <button type="submit" class="btn btn-warning">Resubmit Assignment</button>
                           </form>
                        </div>
                    <?php endif; */ ?>


                <?php elseif ($is_overdue): ?>
                    <div class="alert alert-danger mt-auto"> <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Past Due Date</h5>
                        <p>The due date for this assignment (<?php echo format_date($assignment['due_date']); ?>) has passed. Submissions are no longer accepted.</p>
                    </div>

                <?php else: // $can_submit is true ?>
                    <div class="mt-auto"> <h5 class="mb-3">Submit Your Work</h5>
                        <p class="small text-muted">Please upload your assignment file below before the due date.</p>
                        <form action="submit-assignment.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                            <div class="mb-3">
                                <label for="assignment_file" class="form-label">Select File</label>
                                <input class="form-control" type="file" id="assignment_file" name="assignment_file" required>
                                <div class="form-text">Allowed types: PDF, DOCX, ZIP, SQL, TXT. Max size: 10MB.</div>
                                <div class="invalid-feedback">Please select a file to upload.</div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Submit Assignment</button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

            </div> </div> </div> </div> <?php endif; // End check for $assignment && $is_enrolled ?>

<?php
require_once $path_prefix . 'includes/footer.php';
?>