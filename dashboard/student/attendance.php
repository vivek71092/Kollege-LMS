<?php
// /dashboard/student/attendance.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['student']);

$page_title = "My Attendance";
$user = get_session_user();
$student_id = $user['id'];

// --- Fetch Attendance Data ---
$all_attendance = []; // Initialize
$fetch_error = null;
try {
    // Query attendance records for the student, joining Subjects for the name
    $sql = "SELECT a.date, a.status, a.remarks, s.subject_name
            FROM Attendance a
            JOIN Subjects s ON a.subject_id = s.id
            WHERE a.student_id = ?
            ORDER BY s.subject_name, a.date DESC"; // Order by subject, then date

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$student_id]);
    $all_attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by subject and calculate stats
    $attendance_by_subject = [];
    foreach ($all_attendance as $record) {
        $subject = $record['subject_name'];
        if (!isset($attendance_by_subject[$subject])) {
            $attendance_by_subject[$subject] = ['present' => 0, 'absent' => 0, 'total' => 0, 'records' => []];
        }
        $attendance_by_subject[$subject]['records'][] = $record;
        $attendance_by_subject[$subject]['total']++;
        if ($record['status'] == 'present') {
            $attendance_by_subject[$subject]['present']++;
        } else {
            $attendance_by_subject[$subject]['absent']++;
        }
    }

} catch (PDOException $e) {
    log_error("Error fetching attendance for student ID {$student_id}: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not fetch your attendance records from the database.";
    $_SESSION['error_message'] = $fetch_error;
    $attendance_by_subject = []; // Ensure empty on error
}
// --- End Data Fetching ---

// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0"><?php echo $page_title; ?> Record</h4>
    </div>
    <div class="card-body">

        <?php
        display_flash_message('success_message', 'alert-success');
        display_flash_message('error_message', 'alert-danger');
        ?>

        <?php if ($fetch_error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($fetch_error); ?></div>
        <?php endif; ?>

        <?php if (empty($attendance_by_subject) && !$fetch_error): ?>
            <div class="alert alert-info">Your attendance has not been marked for any subject yet.</div>
        <?php elseif (!empty($attendance_by_subject)): ?>
            <div class="accordion" id="attendanceAccordion">
                <?php $i = 0; foreach ($attendance_by_subject as $subject_name => $data): $i++; ?>
                    <?php
                    // Calculate percentage
                    $percentage = ($data['total'] > 0) ? round(($data['present'] / $data['total']) * 100) : 0;
                    // Determine badge color based on percentage
                    $badge_class = 'bg-secondary'; // Default
                    if ($percentage >= 85) $badge_class = 'bg-success';
                    elseif ($percentage >= 70) $badge_class = 'bg-warning text-dark';
                    elseif ($data['total'] > 0) $badge_class = 'bg-danger'; // Only show danger if records exist
                    ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-<?php echo $i; ?>">
                            <button class="accordion-button <?php if($i > 1) echo 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $i; ?>" aria-expanded="<?php echo $i == 1 ? 'true' : 'false'; ?>" aria-controls="collapse-<?php echo $i; ?>">
                                <span class="me-auto fw-bold"><?php echo htmlspecialchars($subject_name); ?></span>
                                <span class="badge <?php echo $badge_class; ?> me-3"><?php echo $percentage; ?>%</span>
                            </button>
                        </h2>
                        <div id="collapse-<?php echo $i; ?>" class="accordion-collapse collapse <?php if($i == 1) echo 'show'; ?>" aria-labelledby="heading-<?php echo $i; ?>">
                            <div class="accordion-body">
                                <p class="mb-2">
                                    <strong>Summary:</strong>
                                    Total Classes Recorded: <?php echo $data['total']; ?> |
                                    Present: <?php echo $data['present']; ?> |
                                    Absent: <?php echo $data['absent']; ?>
                                </p>
                                <?php if (!empty($data['records'])): ?>
                                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                        <table class="table table-striped table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data['records'] as $record): ?>
                                                    <tr>
                                                        <td><?php echo format_date($record['date'], 'M d, Y'); ?></td>
                                                        <td>
                                                            <?php if ($record['status'] == 'present'): ?>
                                                                <span class="badge bg-success">Present</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger">Absent</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($record['remarks'] ?? ''); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">No specific records found for this subject.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div> <?php endif; ?>
    </div> </div> <?php
require_once $path_prefix . 'includes/footer.php';
?>