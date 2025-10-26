<?php
// /dashboard/admin/content/manage-submissions.php

// Load core files
require_once '../../../config.php';
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Moderate All Submissions";
$user = get_session_user();

// --- Placeholder Data ---
// $sql = "SELECT sub.id, a.title AS assignment_title, CONCAT(u.first_name, ' ', u.last_name) AS student_name, 
//         sub.submission_date, sub.status, sub.marks_obtained, a.max_marks, sub.file_path
//         FROM Submissions sub
//         JOIN Assignments a ON sub.assignment_id = a.id
//         JOIN Users u ON sub.student_id = u.id
//         ORDER BY sub.submission_date DESC";
// $submissions = $pdo->query($sql)->fetchAll();
$submissions = [
    ['id' => 1, 'assignment_title' => 'Project Phase 1', 'student_name' => 'Alice Smith', 'submission_date' => '2025-10-22 09:00:00', 'status' => 'submitted', 'marks_obtained' => null, 'max_marks' => 50, 'file_path' => 'public/uploads/submissions/alice_proj1.zip'],
    ['id' => 2, 'assignment_title' => 'HTML/CSS Homepage', 'student_name' => 'Bob Johnson', 'submission_date' => '2025-10-14 10:00:00', 'status' => 'graded', 'marks_obtained' => 45, 'max_marks' => 50, 'file_path' => 'public/uploads/submissions/bob_proj1.zip'],
    ['id' => 3, 'assignment_title' => 'Analysis Report', 'student_name' => 'Charlie Brown', 'submission_date' => '2025-10-21 14:00:00', 'status' => 'submitted', 'marks_obtained' => null, 'max_marks' => 100, 'file_path' => 'public/uploads/submissions/charlie_report.pdf'],
];
// --- End Placeholder Data ---

require_once '../../../includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0">Content Management: All Submissions</h4>
    </div>
    <div class="card-body">
        
        <?php 
        display_flash_message('success_message', 'alert-success');
        display_flash_message('error_message', 'alert-danger');
        ?>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle data-table">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Assignment</th>
                        <th>Submitted On</th>
                        <th>Status</th>
                        <th>Marks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($submissions)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No submissions found in the system.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($submissions as $sub): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sub['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($sub['assignment_title']); ?></td>
                                <td><?php echo format_date($sub['submission_date']); ?></td>
                                <td>
                                    <?php if ($sub['status'] == 'graded'): ?>
                                        <span class="badge bg-success">Graded</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $sub['marks_obtained'] ?? 'N/A'; ?> / <?php echo $sub['max_marks']; ?></td>
                                <td class="actions-cell">
                                    <a href="<?php echo BASE_URL . $sub['file_path']; ?>" class="btn btn-sm btn-outline-success" target="_blank" download title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="../teacher/grade-assignment.php?id=<?php echo $sub['id']; ?>" class="btn btn-sm btn-outline-primary" title="Grade">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/footer.php';
?>