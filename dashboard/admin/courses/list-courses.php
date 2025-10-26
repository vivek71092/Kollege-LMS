<?php
// /dashboard/admin/courses/list-courses.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Manage Courses (Programs)";
$user = get_session_user();

// --- Fetch Actual Course Data ---
try {
    // Query to fetch courses and join with Users table to get teacher's name
    $sql = "SELECT c.id, c.course_code, c.course_name, c.semester, c.status, 
                   CONCAT(u.first_name, ' ', u.last_name) AS teacher_name 
            FROM Courses c 
            LEFT JOIN Users u ON c.teacher_id = u.id 
            ORDER BY c.course_name"; // Order alphabetically by course name
    $stmt = $pdo->query($sql);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as associative arrays
} catch (PDOException $e) {
    // Log the database error
    log_error("Error fetching courses: " . $e->getMessage(), __FILE__, __LINE__);
    $courses = []; // Set to empty array on error
    $_SESSION['error_message'] = "Could not fetch the course list from the database.";
}
// --- End Data Fetching ---

// Pass correct path prefix to header/footer
$path_prefix = '../../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Course/Program Management</h4>
        <a href="add-course.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Add New Course
        </a>
    </div>
    <div class="card-body">

        <?php
        // Display any success or error messages
        display_flash_message('success_message', 'alert-success');
        display_flash_message('error_message', 'alert-danger'); // Show potential fetch error too
        ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle data-table" id="coursesTable">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Course Name</th>
                        <th>Head Teacher</th>
                        <th>Semesters</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No courses found. Add one using the button above.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td><?php echo $course['id']; ?></td>
                                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                <td><?php echo $course['teacher_name'] ? htmlspecialchars($course['teacher_name']) : '<span class="text-muted">Not Assigned</span>'; ?></td>
                                <td><?php echo htmlspecialchars($course['semester']); ?></td>
                                <td>
                                    <?php // Display status badge ?>
                                    <?php if ($course['status'] == 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions-cell">
                                    <a href="assign-teacher.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-info" title="Assign Head Teacher">
                                        <i class="fas fa-user-plus"></i>
                                    </a>
                                    <a href="edit-course.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit Course Details">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Course"
                                            data-bs-toggle="modal" data-bs-target="#confirmModal"
                                            data-title="Delete Course Confirmation"
                                            data-body="Are you sure you want to delete the course '<?php echo htmlspecialchars($course['course_name']); ?>'? This will also delete ALL associated subjects, enrollments, assignments, notes, etc. This action cannot be undone."
                                            data-confirm-url="delete-course.php?id=<?php echo $course['id']; ?>">
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