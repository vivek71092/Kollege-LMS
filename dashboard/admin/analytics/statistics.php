<?php
// /dashboard/admin/analytics/statistics.php

// Load core files
require_once '../../../config.php';
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "Detailed Statistics";
$user = get_session_user();

// --- Placeholder Data ---
// $top_courses = $pdo->query("SELECT c.course_name, COUNT(e.id) as enrollment_count FROM Enrollments e JOIN Subjects s ON e.subject_id = s.id JOIN Courses c ON s.course_id = c.id GROUP BY c.id ORDER BY enrollment_count DESC LIMIT 5")->fetchAll();
$top_courses = [
    ['course_name' => 'Computer Science', 'enrollment_count' => 150],
    ['course_name' => 'Business Administration', 'enrollment_count' => 85],
];

// $recent_signups = $pdo->query("SELECT first_name, last_name, email, created_at FROM Users WHERE role = 'student' ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recent_signups = [
    ['first_name' => 'New', 'last_name' => 'Student1', 'email' => 'new1@example.com', 'created_at' => '2025-10-23 08:00:00'],
    ['first_name' => 'New', 'last_name' => 'Student2', 'email' => 'new2@example.com', 'created_at' => '2025-10-22 14:00:00'],
];
// --- End Placeholder Data ---

require_once '../../../includes/header.php';
?>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0">Top Courses by Enrollment</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Course Name</th>
                            <th>Total Enrollments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_courses as $course): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                <td><?php echo $course['enrollment_count']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0">Recent Student Signups</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_signups as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td><?php echo format_date($student['created_at'], 'M d, Y'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../../includes/footer.php';
?>