<?php
// /dashboard/teacher/view-submissions.php

// Load core files
require_once '../../config.php';
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['teacher']);
$user = get_session_user();
$teacher_id = $user['id'];

// Get assignment ID from URL
$assignment_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$assignment_id) {
    $_SESSION['error_message'] = "Invalid assignment ID.";
    redirect('dashboard/teacher/manage-assignments.php');
}

// --- Placeholder Data ---
// 1. Get Assignment Title (and verify teacher owns it)
// $stmt = $pdo->prepare("SELECT a.title, a.max_marks FROM Assignments a WHERE a.id = ? AND a.created_by = ?");
// $stmt->execute([$assignment_id, $teacher_id]);
// $assignment = $stmt->fetch();
$assignment = ['title' => 'Project Phase 1', 'max_marks' => 50];

if (!$assignment) {
    $_SESSION['error_message'] = "Assignment not found or you are not authorized.";
    redirect('dashboard/teacher/manage-assignments.php');
}

// 2. Get Submissions
// $sql = "SELECT sub.id, sub.submission_date, sub.file_path, sub.status, sub.marks_obtained, u.first_name, u.last_name
//         FROM Submissions sub
//         JOIN Users u ON sub.student_id = u.id
//         WHERE sub.assignment_id = ?
//         ORDER BY sub.status, sub.submission_date DESC";
// $stmt = $pdo->prepare($sql);
// $stmt->execute([$assignment_id]);
// $submissions = $stmt->fetchAll();
$submissions = [
    ['id' => 1, 'submission_date' => '2025-10-22 09:00:00', 'file_path' => 'public/uploads/submissions/alice_proj1.zip', 'status' => 'submitted', 'marks_obtained' => null, 'first_name' => 'Alice', 'last_name' => 'Smith'],
    ['id' => 2, 'submission_date' => '2025-10-14 10:00:00', 'file_path' => 'public/uploads/submissions/bob_proj1.zip', 'status' => 'graded', 'marks_obtained' => 45, 'first_name' => 'Bob', 'last_name' => 'Johnson'],
];
// --- End Placeholder Data ---

$page_title = "Submissions: " . htmlspecialchars($assignment['title']);
require_once '../../includes/header.php';
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
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Student Name</th>
                        <th>Submitted On</th>
                        <th>Status</th>
                        <th>Marks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($submissions)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No submissions found for this assignment.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($submissions as $sub): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sub['first_name'] . ' ' . $sub['last_name']); ?></td>
                                <td><?php echo format_date($sub['submission_date']); ?></td>
                                <td>
                                    <?php if ($sub['status'] == 'graded'): ?>
                                        <span class="badge bg-success">Graded</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $sub['marks_obtained'] ?? 'N/A'; ?> / <?php echo $assignment['max_marks']; ?>
                                </td>
                                <td>
                                    <a href="<?php echo BASE_URL . $sub['file_path']; ?>" class="btn btn-sm btn-outline-success" target="_blank" download>
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <a href="grade-assignment.php?id=<?php echo $sub['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit me-1"></i> 
                                        <?php echo $sub['status'] == 'graded' ? 'Regrade' : 'Grade'; ?>
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
require_once '../../includes/footer.php';
?>