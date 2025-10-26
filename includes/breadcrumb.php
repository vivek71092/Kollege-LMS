<?php
// /includes/breadcrumb.php

// Ensure $user is available. Assumes functions.php loaded before header.php includes this.
if (!isset($user)) {
    $user = get_session_user();
}
// Provide defaults if user data isn't fully set in session (defensive coding)
$user_role = $user['role'] ?? 'guest';
$user_name = $user['first_name'] ?? 'User';
$user_email = $user['email'] ?? 'user@example.com';

// Ensure BASE_URL is available (should be defined in config.php)
if (!defined('BASE_URL')) {
    define('BASE_URL', '/'); // Basic fallback, should not happen in production
    // Consider logging an error here if BASE_URL is missing
}

?>
<nav class="breadcrumb-header d-flex justify-content-between align-items-center mb-4" aria-label="breadcrumb">

    <div class="page-title">
        <h1 class="h3 mb-0"><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Dashboard'; ?></h1>
        </div>

    <div class="user-menu dropdown">
        <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle fa-lg me-2 text-secondary"></i>
            <span class="d-none d-sm-inline">
                <?php echo htmlspecialchars($user_name); ?>
            </span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown">
            <li>
                <h6 class="dropdown-header small text-muted">
                    Signed in as:<br><strong><?php echo htmlspecialchars($user_email); ?></strong>
                </h6>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item d-flex align-items-center" href="<?php echo BASE_URL . 'dashboard/' . htmlspecialchars($user_role) . '/profile.php'; ?>">
                    <i class="fas fa-user-edit fa-fw me-2"></i> My Profile
                </a>
            </li>
            <li>
                 <a class="dropdown-item d-flex align-items-center" href="<?php echo BASE_URL . 'dashboard/' . htmlspecialchars($user_role) . '/settings.php'; ?>">
                    <i class="fas fa-cog fa-fw me-2"></i> Account Settings
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-fw me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav>