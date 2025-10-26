<?php
// /dashboard/student/dashboard.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['student']);

$page_title = "Student Dashboard";
$user = get_session_user(); // Get logged-in student info
$student_id = $user['id'];

// --- Fetch Actual Data ---
$course_count = 0;
$pending_assignment_count = 0;
$unread_message_count = 0;
$upcoming_assignments = [];
$recent_announcements = []; // Announcements are typically system-wide
$attendance_percentage = 'N/A'; // Default value
$fetch_error = null;

try {
    // 1. Get count of enrolled subjects
    $stmt_courses = $pdo->prepare("SELECT COUNT(*) FROM Enrollments WHERE student_id = ? AND status = 'enrolled'");
    $stmt_courses->execute([$student_id]);
    $course_count = $stmt_courses->fetchColumn();

    // 2. Get count of pending assignments (not submitted or overdue)
    $stmt_pending_assign = $pdo->prepare(
       "SELECT COUNT(a.id)
        FROM Assignments a
        JOIN Subjects s ON a.subject_id = s.id
        JOIN Enrollments e ON s.id = e.subject_id
        LEFT JOIN Submissions sub ON a.id = sub.assignment_id AND sub.student_id = e.student_id
        WHERE e.student_id = ? AND e.status = 'enrolled' AND a.due_date >= CURDATE() AND sub.id IS NULL"
    );
    $stmt_pending_assign->execute([$student_id]);
    $pending_assignment_count = $stmt_pending_assign->fetchColumn();


    // 3. Get count of unread messages
    $stmt_messages = $pdo->prepare("SELECT COUNT(*) FROM Messages WHERE receiver_id = ? AND read_status = 0");
    $stmt_messages->execute([$student_id]);
    $unread_message_count = $stmt_messages->fetchColumn();

    // 4. Get upcoming assignments (limit 3)
    $stmt_upcoming = $pdo->prepare(
       "SELECT a.id, a.title, a.due_date, s.subject_name
        FROM Assignments a
        JOIN Subjects s ON a.subject_id = s.id
        JOIN Enrollments e ON s.id = e.subject_id
        LEFT JOIN Submissions sub ON a.id = sub.assignment_id AND sub.student_id = e.student_id
        WHERE e.student_id = ? AND e.status = 'enrolled' AND a.due_date >= NOW() AND sub.id IS NULL
        ORDER BY a.due_date ASC
        LIMIT 3"
    );
    $stmt_upcoming->execute([$student_id]);
    $upcoming_assignments = $stmt_upcoming->fetchAll(PDO::FETCH_ASSOC);

    // 5. Get recent system-wide announcements (limit 3)
    $stmt_announcements = $pdo->query(
        "SELECT id, title, description, created_date
         FROM Announcements
         WHERE status = 'published'
         ORDER BY priority DESC, created_date DESC
         LIMIT 3"
    );
    $recent_announcements = $stmt_announcements->fetchAll(PDO::FETCH_ASSOC);

    // 6. Calculate overall attendance percentage (simplified example)
    $stmt_attendance = $pdo->prepare(
        "SELECT COUNT(*) as total, SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present
         FROM Attendance
         WHERE student_id = ?"
    );
    $stmt_attendance->execute([$student_id]);
    $att_stats = $stmt_attendance->fetch(PDO::FETCH_ASSOC);
    if ($att_stats && $att_stats['total'] > 0) {
        $attendance_percentage = round(($att_stats['present'] / $att_stats['total']) * 100) . '%';
    }


} catch (PDOException $e) {
    log_error("Error fetching student dashboard data for student ID {$student_id}: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not load dashboard data from the database.";
    $_SESSION['error_message'] = $fetch_error; // Show error on page load
    // Keep defaults
}
// --- End Data Fetching ---

// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card border-primary">
            <div class="card-body">
                <div class="stat-card-info">
                    <h5>Enrolled Subjects</h5>
                    <div class="stat-number"><?php echo $course_count; ?></div>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-book-open"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card border-warning">
            <div class="card-body">
                <div class="stat-card-info">
                    <h5>Pending Assignments</h5>
                    <div class="stat-number"><?php echo $pending_assignment_count; ?></div>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card border-success">
            <div class="card-body">
                <div class="stat-card-info">
                    <h5>Overall Attendance</h5>
                    <div class="stat-number"><?php echo $attendance_percentage; ?></div>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card border-info">
            <div class="card-body">
                <div class="stat-card-info">
                    <h5>Unread Messages</h5>
                    <div class="stat-number"><?php echo $unread_message_count; ?></div>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-envelope"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($fetch_error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($fetch_error); ?></div>
<?php endif; ?>

<div class="row">

    <div class="col-lg-7 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i> Upcoming Due Dates</h5>
            </div>
            <div class="card-body">
                 <?php if (empty($upcoming_assignments)): ?>
                    <div class="text-center text-muted py-3">No upcoming assignments due.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($upcoming_assignments as $assignment): ?>
                            <a href="view-assignment.php?id=<?php echo $assignment['id']; ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($assignment['title']); ?></h6>
                                    <small class="text-danger">Due: <?php echo format_date($assignment['due_date'], 'M d, Y h:i A'); ?></small>
                                </div>
                                <p class="mb-1 text-muted small"><?php echo htmlspecialchars($assignment['subject_name']); ?></p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center bg-light">
                <a href="assignments.php" class="btn btn-outline-primary btn-sm">View All Assignments</a>
            </div>
        </div>
    </div>

    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i> Recent Announcements</h5>
            </div>
            <div class="card-body">
                 <?php if (empty($recent_announcements)): ?>
                     <div class="text-center text-muted py-3">No recent announcements.</div>
                 <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($recent_announcements as $item): ?>
                            <li class="list-group-item">
                                <h6 class="mb-1"><?php echo htmlspecialchars($item['title']); ?></h6>
                                <p class="mb-1 small text-muted"><?php echo truncate_text(htmlspecialchars($item['description']), 100); ?></p>
                                <small class="text-muted"><?php echo format_date($item['created_date'], 'M d, Y'); ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                 <?php endif; ?>
            </div>
            <div class="card-footer text-center bg-light">
                <a href="<?php echo BASE_URL; ?>pages/announcements.php" class="btn btn-outline-primary btn-sm">View All Announcements</a>
            </div>
        </div>
    </div>

</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>