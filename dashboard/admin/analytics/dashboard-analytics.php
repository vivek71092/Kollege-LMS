<?php
// /dashboard/admin/analytics/dashboard-analytics.php

// Load core files
require_once '../../../config.php';
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Analytics Dashboard";
$user = get_session_user();

// --- Placeholder Data ---
// $student_count = $pdo->query("SELECT COUNT(*) FROM Users WHERE role = 'student'")->fetchColumn();
$student_count = 125;
// $teacher_count = $pdo->query("SELECT COUNT(*) FROM Users WHERE role = 'teacher'")->fetchColumn();
$teacher_count = 15;
// $course_count = $pdo->query("SELECT COUNT(*) FROM Courses")->fetchColumn();
$course_count = 20;
// $subject_count = $pdo->query("SELECT COUNT(*) FROM Subjects")->fetchColumn();
$subject_count = 48;
// --- End Placeholder Data ---

require_once '../../../includes/header.php';
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
                <h5 class="mb-0">User Enrollment Analytics</h5>
            </div>
            <div class="card-body">
                <canvas id="enrollmentChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">User Roles</h5>
            </div>
            <div class="card-body">
                <canvas id="userRolesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/footer.php';
?>