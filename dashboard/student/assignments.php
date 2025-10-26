<?php
// /dashboard/student/assignments.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['student']);

$page_title = "My Assignments";
$user = get_session_user();
$student_id = $user['id'];

// --- Fetch Assignments Data ---
$assignments = []; // Initialize
$fetch_error = null;
try {
    // Query assignments for subjects the student is enrolled in
    // LEFT JOIN Submissions to check status
    $sql = "SELECT
                a.id, a.title, a.due_date, a.max_marks,
                s.subject_name,
                sub.status AS submission_status,
                sub.marks_obtained
            FROM Assignments a
            JOIN Subjects s ON a.subject_id = s.id
            JOIN Enrollments e ON s.id = e.subject_id
            LEFT JOIN Submissions sub ON a.id = sub.assignment_id AND sub.student_id = e.student_id
            WHERE e.student_id = ? AND e.status = 'enrolled' AND a.status = 'published'
            ORDER BY a.due_date DESC"; // Show most recent due dates first

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$student_id]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    log_error("Error fetching assignments for student ID {$student_id}: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not fetch your assignments list from the database.";
    $_SESSION['error_message'] = $fetch_error;
}
// --- End Data Fetching ---

// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0"><?php echo $page_title; ?></h4>
    </div>
    <div class="card-body">

        <?php
        display_flash_message('success_message', 'alert-success');
        display_flash_message('error_message', 'alert-danger');
        ?>

        <?php if ($fetch_error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($fetch_error); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle data-table" id="assignmentsTable">
                <thead class="table-light">
                    <tr>
                        <th>Subject</th>
                        <th>Assignment Title</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Marks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($assignments)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No assignments found for your enrolled subjects.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($assignments as $assignment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($assignment['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                <td><?php echo format_date($assignment['due_date']); ?></td>
                                <td>
                                    <?php
                                    // Determine display status based on submission status and due date
                                    $status = $assignment['submission_status'];
                                    $due_date = new DateTime($assignment['due_date']);
                                    $now = new DateTime();
                                    $badge_class = 'bg-secondary';
                                    $status_text = 'Unknown';

                                    if ($status === 'graded') {
                                        $badge_class = 'bg-success';
                                        $status_text = 'Graded';
                                    } elseif ($status === 'submitted' || $status === 'resubmit') { // Treat resubmit visually as submitted
                                        $badge_class = 'bg-info';
                                        $status_text = 'Submitted';
                                    } elseif ($now > $due_date) {
                                        $badge_class = 'bg-danger';
                                        $status_text = 'Overdue';
                                    } else {
                                        $badge_class = 'bg-warning text-dark';
                                        $status_text = 'Pending';
                                    }
                                    echo '<span class="badge ' . $badge_class . '">' . $status_text . '</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php // Display marks if graded
                                    if ($assignment['submission_status'] === 'graded' && isset($assignment['marks_obtained'])) {
                                        echo '<strong>' . htmlspecialchars($assignment['marks_obtained']) . ' / ' . htmlspecialchars($assignment['max_marks']) . '</strong>';
                                    } else {
                                        echo '<span class="text-muted">N/A</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="view-assignment.php?id=<?php echo $assignment['id']; ?>" class="btn btn-primary btn-sm">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div> </div> </div> <?php
require_once $path_prefix . 'includes/footer.php';
?>