<?php
// /dashboard/admin/reports/user-report.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "User Report";
$user = get_session_user();

// --- Report Generation Logic (Handles POST request) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Get filter values
    $role_filter = sanitize_input($_POST['role'] ?? null);
    $status_filter = sanitize_input($_POST['status'] ?? null);

    try {
        // 2. Build SQL query
        $sql = "SELECT
                    id,
                    first_name,
                    last_name,
                    email,
                    phone,
                    role,
                    status,
                    created_at
                FROM Users
                WHERE 1=1";

        $params = [];

        if (!empty($role_filter)) {
            $sql .= " AND role = ?";
            $params[] = $role_filter;
        }
        if (!empty($status_filter)) {
            $sql .= " AND status = ?";
            $params[] = $status_filter;
        }

        $sql .= " ORDER BY role, last_name, first_name";

        // 3. Prepare and Execute
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. Set headers
        $filename = "user_report_" . date('Y-m-d') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        // 5. Output CSV
        $output = fopen('php://output', 'w');
        fputcsv($output, ['User ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Role', 'Status', 'Joined Date']);

        if (!empty($results)) {
            foreach ($results as $row) {
                fputcsv($output, [
                    $row['id'],
                    $row['first_name'],
                    $row['last_name'],
                    $row['email'],
                    $row['phone'] ?? '', // Handle potential null phone
                    ucfirst($row['role']),
                    ucfirst($row['status']),
                    $row['created_at'] // Consider formatting date
                ]);
            }
        } else {
             fputcsv($output, ['No users found matching your criteria.']);
        }

        fclose($output);
        exit;

     } catch (PDOException $e) {
        log_error("Error generating user report: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "A database error occurred while generating the report: " . $e->getMessage();
        redirect('dashboard/admin/reports/user-report.php');
     } catch (Exception $e) {
         log_error("General error generating user report: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "An unexpected error occurred: " . $e->getMessage();
        redirect('dashboard/admin/reports/user-report.php');
    }
}
// --- End Report Generation Logic ---

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
                <p>Select criteria to generate a CSV report of system users.</p>

                <?php
                // Display messages
                display_flash_message('success_message', 'alert-success');
                display_flash_message('error_message', 'alert-danger');
                display_flash_message('info_message', 'alert-info');
                ?>

                <form action="user-report.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="role" class="form-label">Filter by Role (Optional)</label>
                        <select class="form-select" id="role" name="role">
                            <option value="">All Roles</option>
                            <option value="student">Students</option>
                            <option value="teacher">Teachers</option>
                            <option value="admin">Admins</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Filter by Status (Optional)</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-csv me-2"></i> Generate Report
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>