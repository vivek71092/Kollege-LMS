<?php
// /includes/sidebar.php

// Ensure functions.php (which contains get_session_user) is loaded *before* this file is included.
// This is typically done by the calling script (e.g., dashboard.php, list-users.php)
// which includes config.php -> functions.php -> header.php -> sidebar.php

$user = get_session_user(); // Fetch user data from session
$user_role = $user['role'] ?? 'guest'; // Determine role, default to 'guest' if not found

// Construct BASE_URL if not already defined (fallback - should be defined in config.php)
if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $script_dir_fallback = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    // Adjust depth based on common include locations if needed
    if (strpos($_SERVER['SCRIPT_NAME'], '/dashboard/') !== false) {
         $base_url_fallback = rtrim($protocol . $host . dirname(dirname($script_dir_fallback)), '/') . '/';
    } else {
         $base_url_fallback = rtrim($protocol . $host . dirname($script_dir_fallback), '/') . '/';
    }
    define('BASE_URL', $base_url_fallback);
    // It's better practice to ensure config.php defines BASE_URL correctly.
    // log_error("Warning: BASE_URL fallback used in sidebar.php.", __FILE__, __LINE__);
}
// Ensure SITE_NAME is defined (usually from config.php)
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Kollege LMS'); // Fallback site name
}

