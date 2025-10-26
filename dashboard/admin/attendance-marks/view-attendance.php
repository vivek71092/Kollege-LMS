<?php
// /dashboard/admin/attendance-marks/view-attendance.php

// Load core files
require_once '../../../config.php';
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "View All Attendance";
$user = get_session_user();

// --- Placeholder Data ---
// $sql = "SELECT a.id, a.date, a.status, CONCAT(u.first_name, ' ', u.last_name) AS student_name, s.subject_name, CONCAT(t.first_name, ' ', t.last_name) AS teacher_name
//         FROM Attendance a
//         JOIN Users u ON a.student_id = u.id
//         JOIN Users t ON a.teacher_id = t.id
//         JOIN Subjects s ON a.subject_id = s.id
//         ORDER BY a.date DESC";
// $attendance_records = $pdo->query($sql)->fetchAll();
$attendance_records = [
    ['id' => 1, 'date' => '2025-10-06', 'status' => 'absent', 'student_name' => 'Alice Smith', 'subject_name' => 'Web Development', 'teacher_name' => 'Dr. Alan Smith'],
    ['id' => 2, 'date' => '2025-10-04', 'status' => 'present', 'student_name' => 'Bob Johnson', 'subject_name' => 'Data Science', 'teacher_name' => 'Dr. Alan Smith'],
    ['id' => 3, 'date' => '2025-10-03', 'status' => 'present', 'student_name' => 'Alice Smith', 'subject_name' => 'Web Development', 'teacher_name' => 'Dr. Alan Smith'],
];
// --- End Placeholder Data ---

require_once '../../../includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0">System-Wide Attendance Records</h4>
    </div>
    <div class="card-body">
        
        <div class="table-responsive">
            <table class="table table-hover align-middle data-table">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Marked By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($attendance_records)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No attendance records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($attendance_records as $record): ?>
                            <tr>
                                <td><?php echo format_date($record['date'], 'M d, Y'); ?></td>
                                <td><?php echo htmlspecialchars($record['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($record['subject_name']); ?></td>
                                <td>
                                    <?php if ($record['status'] == 'present'): ?>
                                        <span class="badge bg-success">Present</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Absent</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($record['teacher_name']); ?></td>
                                <td class="actions-cell">
                                    <a href="#" class="btn btn-sm btn-outline-primary" title="Edit Record">
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