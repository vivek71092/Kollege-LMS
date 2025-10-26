-- /migrations/seed_data.sql

-- Make sure to use the correct database name if running manually
-- USE if0_40212246_kollege;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Seed data for table `Users`
-- Password for all is 'password' (you should change this immediately!)
--
INSERT INTO `Users` (`id`, `first_name`, `last_name`, `email`, `password`, `phone`, `role`, `status`, `created_at`, `profile_image`, `bio`) VALUES
(1, 'Admin', 'User', 'admin@kollege.ct.ws', '$2y$10$QOBAb8..qfA5n8DNQBb3ZOhRzRvxzldwP6pE2iGuW0AEhccYV.L0e', '9998887777', 'admin', 'active', NOW(), NULL, 'Site Administrator'),
(2, 'Alan', 'Smith', 'alan.smith@kollege.ct.ws', '$2y$10$QOBAb8..qfA5n8DNQBb3ZOhRzRvxzldwP6pE2iGuW0AEhccYV.L0e', '5551234567', 'teacher', 'active', NOW(), NULL, 'Professor of Computer Science'),
(3, 'Emily', 'White', 'emily.white@kollege.ct.ws', '$2y$10$QOBAb8..qfA5n8DNQBb3ZOhRzRvxzldwP6pE2iGuW0AEhccYV.L0e', '5559876543', 'teacher', 'active', NOW(), NULL, 'Instructor for Business Courses'),
(10, 'Alice', 'Student', 'alice@example.com', '$2y$10$QOBAb8..qfA5n8DNQBb3ZOhRzRvxzldwP6pE2iGuW0AEhccYV.L0e', '1112223333', 'student', 'active', NOW(), NULL, 'Computer Science Student'),
(11, 'Bob', 'Student', 'bob@example.com', '$2y$10$QOBAb8..qfA5n8DNQBb3ZOhRzRvxzldwP6pE2iGuW0AEhccYV.L0e', '4445556666', 'student', 'active', NOW(), NULL, 'Business Student');

--
-- Seed data for table `Courses` (Programs)
--
INSERT INTO `Courses` (`id`, `course_code`, `course_name`, `semester`, `teacher_id`, `status`, `created_at`) VALUES
(1, 'CS', 'Computer Science', 8, 2, 'active', NOW()),
(2, 'BBA', 'Business Administration', 6, 3, 'active', NOW());

--
-- Seed data for table `Subjects` (Classes)
--
INSERT INTO `Subjects` (`id`, `subject_code`, `subject_name`, `course_id`, `semester`, `credits`, `status`, `created_at`) VALUES
(1, 'CS101', 'Introduction to Programming', 1, 1, 3, 'active', NOW()),
(2, 'CS305', 'Web Development', 1, 3, 3, 'active', NOW()),
(3, 'CS306', 'Data Science Fundamentals', 1, 3, 3, 'active', NOW()),
(4, 'BBA101', 'Principles of Management', 2, 1, 3, 'active', NOW()),
(5, 'BBA202', 'Marketing Basics', 2, 2, 3, 'active', NOW());

--
-- Seed data for table `Enrollments`
--
INSERT INTO `Enrollments` (`student_id`, `subject_id`, `enrollment_date`, `status`) VALUES
(10, 1, NOW(), 'enrolled'), -- Alice in Intro to Programming
(10, 2, NOW(), 'enrolled'), -- Alice in Web Development
(10, 3, NOW(), 'enrolled'), -- Alice in Data Science
(11, 4, NOW(), 'enrolled'), -- Bob in Principles of Management
(11, 5, NOW(), 'enrolled'); -- Bob in Marketing Basics

--
-- Seed data for table `Settings`
--
INSERT INTO `Settings` (`setting_key`, `setting_value`, `description`) VALUES
('site_name', 'Kollege LMS', 'The public name of the website'),
('admin_email', 'admin@kollege.ct.ws', 'Email address for system notifications'),
('maintenance_mode', '0', '0=Off, 1=On'),
('allow_student_registration', '1', '0=Off, 1=On');

--
-- Seed data for table `ClassSchedule`
--
INSERT INTO `ClassSchedule` (`subject_id`, `day_of_week`, `start_time`, `end_time`, `classroom`, `teacher_id`) VALUES
(2, 'Monday', '09:00:00', '11:00:00', 'Room 101', 2), -- Web Dev Mon
(3, 'Tuesday', '10:00:00', '12:00:00', 'Lab 3', 2),    -- Data Sci Tue
(2, 'Wednesday', '09:00:00', '11:00:00', 'Room 101', 2),-- Web Dev Wed
(3, 'Thursday', '10:00:00', '12:00:00', 'Lab 3', 2),   -- Data Sci Thu
(4, 'Monday', '13:00:00', '15:00:00', 'Room 205', 3),  -- Management Mon
(5, 'Wednesday', '13:00:00', '15:00:00', 'Room 205', 3);-- Marketing Wed


COMMIT;