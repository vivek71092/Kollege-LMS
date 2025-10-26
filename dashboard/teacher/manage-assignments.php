<?php
// /dashboard/teacher/manage-assignments.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['teacher']); // Only teachers access this specific page

$page_title = "Manage Assignments";
$user = get_session_user(); // Get logged-in teacher info
$teacher_id = $user['id'];

// --- Fetch Assignments Created by This Teacher ---
$assignments = []; // Initialize as empty array
$fetch_error = null; // Variable to store fetch error message
try {
    // Query to fetch assignments created by the current teacher
    // Includes subqueries to count total and pending submissions
    $sql = "SELECT
                a.id,
                a.title,
                a.due_date,
                s.subject_name,
                (SELECT COUNT(*) FROM Submissions sub WHERE sub.assignment_id = a.id) AS total_submissions,
                (SELECT COUNT(*) FROM Submissions sub WHERE sub.assignment_id = a.id AND sub.status = 'submitted') AS pending_grading
            FROM Assignments a
            JOIN Subjects s ON a.subject_id = s.id
            WHERE a.created_by = ?
            ORDER BY a.due_date DESC"; // Show newest first based on due date

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$teacher_id]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    log_error("Error fetching assignments for teacher ID {$teacher_id}: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not fetch your assignments list from the database.";
    $_SESSION['error_message'] = $fetch_error; // Set session error
}
// --- End Data Fetching ---

// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">My Created Assignments</h4>
        <a href="create-assignment.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Create New Assignment
        </a>
    </div>
    <div class="card-body">

        <?php
        // Display any success or error messages (including fetch error)
        display_flash_message('success_message', 'alert-success');
        display_flash_message('error_message', 'alert-danger');
        ?>

        <?php if ($fetch_error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($fetch_error); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle data-table" id="assignmentsTable">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Subject</th>
                        <th>Due Date</th>
                        <th>Total Submissions</th>
                        <th>Pending Grading</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($assignments)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">You haven't created any assignments yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($assignments as $assignment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['subject_name']); ?></td>
                                <td><?php echo format_date($assignment['due_date']); ?></td>
                                <td><?php echo $assignment['total_submissions']; ?></td>
                                <td>
                                    <?php // Display pending count with appropriate badge ?>
                                    <?php if ($assignment['pending_grading'] > 0): ?>
                                        <span class="badge bg-warning text-dark"><?php echo $assignment['pending_grading']; ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success">0</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions-cell">
                                    <a href="view-submissions.php?id=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-primary" title="View Submissions">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-secondary disabled" title="Edit Assignment (Coming Soon)">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Assignment"
                                            data-bs-toggle="modal" data-bs-target="#confirmModal"
                                            data-title="Delete Assignment Confirmation"
                                            data-body="Are you sure you want to delete the assignment '<?php echo htmlspecialchars($assignment['title']); ?>'? This will also delete ALL student submissions for it."
                                            data-confirm-url="#"> <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div> </div> </div> <?php
// Necessary JS files (confirm-modal.js, DataTables init) are loaded via the footer
require_once $path_prefix . 'includes/footer.php';
?>