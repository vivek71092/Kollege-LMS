<?php
// /dashboard/admin/dashboard.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Admin Dashboard";
$user = get_session_user();

// --- Fetch Actual Data ---
try {
    // Correct query for student count
    $student_count_stmt = $pdo->query("SELECT COUNT(*) FROM Users WHERE role = 'student'");
    $student_count = $student_count_stmt->fetchColumn();

    // Query for teacher count
    $teacher_count_stmt = $pdo->query("SELECT COUNT(*) FROM Users WHERE role = 'teacher'");
    $teacher_count = $teacher_count_stmt->fetchColumn();

    // Query for course (program) count
    $course_count_stmt = $pdo->query("SELECT COUNT(*) FROM Courses");
    $course_count = $course_count_stmt->fetchColumn();

    // Query for subject count
    $subject_count_stmt = $pdo->query("SELECT COUNT(*) FROM Subjects");
    $subject_count = $subject_count_stmt->fetchColumn();

} catch (PDOException $e) {
    // Log error and set defaults to avoid breaking the page
    log_error("Error fetching dashboard stats: " . $e->getMessage(), __FILE__, __LINE__);
    $student_count = 0;
    $teacher_count = 0;
    $course_count = 0;
    $subject_count = 0;
    // Optionally set an error message for the admin
    $_SESSION['error_message'] = "Could not fetch dashboard statistics.";
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
                    <h5>Total Students</h5>
                    <div class="stat-number"><?php echo $student_count; ?></div>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card border-success">
            <div class="card-body">
                <div class="stat-card-info">
                    <h5>Total Teachers</h5>
                    <div class="stat-number"><?php echo $teacher_count; ?></div>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card border-info">
            <div class="card-body">
                <div class="stat-card-info">
                    <h5>Total Courses</h5>
                    <div class="stat-number"><?php echo $course_count; ?></div>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-book-medical"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card border-warning">
            <div class="card-body">
                <div class="stat-card-info">
                    <h5>Total Subjects</h5>
                    <div class="stat-number"><?php echo $subject_count; ?></div>
                </div>
                <div class="stat-card-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">User Enrollment Analytics (Placeholder Chart)</h5>
            </div>
            <div class="card-body">
                <canvas id="enrollmentChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">User Roles (Placeholder Chart)</h5>
            </div>
            <div class="card-body">
                <canvas id="userRolesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<?php
// We can pass script paths to footer.php if needed,
// but admin.js is already loaded automatically for admin pages.
require_once $path_prefix . 'includes/footer.php';
?>