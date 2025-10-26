<?php
// /dashboard/teacher/dashboard.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['teacher']);

$page_title = "Teacher Dashboard";
$user = get_session_user(); // Get logged-in teacher info
$teacher_id = $user['id'];

// --- Fetch Actual Data ---
$course_count = 0;
$student_count = 0;
$new_submission_count = 0;
$new_submissions = [];
$schedule_today = [];
$fetch_error = null;

try {
    // 1. Get count of distinct subjects assigned to the teacher
    $stmt_courses = $pdo->prepare(
        "SELECT COUNT(DISTINCT s.id) 
         FROM Subjects s 
         JOIN Courses c ON s.course_id = c.id 
         WHERE c.teacher_id = ?"
    );
    $stmt_courses->execute([$teacher_id]);
    $course_count = $stmt_courses->fetchColumn();

    // 2. Get count of unique students enrolled in the teacher's subjects
    $stmt_students = $pdo->prepare(
        "SELECT COUNT(DISTINCT e.student_id) 
         FROM Enrollments e 
         JOIN Subjects s ON e.subject_id = s.id 
         JOIN Courses c ON s.course_id = c.id 
         WHERE c.teacher_id = ?"
    );
    $stmt_students->execute([$teacher_id]);
    $student_count = $stmt_students->fetchColumn();

    // 3. Get recent new submissions for the teacher's assignments
    $stmt_submissions = $pdo->prepare(
       "SELECT sub.id, u.first_name, u.last_name, a.title, s.subject_name 
        FROM Submissions sub 
        JOIN Users u ON sub.student_id = u.id 
        JOIN Assignments a ON sub.assignment_id = a.id 
        JOIN Subjects s ON a.subject_id = s.id
        WHERE a.created_by = ? AND sub.status = 'submitted' 
        ORDER BY sub.submission_date DESC 
        LIMIT 5" // Limit to recent 5
    );
    $stmt_submissions->execute([$teacher_id]);
    $new_submissions = $stmt_submissions->fetchAll(PDO::FETCH_ASSOC);
    $new_submission_count = count($new_submissions); // Count only the fetched ones for the card

    // 4. Get today's schedule for the teacher
    // Note: ClassSchedule might link directly to teacher_id OR via Subject->Course->teacher_id
    // This query assumes ClassSchedule.teacher_id is the primary link. Adjust if needed.
    $today = date('l'); // Get current day name, e.g., 'Saturday'
    $stmt_schedule = $pdo->prepare(
        "SELECT cs.start_time, cs.end_time, s.subject_name, cs.classroom 
         FROM ClassSchedule cs 
         JOIN Subjects s ON cs.subject_id = s.id 
         WHERE cs.teacher_id = ? AND cs.day_of_week = ? 
         ORDER BY cs.start_time ASC"
    );
    $stmt_schedule->execute([$teacher_id, $today]);
    $schedule_today = $stmt_schedule->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    log_error("Error fetching teacher dashboard data for teacher ID {$teacher_id}: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not load dashboard data from the database.";
    $_SESSION['error_message'] = $fetch_error; // Show error on page load
    // Keep counts/arrays initialized to prevent errors in HTML
    $course_count = 0;
    $student_count = 0;
    $new_submission_count = 0;
    $new_submissions = [];
    $schedule_today = [];
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
                    <h5>My Subjects</h5>
                    <div class="stat-number"><?php echo $course_count; ?></div>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card border-success">
            <div class="card-body">
                <div class="stat-card-info">
                    <h5>Total Students</h5>
                    <div class="stat-number"><?php echo $student_count; ?></div>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card border-warning">
            <div class="card-body">
                <div class="stat-card-info">
                    <h5>New Submissions</h5>
                    <div class="stat-number"><?php echo $new_submission_count; ?></div>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-inbox"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card border-info">
            <div class="card-body">
                <div class="stat-card-info">
                    <h5>Classes Today</h5>
                    <div class="stat-number"><?php echo count($schedule_today); ?></div>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($fetch_error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($fetch_error); ?></div>
<?php endif; ?>

<div class="row">

    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-inbox me-2"></i> New Submissions to Grade (Recent 5)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($new_submissions)): ?>
                    <div class="text-center text-muted py-3">No new submissions requiring grading.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($new_submissions as $sub): ?>
                            <a href="grade-assignment.php?id=<?php echo $sub['id']; ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($sub['first_name'] . ' ' . $sub['last_name']); ?></h6>
                                    <small class="text-warning">Needs Grading</small>
                                </div>
                                <p class="mb-1 text-muted small">
                                    <?php echo htmlspecialchars($sub['title']); ?> (<?php echo htmlspecialchars($sub['subject_name']); ?>)
                                </p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center bg-light">
                <a href="manage-assignments.php" class="btn btn-outline-primary btn-sm">View All Assignments</a>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-day me-2"></i> Today's Schedule (<?php echo date('l, M d'); ?>)</h5>
            </div>
            <div class="card-body">
                 <?php if (empty($schedule_today)): ?>
                    <div class="text-center text-muted py-3">No classes scheduled for today.</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($schedule_today as $class):
                            // Format times
                            $start_time_formatted = format_date('1970-01-01 ' . $class['start_time'], 'h:i A');
                            $end_time_formatted = format_date('1970-01-01 ' . $class['end_time'], 'h:i A');
                        ?>
                            <li class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <strong><?php echo "$start_time_formatted - $end_time_formatted"; ?></strong>
                                    <span class="text-muted"><?php echo htmlspecialchars($class['classroom'] ?? 'N/A'); ?></span>
                                </div>
                                <div><?php echo htmlspecialchars($class['subject_name']); ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center bg-light">
                <a href="schedule.php" class="btn btn-outline-primary btn-sm">View Full Schedule</a>
            </div>
        </div>
    </div>

</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>