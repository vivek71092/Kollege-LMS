<?php
// /dashboard/admin/attendance-marks/view-marks.php

// Load core files
require_once '../../../config.php';
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "View All Marks";
$user = get_session_user();

// --- Placeholder Data ---
// $sql = "SELECT m.id, m.total_marks, m.grade, CONCAT(u.first_name, ' ', u.last_name) AS student_name, s.subject_name, CONCAT(t.first_name, ' ', t.last_name) AS teacher_name
//         FROM Marks m
//         JOIN Users u ON m.student_id = u.id
//         JOIN Users t ON m.teacher_id = t.id
//         JOIN Subjects s ON m.subject_id = s.id
//         ORDER BY s.subject_name, u.last_name";
// $marks_records = $pdo->query($sql)->fetchAll();
$marks_records = [
    ['id' => 1, 'total_marks' => 75, 'grade' => 'A-', 'student_name' => 'Alice Smith', 'subject_name' => 'Web Development', 'teacher_name' => 'Dr. Alan Smith'],
    ['id' => 2, 'total_marks' => 65, 'grade' => 'B', 'student_name' => 'Bob Johnson', 'subject_name' => 'Web Development', 'teacher_name' => 'Dr. Alan Smith'],
    ['id' => 3, 'total_marks' => 80, 'grade' => 'A', 'student_name' => 'Charlie Brown', 'subject_name' => 'Data Science', 'teacher_name' => 'Dr. Alan Smith'],
];
// --- End Placeholder Data ---

require_once '../../../includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0">System-Wide Marks Records</h4>
    </div>
    <div class="card-body">
        
        <div class="table-responsive">
            <table class="table table-hover align-middle data-table">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Subject</th>
                        <th>Total Marks</th>
                        <th>Grade</th>
                        <th>Entered By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($marks_records)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No marks records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($marks_records as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($record['subject_name']); ?></td>
                                <td><strong><?php echo htmlspecialchars($record['total_marks']); ?></strong></td>
                                <td><span class="badge bg-primary fs-6"><?php echo htmlspecialchars($record['grade']); ?></span></td>
                                <td><?php echo htmlspecialchars($record['teacher_name']); ?></td>
                                <td class="actions-cell">
                                    <a href="../teacher/manage-marks.php?subject_id=..." class="btn btn-sm btn-outline-primary" title="Edit Record">
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