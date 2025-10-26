<?php
// /dashboard/student/courses.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['student']);

$page_title = "My Subjects";
$user = get_session_user();
$student_id = $user['id'];

// --- Fetch Enrolled Subjects Data ---
$subjects = []; // Initialize as empty array
$fetch_error = null; // Variable to store fetch error message
try {
    // Query to fetch subjects the student is actively enrolled in
    // Joins Courses to get program name and Users to get head teacher name
    $sql = "SELECT
                s.id AS subject_id,
                s.subject_name,
                s.subject_code,
                c.course_name,
                CONCAT(u.first_name, ' ', u.last_name) AS teacher_name
            FROM Subjects s
            JOIN Enrollments e ON s.id = e.subject_id
            JOIN Courses c ON s.course_id = c.id
            LEFT JOIN Users u ON c.teacher_id = u.id -- Left join in case teacher is unassigned
            WHERE e.student_id = ? AND e.status = 'enrolled'
            ORDER BY s.subject_name";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$student_id]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    log_error("Error fetching enrolled subjects for student ID {$student_id}: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not fetch your enrolled subjects from the database.";
    $_SESSION['error_message'] = $fetch_error; // Set session error
}
// --- End Data Fetching ---

// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0"><?php echo $page_title; ?></h4>
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

        <?php if (empty($subjects)): ?>
            <div class="alert alert-info">You are not currently enrolled in any subjects.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($subjects as $subject): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm course-card">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($subject['subject_name']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($subject['subject_code']); ?></h6>
                                <p class="card-text mb-1 mt-auto">
                                    <small>Program: <?php echo htmlspecialchars($subject['course_name']); ?></small>
                                </p>
                                <p class="card-text">
                                    <small>Instructor: <?php echo htmlspecialchars($subject['teacher_name'] ?? 'N/A'); ?></small>
                                </p>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <a href="view-course.php?id=<?php echo $subject['subject_id']; ?>" class="btn btn-primary w-100">
                                    <i class="fas fa-arrow-right me-2"></i> View Subject
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div> </div> <style>
.course-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>

<?php
require_once $path_prefix . 'includes/footer.php';
?>