?>
<aside class="dashboard-sidebar">
    <div class="sidebar-brand">
        <a href="<?php echo BASE_URL; ?>dashboard/index.php"><?php echo htmlspecialchars(SITE_NAME); ?></a>
    </div>

    <ul class="sidebar-nav nav flex-column">

        <?php if ($user_role === 'student'): ?>
            <li class="nav-heading">Student Menu</li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/student/dashboard.php">
                    <i class="fas fa-tachometer-alt fa-fw me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/student/courses.php">
                    <i class="fas fa-book fa-fw me-2"></i> My Subjects
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/student/assignments.php">
                    <i class="fas fa-tasks fa-fw me-2"></i> Assignments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/student/marks.php">
                    <i class="fas fa-marker fa-fw me-2"></i> My Marks
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/student/attendance.php">
                    <i class="fas fa-calendar-check fa-fw me-2"></i> My Attendance
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/student/schedule.php">
                    <i class="fas fa-calendar-alt fa-fw me-2"></i> Class Schedule
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/student/messages.php">
                     <i class="fas fa-envelope fa-fw me-2"></i> Messages
                </a>
            </li>
            <li class="nav-heading">Account</li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/student/profile.php">
                    <i class="fas fa-user-circle fa-fw me-2"></i> My Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/student/settings.php">
                    <i class="fas fa-user-cog fa-fw me-2"></i> Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-fw me-2"></i> Logout
                </a>
            </li>
        <?php endif; ?>

        <?php if ($user_role === 'teacher'): ?>
            <li class="nav-heading">Teacher Menu</li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/teacher/dashboard.php">
                    <i class="fas fa-tachometer-alt fa-fw me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/teacher/courses.php">
                    <i class="fas fa-chalkboard-teacher fa-fw me-2"></i> My Subjects
                </a>
            </li>
             <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/teacher/manage-notes.php">
                    <i class="fas fa-book-open fa-fw me-2"></i> Manage Notes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/teacher/manage-assignments.php">
                    <i class="fas fa-edit fa-fw me-2"></i> Assignments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/teacher/manage-marks.php">
                    <i class="fas fa-percentage fa-fw me-2"></i> Manage Marks
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/teacher/mark-attendance.php">
                    <i class="fas fa-user-check fa-fw me-2"></i> Manage Attendance
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/teacher/students.php">
                    <i class="fas fa-users fa-fw me-2"></i> Enrolled Students
                </a>
            </li>
             <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/teacher/schedule.php">
                    <i class="fas fa-calendar-alt fa-fw me-2"></i> My Schedule
                </a>
            </li>
             <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/teacher/messages.php">
                     <i class="fas fa-envelope fa-fw me-2"></i> Messages
                </a>
            </li>
             <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/teacher/reports.php">
                    <i class="fas fa-file-alt fa-fw me-2"></i> Generate Reports
                </a>
            </li>
             <li class="nav-heading">Account</li>
             <li class="nav-item">
                 <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/teacher/profile.php">
                     <i class="fas fa-user-circle fa-fw me-2"></i> My Profile
                 </a>
             </li>
             <li class="nav-item">
                 <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/teacher/settings.php">
                     <i class="fas fa-user-cog fa-fw me-2"></i> Settings
                 </a>
             </li>
             <li class="nav-item">
                 <a class="nav-link text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                     <i class="fas fa-sign-out-alt fa-fw me-2"></i> Logout
                 </a>
             </li>
        <?php endif; ?>

        <?php if ($user_role === 'admin'): ?>
            <li class="nav-heading">Admin Menu</li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/admin/dashboard.php">
                    <i class="fas fa-chart-line fa-fw me-2"></i> Analytics
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/admin/users/list-users.php">
                    <i class="fas fa-users-cog fa-fw me-2"></i> User Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/admin/courses/list-courses.php">
                    <i class="fas fa-book-medical fa-fw me-2"></i> Course Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/admin/subjects/list-subjects.php">
                    <i class="fas fa-clipboard-list fa-fw me-2"></i> Subject Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/admin/announcements/list-announcements.php">
                    <i class="fas fa-bullhorn fa-fw me-2"></i> Announcements
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#reportsSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="reportsSubmenu">
                     <i class="fas fa-file-download fa-fw me-2"></i> Generate Reports
                </a>
                <ul class="collapse list-unstyled" id="reportsSubmenu">
                    <li><a class="nav-link sub-link" href="<?php echo BASE_URL; ?>dashboard/admin/reports/attendance-report.php"><i class="fas fa-calendar-check fa-fw me-2"></i> Attendance</a></li>
                    <li><a class="nav-link sub-link" href="<?php echo BASE_URL; ?>dashboard/admin/reports/marks-report.php"><i class="fas fa-marker fa-fw me-2"></i> Marks</a></li>
                    <li><a class="nav-link sub-link" href="<?php echo BASE_URL; ?>dashboard/admin/reports/enrollment-report.php"><i class="fas fa-user-plus fa-fw me-2"></i> Enrollment</a></li>
                    <li><a class="nav-link sub-link" href="<?php echo BASE_URL; ?>dashboard/admin/reports/user-report.php"><i class="fas fa-users fa-fw me-2"></i> User</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/admin/audit-logs.php">
                    <i class="fas fa-history fa-fw me-2"></i> Audit Logs
                </a>
            </li>
            <li class="nav-item">
                 <a class="nav-link collapsed" href="#settingsSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="settingsSubmenu">
                     <i class="fas fa-cogs fa-fw me-2"></i> System Settings
                </a>
                 <ul class="collapse list-unstyled" id="settingsSubmenu">
                    <li><a class="nav-link sub-link" href="<?php echo BASE_URL; ?>dashboard/admin/settings/system-settings.php"><i class="fas fa-sliders-h fa-fw me-2"></i> General</a></li>
                    <li><a class="nav-link sub-link" href="<?php echo BASE_URL; ?>dashboard/admin/settings/email-settings.php"><i class="fas fa-envelope-open-text fa-fw me-2"></i> Email</a></li>
                    <li><a class="nav-link sub-link" href="<?php echo BASE_URL; ?>dashboard/admin/settings/backup.php"><i class="fas fa-database fa-fw me-2"></i> Backup</a></li>
                </ul>
            </li>
             <li class="nav-heading">Account</li>
             <li class="nav-item">
                 <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/admin/profile.php">
                     <i class="fas fa-user-circle fa-fw me-2"></i> My Profile
                 </a>
             </li>
             <li class="nav-item">
                 <a class="nav-link" href="<?php echo BASE_URL; ?>dashboard/admin/settings.php">
                     <i class="fas fa-user-cog fa-fw me-2"></i> Settings
                 </a>
             </li>
             <li class="nav-item">
                 <a class="nav-link text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                     <i class="fas fa-sign-out-alt fa-fw me-2"></i> Logout
                 </a>
             </li>
        <?php endif; ?>

    </ul>

</aside>

<style>
    /* Add this only if not already defined elsewhere */
    .sidebar-nav .sub-link {
        padding-left: 2.5rem; /* Indent submenu items */
        font-size: 0.9em;
        color: rgba(255, 255, 255, 0.6); /* Lighter color for sub-items */
    }
    .sidebar-nav .sub-link:hover {
         color: rgba(255, 255, 255, 0.9);
    }
    .sidebar-nav .sub-link i {
         font-size: 0.85em;
         margin-right: 0.7rem;
         width: 18px; /* Align icons slightly */
    }
    .sidebar-nav .nav-link[data-bs-toggle="collapse"] {
        position: relative;
    }
    .sidebar-nav .nav-link[data-bs-toggle="collapse"]::after {
        content: '\f078'; /* FontAwesome down arrow */
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        transition: transform 0.2s ease-in-out;
        font-size: 0.8em;
    }
    .sidebar-nav .nav-link[data-bs-toggle="collapse"][aria-expanded="true"]::after {
         transform: translateY(-50%) rotate(-180deg);
    }
    .sidebar-nav .nav-link i.fa-fw {
        width: 20px;
    }
</style>