<?php
// /includes/header.php

// We assume config.php and functions.php have been included by the calling script
// (e.g., index.php, dashboard/student/dashboard.php)

// Check if we are in a dashboard page
$is_dashboard = (strpos($_SERVER['SCRIPT_NAME'], '/dashboard/') !== false);

// Determine relative path depth for assets (crucial without base tag)
$path_prefix = $is_dashboard ? '../../' : ''; // Adjust if nesting goes deeper than /dashboard/role/
if (strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/users/') !== false ||
    strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/courses/') !== false ||
    strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/subjects/') !== false ||
    strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/content/') !== false ||
    strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/attendance-marks/') !== false ||
    strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/announcements/') !== false ||
    strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/reports/') !== false ||
    strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/settings/') !== false ||
    strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/analytics/') !== false) {
    $path_prefix = '../../../';
} elseif (strpos($_SERVER['SCRIPT_NAME'], '/dashboard/student/') !== false ||
           strpos($_SERVER['SCRIPT_NAME'], '/dashboard/teacher/') !== false ||
           strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/') !== false) { // For admin pages directly under /admin/
    $path_prefix = '../../';
} elseif (strpos($_SERVER['SCRIPT_NAME'], '/pages/') !== false ||
           strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) {
    $path_prefix = '../';
} else {
    $path_prefix = ''; // Root directory (index.php)
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' . SITE_NAME : SITE_NAME; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <link rel="stylesheet" href="<?php echo $path_prefix; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo $path_prefix; ?>public/css/responsive.css">

    <?php if ($is_dashboard): ?>
        <link rel="stylesheet" href="<?php echo $path_prefix; ?>public/css/dashboard.css">
        <?php if (strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/') !== false): ?>
            <link rel="stylesheet" href="<?php echo $path_prefix; ?>public/css/admin.css">
        <?php endif; ?>
    <?php endif; ?>

</head>
<body class="<?php echo $is_dashboard ? 'dashboard' : 'public-site'; ?>">

<?php
// Determine include path depth
$include_prefix = $path_prefix; // Same logic as asset paths
?>

<?php if ($is_dashboard): ?>

    <?php require_once $include_prefix . 'includes/sidebar.php'; // Include the dashboard sidebar ?>

    <main class="dashboard-main-content">

        <?php require_once $include_prefix . 'includes/breadcrumb.php'; // Include the top bar/breadcrumb ?>

        <div class="container-fluid">
            <?php
            // Assuming display_flash_message is globally available via functions.php
            display_flash_message('success_message', 'alert-success');
            display_flash_message('error_message', 'alert-danger');
            display_flash_message('info_message', 'alert-info');
            ?>
        </div>

<?php else: ?>

    <?php require_once $include_prefix . 'includes/navbar.php'; // Include the public navigation bar ?>

    <div class="container" style="padding-top: 1rem;">
        <?php
        display_flash_message('success_message', 'alert-success');
        display_flash_message('error_message', 'alert-danger');
        display_flash_message('info_message', 'alert-info');
        ?>
    </div>

<?php endif; ?>