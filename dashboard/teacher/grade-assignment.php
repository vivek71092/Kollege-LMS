<?php
// /dashboard/teacher/grade-assignment.php

// Load core files
require_once '../../config.php';
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['teacher']);
$user = get_session_user();
$teacher_id = $user['id'];

// Get submission ID from URL
$submission_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$submission_id) {
    $_SESSION['error_message'] = "Invalid submission ID.";
    redirect('dashboard/teacher/manage-assignments.php');
}

// --- Grade Processing (POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marks = filter_input(INPUT_POST, 'marks_obtained', FILTER_SANITIZE_NUMBER_INT);
    $feedback = sanitize_input($_POST['feedback']);
    $sub_id_hidden = filter_input(INPUT_POST, 'submission_id', FILTER_SANITIZE_NUMBER_INT);

    // --- PLACEHOLDER LOGIC ---
    // 1. Get max marks for validation
    // $max_marks_query = $pdo->query("SELECT a.max_marks, a.id FROM Assignments a JOIN Submissions sub ON a.id = sub.assignment_id WHERE sub.id = $sub_id_hidden")->fetch();
    $max_marks = 50;
    $assignment_id = 1;
    
    // 2. Validate
    if ($marks > $max_marks || $marks < 0) {
        $_SESSION['error_message'] = "Marks must be between 0 and $max_marks.";
    } else {
        // 3. Update the `Submissions` table
        // $stmt = $pdo->prepare("UPDATE Submissions SET marks_obtained = ?, feedback = ?, status = 'graded', graded_date = NOW(), graded_by = ? WHERE id = ?");
        // $stmt->execute([$marks, $feedback, $teacher_id, $sub_id_hidden]);
        
        // 4. Update the main `Marks` table (aggregate) - more complex logic needed here
        
        $_SESSION['success_message'] = "Assignment graded successfully! (Simulated)";
        redirect('dashboard/teacher/view-submissions.php?id=' . $assignment_id);
    }
    // Redirect back on error
    redirect('dashboard/teacher/grade-assignment.php?id=' . $sub_id_hidden);
}
// --- End Processing ---


// --- Placeholder Data (GET) ---
// $sql = "SELECT sub.*, u.first_name, u.last_name, a.title, a.max_marks
//         FROM Submissions sub
//         JOIN Users u ON sub.student_id = u.id
//         JOIN Assignments a ON sub.assignment_id = a.id
//         WHERE sub.id = ? AND a.created_by = ?"; // Verify teacher ownership
// $stmt = $pdo->prepare($sql);
// $stmt->execute([$submission_id, $teacher_id]);
// $submission = $stmt->fetch();
$submission = [
    'id' => $submission_id, 'file_path' => 'public/uploads/submissions/alice_proj1.zip', 'status' => 'submitted', 'marks_obtained' => null, 'feedback' => '',
    'first_name' => 'Alice', 'last_name' => 'Smith', 'title' => 'Project Phase 1', 'max_marks' => 50
];

if (!$submission) {
    $_SESSION['error_message'] = "Submission not found or you are not authorized.";
    redirect('dashboard/teacher/manage-assignments.php');
}
// --- End Placeholder Data ---

$page_title = "Grade: " . htmlspecialchars($submission['first_name'] . ' ' . $submission['last_name']);
require_once '../../includes/header.php';
?>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h4 class="mb-0">Submission Details</h4>
            </div>
            <div class="card-body">
                <h5><?php echo htmlspecialchars($submission['title']); ?></h5>
                <p>
                    <strong>Student:</strong> <?php echo htmlspecialchars($submission['first_name'] . ' ' . $submission['last_name']); ?><br>
                    <strong>Status:</strong> <span class="badge bg-<?php echo $submission['status'] == 'graded' ? 'success' : 'warning text-dark'; ?>"><?php echo ucfirst($submission['status']); ?></span>
                </p>
                <a href="<?php echo BASE_URL . $submission['file_path']; ?>" class="btn btn-success" target="_blank" download>
                    <i class="fas fa-download me-2"></i> Download Submitted File
                </a>
            </div>
            <div class="card-footer">
                <a href="view-submissions.php?id=<?php echo $submission['assignment_id'] ?? 1; ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to All Submissions
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h4 class="mb-0">Grade Assignment</h4>
            </div>
            <div class="card-body">
                <?php 
                display_flash_message('success_message', 'alert-success');
                display_flash_message('error_message', 'alert-danger');
                ?>
                <form action="grade-assignment.php" method="POST">
                    <input type="hidden" name="submission_id" value="<?php echo $submission['id']; ?>">
                    <div class="mb-3">
                        <label for="marks_obtained" class="form-label">Marks</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="marks_obtained" name="marks_obtained" value="<?php echo htmlspecialchars($submission['marks_obtained']); ?>" min="0" max="<?php echo $submission['max_marks']; ?>" required>
                            <span class="input-group-text">/ <?php echo $submission['max_marks']; ?></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="feedback" class="form-label">Feedback / Comments</label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="8"><?php echo htmlspecialchars($submission['feedback']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <?php echo $submission['status'] == 'graded' ? 'Update Grade' : 'Submit Grade'; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>