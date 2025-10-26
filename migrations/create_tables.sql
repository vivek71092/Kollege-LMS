-- /migrations/create_tables.sql

-- Make sure to use the correct database name if running manually
-- USE if0_40212246_kollege;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Table structure for table `Users`
--
CREATE TABLE `Users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NULL,
  `role` ENUM('student', 'teacher', 'admin') NOT NULL,
  `status` ENUM('active', 'pending', 'suspended') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `profile_image` VARCHAR(255) NULL,
  `bio` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `Roles` (Optional, as role is ENUM in Users)
-- If you need more role details or plan more roles, use this.
-- CREATE TABLE `Roles` (
--   `id` INT AUTO_INCREMENT PRIMARY KEY,
--   `role_name` VARCHAR(50) NOT NULL UNIQUE,
--   `description` TEXT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `Courses` (Programs)
--
CREATE TABLE `Courses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `course_code` VARCHAR(20) NOT NULL UNIQUE,
  `course_name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `semester` INT NULL COMMENT 'Total number of semesters',
  `teacher_id` INT NULL COMMENT 'Head teacher/coordinator ID',
  `credits` INT NULL COMMENT 'Total credits for the course',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   FOREIGN KEY (`teacher_id`) REFERENCES `Users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `Subjects` (Individual Classes)
--
CREATE TABLE `Subjects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subject_code` VARCHAR(20) NOT NULL UNIQUE,
  `subject_name` VARCHAR(255) NOT NULL,
  `course_id` INT NOT NULL,
  `semester` INT NOT NULL COMMENT 'Semester in which this subject is offered',
  `credits` INT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `Courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `Enrollments`
--
CREATE TABLE `Enrollments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `subject_id` INT NOT NULL,
  `enrollment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('enrolled', 'completed', 'withdrawn') NOT NULL DEFAULT 'enrolled',
  FOREIGN KEY (`student_id`) REFERENCES `Users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_id`) REFERENCES `Subjects`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `student_subject` (`student_id`, `subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `Attendance`
--
CREATE TABLE `Attendance` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `subject_id` INT NOT NULL,
  `date` DATE NOT NULL,
  `status` ENUM('present', 'absent') NOT NULL,
  `teacher_id` INT NOT NULL COMMENT 'Who marked the attendance',
  `remarks` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `Users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_id`) REFERENCES `Subjects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`teacher_id`) REFERENCES `Users`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `student_subject_date` (`student_id`, `subject_id`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `Marks`
--
CREATE TABLE `Marks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `subject_id` INT NOT NULL,
  `assignment_marks` DECIMAL(5,2) NULL,
  `midterm_marks` DECIMAL(5,2) NULL,
  `final_marks` DECIMAL(5,2) NULL,
  `total_marks` DECIMAL(5,2) NULL,
  `grade` VARCHAR(5) NULL,
  `teacher_id` INT NOT NULL COMMENT 'Who last updated the marks',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `Users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_id`) REFERENCES `Subjects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`teacher_id`) REFERENCES `Users`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `student_subject_marks` (`student_id`, `subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `Notes`
--
CREATE TABLE `Notes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subject_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `uploaded_by` INT NOT NULL COMMENT 'User ID of teacher/admin',
  `upload_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `file_type` VARCHAR(100) NULL,
  `file_size` INT NULL,
  FOREIGN KEY (`subject_id`) REFERENCES `Subjects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`uploaded_by`) REFERENCES `Users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `Assignments`
--
CREATE TABLE `Assignments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subject_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `instructions` TEXT NULL,
  `due_date` TIMESTAMP NOT NULL,
  `max_marks` INT NOT NULL,
  `created_by` INT NOT NULL COMMENT 'User ID of teacher/admin',
  `status` ENUM('published', 'draft') NOT NULL DEFAULT 'published',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`subject_id`) REFERENCES `Subjects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `Users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `Submissions`
--
CREATE TABLE `Submissions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `assignment_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  `submission_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `file_path` VARCHAR(255) NOT NULL,
  `marks_obtained` DECIMAL(5,2) NULL,
  `feedback` TEXT NULL,
  `graded_date` TIMESTAMP NULL,
  `graded_by` INT NULL COMMENT 'User ID of teacher/admin',
  `status` ENUM('submitted', 'graded', 'late', 'resubmit') NOT NULL DEFAULT 'submitted',
  FOREIGN KEY (`assignment_id`) REFERENCES `Assignments`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `Users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`graded_by`) REFERENCES `Users`(`id`) ON DELETE SET NULL,
  UNIQUE KEY `assignment_student` (`assignment_id`, `student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `Announcements`
--
CREATE TABLE `Announcements` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `created_by` INT NOT NULL COMMENT 'User ID of admin',
  `created_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('published', 'draft') NOT NULL DEFAULT 'draft',
  `image` VARCHAR(255) NULL,
  `priority` TINYINT(1) DEFAULT 0 COMMENT '0=Normal, 1=High/Pinned'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `Settings`
--
CREATE TABLE `Settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `description` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `AuditLogs`
--
CREATE TABLE `AuditLogs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `action` VARCHAR(50) NOT NULL,
  `table_name` VARCHAR(50) NULL,
  `record_id` INT NULL,
  `details` TEXT NULL,
  `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `Messages`
--
CREATE TABLE `Messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sender_id` INT NOT NULL,
  `receiver_id` INT NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `sent_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `read_status` TINYINT(1) DEFAULT 0 COMMENT '0=Unread, 1=Read',
  FOREIGN KEY (`sender_id`) REFERENCES `Users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`receiver_id`) REFERENCES `Users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `Notifications`
--
CREATE TABLE `Notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL COMMENT 'Recipient User ID',
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `type` VARCHAR(50) NULL COMMENT 'e.g., grade, assignment, announcement',
  `reference_id` INT NULL COMMENT 'ID of related item (e.g., assignment ID)',
  `read_status` TINYINT(1) DEFAULT 0 COMMENT '0=Unread, 1=Read',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `ClassSchedule`
--
CREATE TABLE `ClassSchedule` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subject_id` INT NOT NULL,
  `day_of_week` ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `classroom` VARCHAR(100) NULL,
  `teacher_id` INT NULL COMMENT 'Specific teacher for this slot, if different from course head',
  FOREIGN KEY (`subject_id`) REFERENCES `Subjects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`teacher_id`) REFERENCES `Users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `Certificates` (Optional)
--
CREATE TABLE `Certificates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `course_id` INT NOT NULL COMMENT 'The overall course/program ID',
  `issue_date` DATE NOT NULL,
  `certificate_path` VARCHAR(255) NOT NULL,
  `status` ENUM('issued', 'revoked') NOT NULL DEFAULT 'issued',
  FOREIGN KEY (`student_id`) REFERENCES `Users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `Courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


COMMIT;