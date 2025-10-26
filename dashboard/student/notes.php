<?php
// /dashboard/student/notes.php

// Load core files
require_once '../../config.php';
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['student']);

$page_title = "My Notes";
$user = get_session_user();
$student_id = $user['id'];

// --- Placeholder Data ---
// $notes_sql = "SELECT n.id, n.title, n.description, n.file_path, n.upload_date, s.subject_name
//               FROM Notes n
//               JOIN Subjects s ON n.subject_id = s.id
//               JOIN Enrollments e ON s.id = e.subject_id
//               WHERE e.student_id = ?
//               ORDER BY s.subject_name, n.upload_date DESC";
// $stmt = $pdo->prepare($notes_sql);
// $stmt->execute([$student_id]);
// $all_notes = $stmt->fetchAll();
$all_notes = [
    ['id' => 1, 'title' => 'Lecture 1: Intro to HTML', 'description' => 'Basic HTML tags.', 'file_path' => 'public/uploads/notes/lecture1.pdf', 'upload_date' => '2025-10-01 10:00:00', 'subject_name' => 'Web Development'],
    ['id' => 2, 'title' => 'Lecture 2: CSS Fundamentals', 'description' => 'Selectors, properties.', 'file_path' => 'public/uploads/notes/lecture2.pdf', 'upload_date' => '2025-10-03 10:00:00', 'subject_name' => 'Web Development'],
    ['id' => 3, 'title' => 'Module 1: Data Basics', 'description' => 'Types of data.', 'file_path' => 'public/uploads/notes/data-basics.pdf', 'upload_date' => '2025-10-02 14:00:00', 'subject_name' => 'Data Science'],
];

// Group notes by subject
$notes_by_subject = [];
foreach ($all_notes as $note) {
    $notes_by_subject[$note['subject_name']][] = $note;
}
// --- End Placeholder Data ---

require_once '../../includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0">All Lecture Notes</h4>
    </div>
    <div class="card-body">
        <?php if (empty($notes_by_subject)): ?>
            <div class="alert alert-info">No notes have been uploaded for your courses yet.</div>
        <?php else: ?>
            <div class="accordion" id="notesAccordion">
                <?php $i = 0; foreach ($notes_by_subject as $subject_name => $notes): $i++; ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-<?php echo $i; ?>">
                            <button class="accordion-button <?php if($i > 1) echo 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $i; ?>" aria-expanded="<?php echo $i == 1 ? 'true' : 'false'; ?>" aria-controls="collapse-<?php echo $i; ?>">
                                <?php echo htmlspecialchars($subject_name); ?> (<?php echo count($notes); ?>)
                            </button>
                        </h2>
                        <div id="collapse-<?php echo $i; ?>" class="accordion-collapse collapse <?php if($i == 1) echo 'show'; ?>" aria-labelledby="heading-<?php echo $i; ?>">
                            <div class="accordion-body">
                                <ul class="list-group">
                                    <?php foreach ($notes as $note): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center" id="note-<?php echo $note['id']; ?>">
                                            <div>
                                                <strong><?php echo htmlspecialchars($note['title']); ?></strong>
                                                <p class="mb-0 text-muted small"><?php echo htmlspecialchars($note['description']); ?></p>
                                                <small>Uploaded: <?php echo format_date($note['upload_date'], 'M d, Y'); ?></small>
                                            </div>
                                            <a href="<?php echo htmlspecialchars($note['file_path']); ?>" class="btn btn-primary btn-sm" target="_blank" download>
                                                <i class="fas fa-download me-2"></i> Download
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>