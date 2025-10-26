<?php
// /dashboard/admin/content/manage-notes.php

// Load core files
require_once '../../../config.php';
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Moderate All Notes";
$user = get_session_user();

// --- Placeholder Data ---
// $sql = "SELECT n.id, n.title, n.upload_date, s.subject_name, CONCAT(u.first_name, ' ', u.last_name) AS teacher_name, n.file_path
//         FROM Notes n
//         JOIN Subjects s ON n.subject_id = s.id
//         JOIN Users u ON n.uploaded_by = u.id
//         ORDER BY n.upload_date DESC";
// $notes = $pdo->query($sql)->fetchAll();
$notes = [
    ['id' => 3, 'title' => 'Module 1: Data Basics', 'upload_date' => '2025-10-02 14:00:00', 'subject_name' => 'Data Science', 'teacher_name' => 'Dr. Alan Smith', 'file_path' => 'public/uploads/notes/data-basics.pdf'],
    ['id' => 2, 'title' => 'Lecture 2: CSS Fundamentals', 'upload_date' => '2025-10-03 10:00:00', 'subject_name' => 'Web Development', 'teacher_name' => 'Dr. Alan Smith', 'file_path' => 'public/uploads/notes/lecture2.pdf'],
    ['id' => 1, 'title' => 'Lecture 1: Intro to HTML', 'upload_date' => '2025-10-01 10:00:00', 'subject_name' => 'Web Development', 'teacher_name' => 'Dr. Alan Smith', 'file_path' => 'public/uploads/notes/lecture1.pdf'],
];
// --- End Placeholder Data ---

require_once '../../../includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0">Content Management: All Notes</h4>
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
                        <th>Uploaded By</th>
                        <th>Uploaded On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($notes)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No notes found in the system.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($notes as $note): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($note['title']); ?></td>
                                <td><?php echo htmlspecialchars($note['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($note['teacher_name']); ?></td>
                                <td><?php echo format_date($note['upload_date']); ?></td>
                                <td class="actions-cell">
                                    <a href="<?php echo BASE_URL . $note['file_path']; ?>" class="btn btn-sm btn-outline-success" target="_blank" download title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"
                                            data-bs-toggle="modal" data-bs-target="#confirmModal" 
                                            data-title="Delete Note" 
                                            data-body="Are you sure you want to delete this note: <?php echo htmlspecialchars($note['title']); ?>?"
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