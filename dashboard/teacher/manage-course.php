<?php
// /dashboard/teacher/manage-course.php

// Load core files
require_once '../../config.php';
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['teacher']);
$user = get_session_user();
$teacher_id = $user['id'];

// Get subject ID from URL
$subject_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$subject_id) {
    $_SESSION['error_message'] = "Invalid subject ID.";
    redirect('dashboard/teacher/courses.php');
}

// --- Placeholder Data ---
// 1. Verify teacher is assigned to this subject
// $stmt = $pdo->prepare("SELECT s.subject_name, s.subject_code, c.description FROM Subjects s JOIN Courses c ON s.course_id = c.id WHERE s.id = ? AND c.teacher_id = ?");
// $stmt->execute([$subject_id, $teacher_id]);
// $subject = $stmt->fetch();
$subject = [
    'subject_name' => 'Web Development', 
    'subject_code' => 'CS305', 
    'description' => 'This course covers the fundamentals of HTML, CSS, JavaScript, PHP, and MySQL.'
];

if (!$subject) {
    $_SESSION['error_message'] = "You are not authorized to manage this subject.";
    redirect('dashboard/teacher/courses.php');
}

// 2. Get enrolled students
// $stmt = $pdo->prepare("SELECT u.id, u.first_name, u.last_name, u.email FROM Users u JOIN Enrollments e ON u.id = e.student_id WHERE e.subject_id = ?");
// $stmt->execute([$subject_id]);
// $students = $stmt->fetchAll();
$students = [
    ['id' => 10, 'first_name' => 'Alice', 'last_name' => 'Smith', 'email' => 'alice@example.com'],
    ['id' => 11, 'first_name' => 'Bob', 'last_name' => 'Johnson', 'email' => 'bob@example.com'],
];
// --- End Placeholder Data ---

$page_title = "Manage: " . htmlspecialchars($subject['subject_name']);
require_once '../../includes/header.php';
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h4"><?php echo htmlspecialchars($subject['subject_name']); ?> (<?php echo htmlspecialchars($subject['subject_code']); ?>)</h2>
                <p><?php echo htmlspecialchars($subject['description']); ?></p>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="mark-attendance.php?subject_id=<?php echo $subject_id; ?>" class="list-group-item list-group-item-action"><i class="fas fa-user-check fa-fw me-2"></i> Mark Attendance</a>
                    <a href="manage-marks.php?subject_id=<?php echo $subject_id; ?>" class="list-group-item list-group-item-action"><i class="fas fa-percentage fa-fw me-2"></i> Manage Marks</a>
                    <a href="upload-notes.php?subject_id=<?php echo $subject_id; ?>" class="list-group-item list-group-item-action"><i class="fas fa-upload fa-fw me-2"></i> Upload Notes</a>
                    <a href="create-assignment.php?subject_id=<?php echo $subject_id; ?>" class="list-group-item list-group-item-action"><i class="fas fa-plus-circle fa-fw me-2"></i> Create Assignment</a>
                    <a href="manage-notes.php?subject_id=<?php echo $subject_id; ?>" class="list-group-item list-group-item-action"><i class="fas fa-book-open fa-fw me-2"></i> View All Notes</a>
                    <a href="manage-assignments.php?subject_id=<?php echo $subject_id; ?>" class="list-group-item list-group-item-action"><i class="fas fa-tasks fa-fw me-2"></i> View All Assignments</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h5 class="mb-0">Enrolled Students (<?php echo count($students); ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td>
                                        <a href="messages.php?to=<?php echo $student['id']; ?>" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                        </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center">
                 <a href="students.php" class="btn btn-outline-primary btn-sm">View All My Students</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>