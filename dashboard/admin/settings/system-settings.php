<?php
// /dashboard/admin/settings/system-settings.php

// Load core files
require_once '../../../config.php'; // Ensures $pdo is available
require_once '../../../functions.php';
require_once '../../../auth/check_auth.php'; // Ensure user is logged in

// Role-specific check
require_role(['admin']);

$page_title = "System Settings";
$user = get_session_user();

// --- Settings Update Processing (POST request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the settings submitted from the form
    $settings_to_update = [
        // Sanitize each expected input
        'site_name' => sanitize_input($_POST['site_name'] ?? SITE_NAME), // Fallback to constant if empty
        'admin_email' => sanitize_input($_POST['admin_email'] ?? ADMIN_EMAIL),
        'maintenance_mode' => isset($_POST['maintenance_mode']) ? (int)$_POST['maintenance_mode'] : 0, // Cast to int (0 or 1)
        'allow_student_registration' => isset($_POST['allow_student_registration']) ? (int)$_POST['allow_student_registration'] : 0, // Added this setting
    ];

    // --- ACTUAL DATABASE UPDATE LOGIC ---
    $pdo->beginTransaction(); // Start transaction for multiple updates
    try {
        $sql = "INSERT INTO Settings (setting_key, setting_value) 
                VALUES (:key, :value) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        $stmt = $pdo->prepare($sql);

        $all_success = true;
        foreach ($settings_to_update as $key => $value) {
            if (!$stmt->execute([':key' => $key, ':value' => $value])) {
                $all_success = false;
                // Log specific key failure if needed
                log_error("Failed to update setting: $key", __FILE__, __LINE__);
                break; // Stop on first error
            }
        }

        if ($all_success) {
            $pdo->commit(); // Commit changes if all updates were successful
            $_SESSION['success_message'] = "System settings updated successfully!";
            // Optional: Log action
            // Logger::info("Admin (ID: {$user['id']}) updated system settings.");
        } else {
            $pdo->rollBack(); // Roll back changes if any update failed
            $_SESSION['error_message'] = "Failed to update one or more settings.";
        }

    } catch (PDOException $e) {
        $pdo->rollBack(); // Roll back changes on database error
        log_error("Error updating system settings: " . $e->getMessage(), __FILE__, __LINE__);
        $_SESSION['error_message'] = "A database error occurred while saving settings.";
    }
    // --- END ACTUAL DATABASE LOGIC ---

    // Redirect back to the settings page to show messages
    redirect('dashboard/admin/settings/system-settings.php');
}
// --- End POST Processing ---


// --- Fetch Current Settings (GET request) ---
$settings = [];
$fetch_error = null;
try {
    // Fetch all settings from the database into an associative array [key => value]
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM Settings");
    if ($stmt) {
        $settings_raw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        // Ensure expected keys exist, using defaults from config.php if not found in DB
        $settings['site_name'] = $settings_raw['site_name'] ?? SITE_NAME;
        $settings['admin_email'] = $settings_raw['admin_email'] ?? ADMIN_EMAIL;
        $settings['maintenance_mode'] = $settings_raw['maintenance_mode'] ?? '0';
        $settings['allow_student_registration'] = $settings_raw['allow_student_registration'] ?? '1'; // Default allow
    } else {
         $fetch_error = "Failed to prepare settings query.";
         // Use defaults if fetch fails
         $settings['site_name'] = SITE_NAME;
         $settings['admin_email'] = ADMIN_EMAIL;
         $settings['maintenance_mode'] = '0';
         $settings['allow_student_registration'] = '1';
    }
} catch (PDOException $e) {
    log_error("Error fetching system settings: " . $e->getMessage(), __FILE__, __LINE__);
    $fetch_error = "Could not fetch settings from the database.";
    $_SESSION['error_message'] = $fetch_error;
    // Use defaults if fetch fails
    $settings['site_name'] = SITE_NAME;
    $settings['admin_email'] = ADMIN_EMAIL;
    $settings['maintenance_mode'] = '0';
    $settings['allow_student_registration'] = '1';
}
// --- End Fetch ---

// Pass correct path prefix to header/footer
$path_prefix = '../../../';
require_once $path_prefix . 'includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">General System Settings</h4>
            </div>
            <div class="card-body">

                <?php
                // Display messages (including fetch error)
                display_flash_message('success_message', 'alert-success');
                display_flash_message('error_message', 'alert-danger');
                ?>

                 <?php if ($fetch_error && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
                    <div class="alert alert-warning">Could not load current settings from database. Displaying defaults. Error: <?php echo htmlspecialchars($fetch_error); ?></div>
                <?php endif; ?>

                <form action="system-settings.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="site_name" class="form-label">Site Name</label>
                        <input type="text" class="form-control" id="site_name" name="site_name"
                               value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
                         <div class="invalid-feedback">Site name is required.</div>
                    </div>

                    <div class="mb-3">
                        <label for="admin_email" class="form-label">Administrator Email</label>
                        <input type="email" class="form-control" id="admin_email" name="admin_email"
                               value="<?php echo htmlspecialchars($settings['admin_email']); ?>" required>
                        <small class="form-text">Used for system notifications and contact forms.</small>
                         <div class="invalid-feedback">A valid admin email is required.</div>
                    </div>

                    <div class="mb-3">
                        <label for="maintenance_mode" class="form-label">Maintenance Mode</label>
                        <select class="form-select" id="maintenance_mode" name="maintenance_mode" required>
                            <option value="0" <?php if ($settings['maintenance_mode'] == '0') echo 'selected'; ?>>Off (Site Live)</option>
                            <option value="1" <?php if ($settings['maintenance_mode'] == '1') echo 'selected'; ?>>On (Site Disabled for non-admins)</option>
                        </select>
                        <small class="form-text">When On, only logged-in Admins can access the site.</small>
                    </div>

                    <div class="mb-3">
                        <label for="allow_student_registration" class="form-label">Allow Student Self-Registration</label>
                        <select class="form-select" id="allow_student_registration" name="allow_student_registration" required>
                            <option value="1" <?php if ($settings['allow_student_registration'] == '1') echo 'selected'; ?>>Yes (Allow students to register themselves)</option>
                            <option value="0" <?php if ($settings['allow_student_registration'] == '0') echo 'selected'; ?>>No (Admin/Teacher must create student accounts)</option>
                        </select>
                    </div>


                    <hr class="my-4">

                    <button type="submit" class="btn btn-primary">Save Settings</button>
                    <a href="../dashboard.php" class="btn btn-secondary">Cancel</a> </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once $path_prefix . 'includes/footer.php';
?>