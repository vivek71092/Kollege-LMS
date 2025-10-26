<?php
// /dashboard/admin/settings/backup.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available (for header/footer includes)
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Database Backup";
$user = get_session_user();

// --- No POST processing needed for placeholder ---

// Pass correct path prefix to header/footer
$path_prefix = '../../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0"><?php echo $page_title; ?></h4>
            </div>
            <div class="card-body">

                <?php
                // Display any relevant messages if needed
                display_flash_message('success_message', 'alert-success');
                display_flash_message('error_message', 'alert-danger');
                display_flash_message('info_message', 'alert-info');
                ?>

                <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">Feature Coming Soon! <i class="fas fa-wrench ms-2"></i></h4>
                    <p>The automated database backup generation feature is currently under development.</p>
                    <hr>
                    <p class="mb-0">In the meantime, please use your hosting control panel (like phpMyAdmin) to manually export the database (`<?php echo DB_NAME; ?>`) for backups.</p>
                </div>

                <a href="../dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                </a>

            </div> </div> </div> </div> <?php
require_once $path_prefix . 'includes/footer.php';
?>