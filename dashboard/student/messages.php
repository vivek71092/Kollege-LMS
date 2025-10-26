<?php
// /dashboard/student/messages.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['student']);

$page_title = "Messages";
$user = get_session_user();

// --- No data fetching needed for placeholder ---

// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="row">
    <div class="col-lg-10 offset-lg-1">
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
                    <h4 class="alert-heading">Feature Coming Soon! <i class="fas fa-comments ms-2"></i></h4>
                    <p>The integrated messaging system for communicating with your teachers is currently under development.</p>
                    <hr>
                    <p class="mb-0">This feature will allow direct communication within the LMS platform. Please use existing communication methods (like email) in the meantime if you need to contact your instructor.</p>
                </div>

                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                </a>

            </div> <!-- /card-body -->
        </div> <!-- /card -->
    </div> <!-- /col -->
</div> <!-- /row -->

<?php
require_once $path_prefix . 'includes/footer.php';
?>