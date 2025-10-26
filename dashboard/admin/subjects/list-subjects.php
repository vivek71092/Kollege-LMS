<?php
// /dashboard/admin/subjects/list-subjects.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Manage Subjects";
$user = get_session_user();

// --- Fetch Actual Subject Data ---
try {
    // Query to fetch subjects and join with Courses table to get course name
    $sql = "SELECT s.id, s.subject_code, s.subject_name, s.semester, c.course_name, s.status
            FROM Subjects s
            JOIN Courses c ON s.course_id = c.id
            ORDER BY c.course_name, s.semester, s.subject_name"; // Order logically
    $stmt = $pdo->query($sql);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as associative arrays
} catch (PDOException $e) {
    // Log the database error
    log_error("Error fetching subjects: " . $e->getMessage(), __FILE__, __LINE__);
    $subjects = []; // Set to empty array on error
    $_SESSION['error_message'] = "Could not fetch the subject list from the database.";
}
// --- End Data Fetching ---

// Pass correct path prefix to header/footer
$path_prefix = '../../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Subject Management</h4>
        <a href="add-subject.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Add New Subject
        </a>
    </div>
    <div class="card-body">

        <?php
        // Display any success or error messages
        display_flash_message('success_message', 'alert-success');
        display_flash_message('error_message', 'alert-danger'); // Show potential fetch error too
        ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle data-table" id="subjectsTable">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Subject Name</th>
                        <th>Course (Program)</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subjects)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No subjects found. Add one using the button above.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($subject['course_name']); ?></td>
                                <td><?php echo htmlspecialchars($subject['semester']); ?></td>
                                <td>
                                    <?php // Display status badge ?>
                                    <?php if ($subject['status'] == 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions-cell">
                                    <a href="edit-subject.php?id=<?php echo $subject['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit Subject Details">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Subject"
                                            data-bs-toggle="modal" data-bs-target="#confirmModal"
                                            data-title="Delete Subject Confirmation"
                                            data-body="Are you sure you want to delete the subject '<?php echo htmlspecialchars($subject['subject_name']); ?>'? This will also delete ALL associated notes, assignments, enrollments, marks, etc. This action cannot be undone."
                                            data-confirm-url="delete-subject.php?id=<?php echo $subject['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
// The confirm-modal.js script is loaded via the footer and will handle the delete confirmation
require_once $path_prefix . 'includes/footer.php';
?>