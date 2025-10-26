<?php
// /dashboard/teacher/courses.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['teacher']);

$page_title = "My Subjects";
$user = get_session_user(); // Get logged-in teacher info
$teacher_id = $user['id'];

// --- Fetch Actual Assigned Subjects Data ---
$subjects = []; // Initialize as empty array
$fetch_error = null; // Variable to store fetch error message
try {
    // Query to fetch subjects assigned to this teacher via the Courses table
    // Also counts enrolled students for each subject using a subquery
    $sql = "SELECT
                s.id AS subject_id,
                s.subject_name,
                s.subject_code,
                c.course_name,
                (SELECT COUNT(*) FROM Enrollments e WHERE e.subject_id = s.id AND e.status = 'enrolled') AS student_count
            FROM Subjects s
            JOIN Courses c ON s.course_id = c.id
            WHERE c.teacher_id = ?  -- Filter by the teacher assigned to the main Course
               OR s.id IN (SELECT DISTINCT subject_id FROM ClassSchedule cs WHERE cs.teacher_id = ?) -- Include subjects where teacher has specific classes
            ORDER BY s.subject_name";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$teacher_id, $teacher_id]); // Pass teacher_id for both WHERE conditions
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    log_error("Error fetching teacher's subjects for teacher ID {$teacher_id}: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not fetch subject list from the database.";
    $_SESSION['error_message'] = $fetch_error; // Set session error
}
// --- End Data Fetching ---

// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0">My Assigned Subjects</h4>
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
            <div class="alert alert-info">You are not currently assigned to manage any subjects or scheduled to teach any classes.</div>
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
                                    <strong><?php echo $subject['student_count']; ?></strong> Students Enrolled
                                </p>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <a href="manage-course.php?id=<?php echo $subject['subject_id']; ?>" class="btn btn-primary w-100">
                                    <i class="fas fa-cog me-2"></i> Manage Subject
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