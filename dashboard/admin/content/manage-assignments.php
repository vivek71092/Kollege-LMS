<?php
// /dashboard/admin/content/manage-assignments.php

// Load core files
require_once '../../../config.php';
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Moderate All Assignments";
$user = get_session_user();

// --- Placeholder Data ---
// $sql = "SELECT a.id, a.title, a.due_date, s.subject_name, CONCAT(u.first_name, ' ', u.last_name) AS teacher_name,
//         (SELECT COUNT(*) FROM Submissions sub WHERE sub.assignment_id = a.id) AS submission_count
//         FROM Assignments a
//         JOIN Subjects s ON a.subject_id = s.id
//         JOIN Users u ON a.created_by = u.id
//         ORDER BY a.due_date DESC";
// $assignments = $pdo->query($sql)->fetchAll();
$assignments = [
    ['id' => 3, 'title' => 'Analysis Report', 'due_date' => '2025-10-30 23:59:00', 'subject_name' => 'Data Science', 'teacher_name' => 'Dr. Alan Smith', 'submission_count' => 1],
    ['id' => 1, 'title' => 'Project Phase 1', 'due_date' => '2025-10-28 23:59:00', 'subject_name' => 'Web Development', 'teacher_name' => 'Dr. Alan Smith', 'submission_count' => 1],
    ['id' => 2, 'title' => 'HTML/CSS Homepage', 'due_date' => '2025-10-15 23:59:00', 'subject_name' => 'Web Development', 'teacher_name' => 'Dr. Alan Smith', 'submission_count' => 12],
];
// --- End Placeholder Data ---

require_once '../../../includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0">Content Management: All Assignments</h4>
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
                        <th>Title</th>
                        <th>Subject</th>
                        <th>Created By</th>
                        <th>Due Date</th>
                        <th>Submissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($assignments)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No assignments found in the system.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($assignments as $assignment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['teacher_name']); ?></td>
                                <td><?php echo format_date($assignment['due_date']); ?></td>
                                <td><?php echo $assignment['submission_count']; ?></td>
                                <td class="actions-cell">
                                    <a href="../teacher/view-submissions.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-outline-primary" title="View Submissions">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"
                                            data-bs-toggle="modal" data-bs-target="#confirmModal" 
                                            data-title="Delete Assignment" 
                                            data-body="Are you sure you want to delete this assignment: <?php echo htmlspecialchars($assignment['title']); ?>?"
                                            data-confirm-url="#"> <i class="fas fa-trash"></i>
                                    </button>
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
// Add the JS to make the confirmation modal dynamic
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var confirmModal = document.getElementById('confirmModal');
    if(confirmModal) {
        confirmModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var title = button.getAttribute('data-title');
            var body = button.getAttribute('data-body');
            var url = button.getAttribute('data-confirm-url');
            
            confirmModal.querySelector('.modal-title').textContent = title;
            confirmModal.querySelector('.modal-body').textContent = body;
            confirmModal.querySelector('#confirmModalButton').href = url;
        });
    }
});
</script>

<?php
require_once '../../../includes/footer.php';
?>