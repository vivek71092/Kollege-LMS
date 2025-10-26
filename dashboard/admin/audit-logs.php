<?php
// /dashboard/admin/audit-logs.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Audit Logs";
$user = get_session_user(); // Logged-in admin

// --- Fetch Actual Audit Log Data ---
$logs = []; // Initialize as empty array
$fetch_error = null; // Variable to store fetch error message
try {
    // Query to fetch audit logs, joining with Users to get the user's name
    // Use LEFT JOIN in case the user who performed the action was later deleted
    $sql = "SELECT al.id, al.action, al.table_name, al.record_id, al.details, al.timestamp,
                   CONCAT(u.first_name, ' ', u.last_name) AS user_name,
                   u.email AS user_email
            FROM AuditLogs al
            LEFT JOIN Users u ON al.user_id = u.id
            ORDER BY al.timestamp DESC
            LIMIT 200"; // Limit to the most recent 200 entries (implement proper pagination later)

    $stmt = $pdo->query($sql);
    if ($stmt) {
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $fetch_error = "Failed to prepare the audit log query.";
    }

} catch (PDOException $e) {
    log_error("Error fetching audit logs: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not fetch audit logs from the database.";
    $_SESSION['error_message'] = $fetch_error; // Set session error
}
// --- End Data Fetching ---

// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0">System Audit Logs</h4>
    </div>
    <div class="card-body">

        <?php
        // Display any success or error messages (including fetch error)
        display_flash_message('success_message', 'alert-success');
        display_flash_message('error_message', 'alert-danger');
        ?>

        <?php if ($fetch_error): ?>
             <div class="alert alert-warning"><?php echo htmlspecialchars($fetch_error); ?></div>
        <?php endif; ?>

        <p>Showing the most recent system actions.</p>

        <div class="table-responsive">
            <table class="table table-hover table-sm data-table" id="auditLogTable">
                <thead class="table-light">
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Table</th>
                        <th>Record ID</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No audit logs found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td style="white-space: nowrap;"><?php echo format_date($log['timestamp'], 'Y-m-d H:i:s'); // More precise format ?></td>
                                <td title="<?php echo htmlspecialchars($log['user_email'] ?? 'N/A'); ?>">
                                    <?php echo htmlspecialchars($log['user_name'] ?? 'System/Deleted User'); ?>
                                </td>
                                <td>
                                    <?php
                                    // Set badge color based on action type
                                    $action = strtoupper($log['action']);
                                    $badge_class = 'bg-secondary'; // Default
                                    if (in_array($action, ['CREATE', 'INSERT'])) $badge_class = 'bg-success';
                                    if (in_array($action, ['UPDATE', 'EDIT'])) $badge_class = 'bg-primary';
                                    if (in_array($action, ['DELETE', 'REMOVE'])) $badge_class = 'bg-danger';
                                    if (in_array($action, ['LOGIN', 'AUTH'])) $badge_class = 'bg-info';
                                    if (in_array($action, ['LOGOUT'])) $badge_class = 'bg-warning text-dark';
                                    echo "<span class='badge " . $badge_class . "'>" . htmlspecialchars($action) . "</span>";
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($log['table_name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($log['record_id'] ?? ''); ?></td>
                                <td style="min-width: 250px;"><?php echo htmlspecialchars($log['details'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// DataTables initialization is handled by admin.js loaded in the footer
require_once $path_prefix . 'includes/footer.php';
?>