<?php
// /dashboard/admin/announcements/list-announcements.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Manage Announcements";
$user = get_session_user();

// --- Fetch Actual Announcement Data ---
$announcements = []; // Initialize as empty array
$fetch_error = null; // Variable to store fetch error message
try {
    // Query announcements and join with Users to get the author's name
    $sql = "SELECT a.id, a.title, a.status, a.priority, a.created_date,
                   CONCAT(u.first_name, ' ', u.last_name) AS author
            FROM Announcements a
            LEFT JOIN Users u ON a.created_by = u.id
            ORDER BY a.priority DESC, a.created_date DESC";
    $stmt = $pdo->query($sql);
    if ($stmt) {
        $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $fetch_error = "Failed to prepare the announcement query.";
    }
} catch (PDOException $e) {
    log_error("Error fetching announcements: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not fetch the announcement list from the database.";
    $_SESSION['error_message'] = $fetch_error; // Set session error too
}
// --- End Data Fetching ---

// Pass correct path prefix to header/footer
$path_prefix = '../../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Announcement Management</h4>
        <a href="add-announcement.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Create New Announcement
        </a>
    </div>
    <div class="card-body">

        <?php
        // Display any success or error messages (including fetch error)
        display_flash_message('success_message', 'alert-success');
        display_flash_message('error_message', 'alert-danger');
        ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle data-table" id="announcementsTable">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>         <th>Author</th>        <th>Status</th>        <th>Priority</th>      <th>Created Date</th>  <th>Actions</th>       </tr>
                </thead>
                <tbody>
                    <?php if (!empty($fetch_error)): // Show fetch error if occurred ?>
                        <tr>
                            <td colspan="6" class="text-center text-danger"><?php echo htmlspecialchars($fetch_error); ?></td>
                        </tr>
                    <?php elseif (empty($announcements)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No announcements found. Add one using the button above.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($announcements as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td><?php echo htmlspecialchars($item['author'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php if ($item['status'] == 'published'): ?><span class="badge bg-success">Published</span><?php else: ?><span class="badge bg-secondary">Draft</span><?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item['priority'] == 1): ?><span class="badge bg-warning text-dark">High</span><?php else: ?><span class="badge bg-info">Normal</span><?php endif; ?>
                                </td>
                                <td><?php echo format_date($item['created_date']); ?></td>
                                <td class="actions-cell">
                                    <a href="edit-announcement.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit Announcement"><i class="fas fa-edit"></i></a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Announcement" data-bs-toggle="modal" data-bs-target="#confirmModal" data-title="Delete Announcement Confirmation" data-body="Are you sure you want to delete the announcement: '<?php echo htmlspecialchars($item['title']); ?>'?" data-confirm-url="delete-announcement.php?id=<?php echo $item['id']; ?>"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div> </div> </div> <?php
// The confirm-modal.js and admin.js (for DataTables) scripts are loaded via the footer
require_once $path_prefix . 'includes/footer.php';
?>