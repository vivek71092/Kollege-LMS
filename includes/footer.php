<?php
// /includes/footer.php

// Check again if we are in a dashboard page
$is_dashboard = (strpos($_SERVER['SCRIPT_NAME'], '/dashboard/') !== false);

// Determine relative path depth for assets/includes (crucial without base tag)
// This logic calculates how many '../' are needed to get back to the root
// based on the current script's directory depth.
if (strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/users/') !== false ||
    strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/courses/') !== false ||
    strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/subjects/') !== false ||
    strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/content/') !== false ||
    strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/attendance-marks/') !== false ||
    strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/announcements/') !== false ||
    strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/reports/') !== false ||
    strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/settings/') !== false ||
    strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/analytics/') !== false) {
    // Files deep inside admin (e.g., /dashboard/admin/users/add-user.php) need ../../../
    $path_prefix = '../../../';
} elseif (strpos($_SERVER['SCRIPT_NAME'], '/dashboard/student/') !== false ||
           strpos($_SERVER['SCRIPT_NAME'], '/dashboard/teacher/') !== false ||
           strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/') !== false) {
    // Files directly under role directories (e.g., /dashboard/student/dashboard.php) need ../../
    $path_prefix = '../../';
} elseif (strpos($_SERVER['SCRIPT_NAME'], '/pages/') !== false ||
           strpos($_SERVER['SCRIPT_NAME'], '/auth/') !== false) {
    // Files under /pages/ or /auth/ need ../
    $path_prefix = '../';
} else {
    // Files in the root directory (e.g., index.php) need no prefix
    $path_prefix = '';
}
// Use the same prefix for including files like modals.php
$include_prefix = $path_prefix;
?>

<?php if ($is_dashboard): ?>

    </main> <?php else: ?>

    <footer class="footer mt-auto py-3 bg-dark text-light">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5><?php echo SITE_NAME; ?></h5>
                    <p class="text-muted">Providing quality education through a modern, accessible learning management system.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a class="text-muted text-decoration-none" href="<?php echo BASE_URL; ?>pages/about.php">About Us</a></li>
                        <li><a class="text-muted text-decoration-none" href="<?php echo BASE_URL; ?>pages/contact.php">Contact</a></li>
                        <li><a class="text-muted text-decoration-none" href="<?php echo BASE_URL; ?>pages/privacy.php">Privacy Policy</a></li>
                        <li><a class="text-muted text-decoration-none" href="<?php echo BASE_URL; ?>pages/terms.php">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Contact Us</h5>
                    <address class="text-muted">
                        Email: <a class="text-muted" href="mailto:<?php echo ADMIN_EMAIL ?? 'info@kollege.ct.ws'; ?>"><?php echo ADMIN_EMAIL ?? 'info@kollege.ct.ws'; ?></a><br>
                        Phone: +1 234 567 890 (Placeholder)
                    </address>
                </div>
            </div>
            <div class="text-center text-muted border-top pt-3 mt-3">
                &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All Rights Reserved.
            </div>
        </div>
    </footer>

<?php endif; ?>

<?php
// Include site-wide modals using the calculated prefix
require_once $include_prefix . 'includes/modals.php';
?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<?php if ($is_dashboard): ?>
    <script src="<?php echo $path_prefix; ?>public/js/dashboard.js"></script>
    <?php if (strpos($_SERVER['SCRIPT_NAME'], '/dashboard/admin/') !== false): ?>
        <script src="<?php echo $path_prefix; ?>public/js/admin.js"></script>
    <?php endif; ?>
<?php else: ?>
    <script src="<?php echo $path_prefix; ?>public/js/main.js"></script>
<?php endif; ?>

<script src="<?php echo $path_prefix; ?>public/js/validation.js"></script>

<script src="<?php echo $path_prefix; ?>public/js/confirm-modal.js"></script>

<?php
if (isset($page_scripts) && is_array($page_scripts)) {
    foreach ($page_scripts as $script) {
        // Assume page_scripts are relative to the public/js/ directory or absolute URLs
        $script_path = (strpos($script, 'http') === 0 || strpos($script, '/') === 0) ? $script : $path_prefix . 'public/js/' . ltrim($script, '/');
        echo '<script src="' . htmlspecialchars($script_path) . '"></script>' . "\n";
    }
}
?>

</body>
</html>