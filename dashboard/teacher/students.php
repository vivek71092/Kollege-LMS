<?php
// /dashboard/teacher/students.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['teacher']); // Only teachers access this page
$user = get_session_user(); // Get logged-in teacher info
$teacher_id = $user['id'];
$page_title = "My Students";

// --- Fetch Students Enrolled in Teacher's Subjects ---
$students_data = []; // Initialize as empty array
$fetch_error = null; // Variable to store fetch error message
try {
    // Query to fetch distinct students enrolled in subjects taught by this teacher
    // (either via Course assignment or ClassSchedule assignment)
    // Also retrieves the subjects they are enrolled in *with this teacher*
    $sql = "SELECT DISTINCT
                u.id,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                GROUP_CONCAT(DISTINCT s.subject_name ORDER BY s.subject_name SEPARATOR ', ') AS enrolled_subjects
            FROM Users u
            JOIN Enrollments e ON u.id = e.student_id
            JOIN Subjects s ON e.subject_id = s.id
            JOIN Courses c ON s.course_id = c.id
            WHERE u.role = 'student' AND e.status = 'enrolled'
              AND (
                    c.teacher_id = :teacher_id_course -- Student is in a subject whose course head is this teacher
                    OR
                    s.id IN (SELECT DISTINCT cs.subject_id FROM ClassSchedule cs WHERE cs.teacher_id = :teacher_id_schedule) -- Student is in a subject this teacher teaches via schedule
                  )
            GROUP BY u.id, u.first_name, u.last_name, u.email, u.phone -- Group by student to get one row per student
            ORDER BY u.last_name, u.first_name"; // Order alphabetically

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':teacher_id_course' => $teacher_id,
        ':teacher_id_schedule' => $teacher_id
    ]);
    $students_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    log_error("Error fetching students for teacher ID {$teacher_id}: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not fetch your student list from the database.";
    $_SESSION['error_message'] = $fetch_error; // Set session error
}
// --- End Data Fetching ---

// Pass correct path prefix to header/footer
$path_prefix = '../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header">
        <h4 class="mb-0">All Students Enrolled in My Subjects</h4>
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
            <table class="table table-hover align-middle data-table" id="studentsTable">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Enrolled Subjects (Yours)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students_data)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No students are currently enrolled in your subjects.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($students_data as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php
                                    // Display subjects as badges or simple list
                                    $subjects_list = explode(', ', $student['enrolled_subjects']);
                                    foreach ($subjects_list as $subj_name) {
                                        echo '<span class="badge bg-secondary me-1">' . htmlspecialchars($subj_name) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td class="actions-cell">
                                    <a href="messages.php?to=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Message Student">
                                        <i class="fas fa-envelope"></i>
                                    </a>
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