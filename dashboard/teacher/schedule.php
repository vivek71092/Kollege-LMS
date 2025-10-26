<?php
// /dashboard/teacher/schedule.php

// Load core files
require_once '../../config.php'; // Ensures $pdo is available
require_once '../../functions.php';
require_once '../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['teacher']); // Only teachers access this page
$user = get_session_user(); // Get logged-in teacher info
$teacher_id = $user['id'];
$page_title = "My Class Schedule";

// --- Fetch Teacher's Schedule ---
$schedule_data = []; // Initialize as empty array
$fetch_error = null; // Variable to store fetch error message
try {
    // Query ClassSchedule, joining Subjects, filtered by teacher_id
    // Order by the specific day order, then by start time
    $sql = "SELECT
                cs.day_of_week,
                cs.start_time,
                cs.end_time,
                cs.classroom,
                s.subject_name
            FROM ClassSchedule cs
            JOIN Subjects s ON cs.subject_id = s.id
            WHERE cs.teacher_id = ?
            ORDER BY FIELD(cs.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), cs.start_time ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$teacher_id]);
    $schedule_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group schedule by day for easier display (optional but helpful)
    $schedule_by_day = [];
    foreach ($schedule_data as $class) {
        $schedule_by_day[$class['day_of_week']][] = $class;
    }

} catch (PDOException $e) {
    log_error("Error fetching schedule for teacher ID {$teacher_id}: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not fetch your schedule from the database.";
    $_SESSION['error_message'] = $fetch_error; // Set session error
    $schedule_by_day = []; // Ensure it's an empty array on error
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

        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center">
                <thead class="table-light">
                    <tr>
                        <th style="width: 15%;">Day</th>
                        <th style="width: 25%;">Time</th>
                        <th>Subject</th>
                        <th>Classroom</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $days_ordered = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    $has_schedule = false; // Flag to check if any classes were found

                    foreach ($days_ordered as $day):
                        if (isset($schedule_by_day[$day]) && !empty($schedule_by_day[$day])):
                            $has_schedule = true;
                            $day_classes = $schedule_by_day[$day];
                            $rowspan = count($day_classes); // Calculate rowspan for the day cell
                            $first_class = true; // Flag for the first row of the day

                            foreach ($day_classes as $class):
                                $start = format_date('1970-01-01 ' . $class['start_time'], 'h:i A');
                                $end = format_date('1970-01-01 ' . $class['end_time'], 'h:i A');
                    ?>
                                <tr>
                                    <?php if ($first_class): // Only output the day cell for the first class of the day ?>
                                        <td class="fw-bold align-middle" rowspan="<?php echo $rowspan; ?>">
                                            <?php echo htmlspecialchars($day); ?>
                                        </td>
                                        <?php $first_class = false; ?>
                                    <?php endif; ?>
                                    <td><?php echo $start; ?> - <?php echo $end; ?></td>
                                    <td><?php echo htmlspecialchars($class['subject_name']); ?></td>
                                    <td><?php echo htmlspecialchars($class['classroom'] ?? 'N/A'); ?></td>
                                </tr>
                            <?php
                            endforeach; // end foreach class
                        endif; // end if day has classes
                    endforeach; // end foreach day

                    // Display message if no schedule data was found at all
                    if (!$has_schedule && empty($fetch_error)):
                    ?>
                        <tr>
                            <td colspan="4" class="text-muted text-center py-4">Your schedule is not available or no classes are assigned.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div> </div> </div> <?php
require_once $path_prefix . 'includes/footer.php';
?>