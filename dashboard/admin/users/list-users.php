<?php
// /dashboard/admin/users/list-users.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Manage Users";
$user = get_session_user(); // Get the logged-in admin's info

// --- Fetch Actual User Data ---
try {
    // Select all necessary columns for display
    $sql = "SELECT id, first_name, last_name, email, role, status, created_at
            FROM Users
            ORDER BY created_at DESC"; // Fetch ALL users
    $stmt = $pdo->query($sql);
    // Fetch as associative arrays
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log the database error
    log_error("Error fetching users: " . $e->getMessage(), __FILE__, __LINE__);
    $users = []; // Set to empty array on error to prevent issues later
    $_SESSION['error_message'] = "Could not fetch user list from the database.";
}
// --- End Data Fetching ---

// Pass correct path prefix to header/footer for including files
$path_prefix = '../../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">User Management</h4>
        <a href="add-user.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Add New User
        </a>
    </div>
    <div class="card-body">

        <?php
        // Display any success or error messages from previous actions
        display_flash_message('success_message', 'alert-success');
        display_flash_message('error_message', 'alert-danger'); // Show potential fetch error too
        ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle data-table" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No users found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?php echo $u['id']; ?></td>
                                <td><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><span class="badge bg-primary"><?php echo ucfirst($u['role']); ?></span></td>
                                <td>
                                    <?php // Display status badge based on value
                                    $status_badge_class = 'bg-secondary'; // Default
                                    if ($u['status'] == 'active') {
                                        $status_badge_class = 'bg-success';
                                    } elseif ($u['status'] == 'pending') {
                                        $status_badge_class = 'bg-warning text-dark';
                                    } elseif ($u['status'] == 'suspended') {
                                        $status_badge_class = 'bg-danger';
                                    }
                                    ?>
                                    <span class="badge <?php echo $status_badge_class; ?>"><?php echo ucfirst($u['status']); ?></span>
                                </td>
                                <td><?php echo format_date($u['created_at'], 'M d, Y'); // Format the date ?></td>
                                <td class="actions-cell">
                                    <a href="edit-user.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php // Prevent admin from seeing delete button for themselves ?>
                                    <?php if ($u['id'] != $user['id']): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                                data-bs-toggle="modal"
                                                data-bs-target="#confirmModal"
                                                data-title="Delete User Confirmation"
                                                data-body="Are you absolutely sure you want to delete the user '<?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?>' (<?php echo htmlspecialchars($u['email']); ?>)? This action cannot be undone."
                                                data-confirm-url="delete-user.php?id=<?php echo $u['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                     <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// IMPORTANT: The JavaScript for the modal ('confirm-modal.js') AND DataTables ('admin.js')
// MUST be correctly included in the footer file for the buttons and table features to work.
// No extra inline JS is needed here if those files are correctly loaded via footer.php.
require_once $path_prefix . 'includes/footer.php';
?>