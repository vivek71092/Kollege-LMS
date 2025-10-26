# Kollege LMS - A Comprehensive PHP-Based Learning Management System

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT) **Project Status:** Development / Academic Submission
**Repository:** [https://github.com/vivek71092/Kollege-LMS](https://github.com/vivek71092/Kollege-LMS)
**Live Demo:** [https://kollege.ct.ws/](https://kollege.ct.ws/)

---

## 1. Introduction

**Kollege LMS** is a full-featured Learning Management System (LMS) developed as a Major Project for the 6th semester of the BCA program at Chandigarh University. It is built from the ground up using core web technologies like **PHP, MySQL, HTML, CSS (Bootstrap 5), and JavaScript**, demonstrating a practical application of full-stack web development principles.

The system provides distinct interfaces and functionalities for **Students**, **Teachers**, and **Administrators**, aiming to simulate a real-world environment for managing educational content, user interactions, assessments, and system administration. It is designed with compatibility for standard shared hosting environments in mind.

**Developer:**
* **Vivek Kumar**
* BCA 3rd Year Student, Chandigarh University

---

## 2. Table of Contents

* [1. Introduction](#1-introduction)
* [2. Table of Contents](#2-table-of-contents)
* [3. Project Goals & Scope](#3-project-goals--scope)
* [4. Core Features](#4-core-features)
    * [4.1. Public Access](#41-public-access)
    * [4.2. Authentication & User Management](#42-authentication--user-management)
    * [4.3. Student Portal](#43-student-portal)
    * [4.4. Teacher Portal](#44-teacher-portal)
    * [4.5. Admin Panel](#45-admin-panel)
    * [4.6. Common Features](#46-common-features)
* [5. Technology Stack & Architecture](#5-technology-stack--architecture)
    * [5.1. Core Technologies](#51-core-technologies)
    * [5.2. Frontend Libraries](#52-frontend-libraries)
    * [5.3. Architectural Approach](#53-architectural-approach)
* [6. Database Schema Overview](#6-database-schema-overview)
* [7. Setup and Installation Guide](#7-setup-and-installation-guide)
    * [7.1. Prerequisites](#71-prerequisites)
    * [7.2. Installation Steps](#72-installation-steps)
    * [7.3. Configuration](#73-configuration)
    * [7.4. Web Server Configuration](#74-web-server-configuration)
    * [7.5. Directory Permissions](#75-directory-permissions)
* [8. Usage & Default Credentials](#8-usage--default-credentials)
* [9. Key Implementation Details](#9-key-implementation-details)
    * [9.1. Security Measures](#91-security-measures)
    * [9.2. Performance Considerations](#92-performance-considerations)
    * [9.3. User Experience (UX)](#93-user-experience-ux)
    * [9.4. File Management Strategy](#94-file-management-strategy)
    * [9.5. Error Handling & Logging](#95-error-handling--logging)
* [10. API Endpoints Summary](#10-api-endpoints-summary)
* [11. Future Enhancements & Roadmap](#11-future-enhancements--roadmap)
* [12. License](#12-license)

---

## 3. Project Goals & Scope

* **Primary Goal:** To develop a functional, multi-user LMS demonstrating proficiency in full-stack web development using PHP and related technologies.
* **Educational Context:** Serves as a Major Project for academic evaluation, showcasing skills in database design, backend logic, frontend implementation, and user authentication.
* **Scope:** The project includes core LMS functionalities for content delivery (notes), assessment (assignments, submissions, grading), user tracking (attendance, marks), and basic system administration, tailored for student, teacher, and admin roles. Features like direct messaging and system reports are included conceptually or in basic form. Advanced features like real-time collaboration, complex quizzing, or SCORM compliance are outside the current scope.

---

## 4. Core Features

### 4.1. Public Access
* **Homepage:** Engaging entry point with hero section, welcome message, and featured course highlights.
* **Informational Pages:** About Us, Vision & Mission, Contact Us (with functional form handler structure), FAQ, Terms & Conditions, Privacy Policy.
* **Updates:** News/Announcements section displaying latest information.
* **Media:** Basic Gallery page structure.
* **Navigation:** User-friendly top navigation bar with clear Login/Register calls to action.

### 4.2. Authentication & User Management
* **Registration:** Students can self-register for accounts.
* **Login/Logout:** Secure session-based login and logout functionality.
* **Password Management:** Password Reset/Recovery mechanism structure.
* **Session Security:** Session regeneration on login, HttpOnly cookies.
* **Role-Based Access Control (RBAC):** Users are redirected to appropriate dashboards (Student, Teacher, Admin) upon login, and access to features is restricted based on their role (`require_role()` function).
* **Profile Management:** All users can view/edit basic profile information (name, phone, bio) and change their own password.

### 4.3. Student Portal
* **Dashboard:** Centralized view of enrolled subjects, upcoming assignment deadlines, recent grades/attendance summary (placeholders/basic implementation).
* **My Subjects:** List of subjects the student is currently enrolled in.
* **View Subject:** Access detailed view for a subject, including associated notes and assignments.
* **Notes:** View and download lecture notes/materials uploaded by teachers for enrolled subjects.
* **Assignments:** View list of assignments, details, instructions, due dates, and submission status.
* **Submit Assignment:** Functional file upload mechanism for submitting work.
* **My Marks:** View aggregate marks (assignment, midterm, final, total) and grades per subject.
* **My Attendance:** View personal attendance records (present/absent) per subject.
* **Class Schedule:** View personal timetable based on enrolled subjects.
* **Messages:** Placeholder interface for viewing messages (full functionality planned).

### 4.4. Teacher Portal
* **Dashboard:** Overview of assigned subjects, total students, count of new submissions, and today's schedule.
* **My Subjects:** List of subjects assigned to the teacher (via Course head or ClassSchedule).
* **Manage Subject:** Central hub for a specific subject, linking to notes, assignments, students, attendance, and marks management for that subject.
* **Notes Management:** Upload new lecture notes (with file validation) and manage (view/delete) existing notes.
* **Assignment Management:** Create new assignments (set title, description, due date, marks), view list of created assignments, see submission counts.
* **Submissions:** View list of student submissions for a specific assignment.
* **Grading:** Interface to view a student's submission file, enter marks, and provide feedback.
* **Attendance Management:** Mark daily attendance (present/absent with remarks) for enrolled students in a subject.
* **Marks Management:** Input/update aggregate marks (midterm, final - assignment marks often come from individual grading) for students in a subject.
* **Enrolled Students:** View a list of students enrolled across all assigned subjects.
* **My Schedule:** View personal teaching timetable.
* **Messages:** Placeholder interface.
* **Generate Reports:** Basic interface to generate Attendance and Marks reports (CSV download implemented) scoped to the teacher's subjects.

### 4.5. Admin Panel
* **Comprehensive Dashboard:** System-wide overview and analytics.
* **User Management:** Add/Edit/Delete users (all roles), manage roles, bulk operations.
* **Course & Subject Management:** Add/Edit/Delete courses (programs) and subjects (classes), assign teachers.
* **Content Moderation:** Oversee all notes, assignments, and submissions.
* **System Oversight:** Manage attendance & marks records, view audit logs.
* **Configuration:** Manage system settings, email settings, perform backups.
* **Reporting:** Generate system-wide reports (users, enrollment, etc.).

### 4.6. Common Features
* Profile viewing and editing.
* Password change functionality.
* Notification system (e.g., new grades, new assignments).
* Basic search functionality.
* Responsive design for accessibility on various devices.

---

## 5. Technology Stack & Architecture

### 5.1. Core Technologies
* **PHP (>= 7.4):** Server-side scripting language used for all backend logic, database interaction, and rendering HTML. A mix of procedural (helper functions) and Object-Oriented Programming (classes for core entities) is employed.
* **MySQL (>= 5.7):** Relational database used for storing all application data (users, courses, content, etc.). Interactions are handled via PHP's PDO extension for security and flexibility.
* **HTML5:** Standard markup language for structuring web pages.
* **CSS3:** Used for styling the application, primarily through the Bootstrap framework and custom stylesheets (`style.css`, `dashboard.css`, etc.).
* **JavaScript (ES6):** Used for client-side interactivity, form validation, AJAX requests (via Fetch API or jQuery), and dynamic updates (e.g., DataTables, Chart.js).

### 5.2. Frontend Libraries
* **Bootstrap (v5.3):** A popular CSS framework providing a responsive grid system, pre-styled components (buttons, forms, cards, modals, navbar, etc.), and utility classes. Used extensively for the UI.
* **jQuery (v3.x):** A JavaScript library simplifying DOM manipulation, event handling, and AJAX. Required by Bootstrap's JS components and DataTables.
* **DataTables:** A jQuery plugin enhancing standard HTML tables with features like sorting, filtering, pagination, and responsiveness. Used primarily in admin and teacher tables.
* **Chart.js:** A JavaScript library for creating various types of charts (bar, line, pie, etc.). Used for visualizing data on the admin analytics dashboard.
* **Font Awesome (v6):** An icon library used throughout the application for visual cues and button icons.

### 5.3. Architectural Approach
* **File-Based Structure:** The project uses a conventional file-based structure, separating concerns into different directories (e.g., `pages`, `dashboard`, `classes`, `includes`, `api`).
* **Procedural Core with OOP Elements:** Global functions (`functions.php`, `utils/`) handle common tasks like redirection, sanitization, and authentication checks. Core data entities (User, Course, Assignment, etc.) are managed through dedicated PHP classes in the `/classes/` directory, promoting better organization and reusability for database interactions.
* **Template Inclusion:** Reusable HTML components like headers, footers, sidebars, and navigation bars are stored in the `/includes/` directory and included in page scripts using `require_once`.
* **API Endpoints:** AJAX functionality is handled by dedicated PHP scripts in the `/api/` directory, which typically process requests and return JSON responses.
* **Configuration:** Centralized configuration (`config.php`) manages database credentials (ideally loaded from `.env`), base URL, and site settings.

---

## 6. Database Schema Overview

The database is designed to model the relationships between users, courses, subjects, content, and assessments. Key tables include:

* **`Users`**: Core table for all accounts (students, teachers, admins). Stores credentials, profile info, role, and status.
* **`Courses`**: Represents academic programs (e.g., "Computer Science"). Can have a head teacher assigned.
* **`Subjects`**: Represents individual classes or modules within a `Course` (e.g., "Web Development" within "Computer Science").
* **`Enrollments`**: Links students to the subjects they are taking.
* **`Assignments`**: Defines tasks given to students.
* **`Submissions`**: Stores student work submitted for assignments.
* **`Notes`**: Stores lecture notes and materials uploaded by teachers, linked to a `Subject`.
* **`Attendance`**: Records student presence for classes.
* **`Marks`**: Stores aggregate marks (midterm, final, total, grade) for students per subject.
* **`Settings`**: Stores key-value pairs for system configuration.
* **`Messages`**, **`Notifications`**, **`AuditLogs`**, **`ClassSchedule`**...

**Refer to `/migrations/create_tables.sql` for the complete DDL statements and relationships.**

---

## 7. Setup and Installation Guide

*(This section can reuse the detailed setup steps from the previous README response, ensuring clarity on Composer, `.env` vs `config.php`, web server document root, and permissions.)*

### 7.1. Prerequisites
* Web Server (Apache with `mod_rewrite`, or Nginx)
* PHP >= 7.4 (with PDO, mbstring, json extensions)
* MySQL >= 5.7
* Composer (Recommended for `.env` support)
* Git (for cloning)

### 7.2. Installation Steps
1.  **Clone:** `git clone https://github.com/vivek71092/kollege.git kollege-lms && cd kollege-lms`
2.  **Install Dependencies:** If using Composer, run `composer install`.
3.  **Database:** Create a MySQL database and user. Import `migrations/create_tables.sql` and optionally `migrations/seed_data_large_v2.sql`.
4.  **Configuration:** See section 7.3 below.
5.  **Web Server:** Configure document root to the project's **root directory**.
6.  **Permissions:** Ensure `/public/uploads/` and `/logs/` are writable by the web server.

### 7.3. Configuration
* **Recommended Method (`.env`):**
    * Copy `.env.example` to `.env`.
    * Edit `.env` with your `DB_*` credentials, `BASE_URL` (e.g., `https://kollege.ct.ws/`), and `ENVIRONMENT` (`development` or `production`).
    * Ensure `config.php` loads `.env` variables (requires `vlucas/phpdotenv`).
* **Alternative Method (`config.php`):**
    * Edit `config.php` directly.
    * Update the `define()` constants for `DB_*` and `BASE_URL`. **Use with caution regarding credentials in version control.**

### 7.4. Web Server Configuration
* **Apache:** Ensure `AllowOverride All` is set for the project directory so `.htaccess` can function. `mod_rewrite` should be enabled.
* **Nginx:** Use appropriate `try_files` directives to route requests through `index.php` if needed, and configure PHP-FPM processing. Add rules to deny access to sensitive files (`.env`, `.htaccess`, etc.).

### 7.5. Directory Permissions
* `/public/uploads/` and its subdirectories need write permissions for the web server user (e.g., `www-data`, `apache`). `chmod -R 775 public/uploads`.
* `/logs/` needs write permissions. `chmod -R 775 logs`.
* You might need `chown` depending on your server setup.

---

## 8. Usage & Default Credentials

* Access the application via the configured `BASE_URL`.
* **Default Logins (if seed data used):**
    * Admin: `admin@kollege.ct.ws` / `password`
    * Teacher: `teacher@kollege.ct.ws` / `password`
    * Student: `student@example.com` / `password`
* **⚠️ Change default passwords immediately after first login!**

---

## 9. Key Implementation Details

### 9.1. Security Measures
* **Password Hashing:** Uses PHP's `password_hash()` (BCRYPT).
* **SQL Injection Prevention:** Uses PDO prepared statements.
* **XSS Prevention:** Uses `htmlspecialchars()` on output. Basic input sanitization.
* **Session Security:** `session_regenerate_id()`, HttpOnly & Secure cookies, SameSite attribute.
* **File Uploads:** Validates types/sizes, unique filenames. Backups intended for non-webroot storage.
* **Access Control:** RBAC via `check_auth.php` and `require_role()`.
* **Error Handling:** Custom handlers prevent sensitive info leaks in production.

### 9.2. Performance Considerations
* **Database Indexing:** Assumed via `create_tables.sql`.
* **Pagination:** Client-side via DataTables; server-side needed for large scale.
* **Asset Loading:** CDNs used for common libraries.

### 9.3. User Experience (UX)
* **Responsiveness:** Bootstrap 5 for multiple device sizes.
* **Feedback:** Session flash messages used for action results.
* **Navigation:** Role-specific sidebars, breadcrumb context.

### 9.4. File Management Strategy
* **User Uploads (`/public/uploads/`):** Notes, Submissions stored here.
* **Profile Pictures (`/public/images/placeholders/profile/`):** User profile pics.
* **Static Assets (`/public/`):** CSS, JS, default images, logos.
* **Backups (`/../backups/` - intended):** Database backups stored securely.

### 9.5. Error Handling & Logging
* **`error_handler.php`:** Centralized error/exception handling.
* **Environment Control:** `ENVIRONMENT` (`development`/`production`) controls error display.
* **Logging:** All errors logged to `/logs/app_error.log`.

---

## 10. API Endpoints Summary

AJAX requests handled via scripts in `/api/`, returning JSON.

* `/users/`: Profile updates, password changes.
* `/courses/`: Enrollment actions.
* `/assignments/`: Submitting work, grading.
* `/attendance/`: Saving attendance.
* `/marks/`: Saving marks.
* `/notes/`: Deleting notes.
* `/messages/`, `/notifications/`: (Placeholder) Getting data, marking read.
* `/search.php`: (Placeholder) Search.

---

## 11. Future Enhancements & Roadmap

* Implement full Email Verification workflow.
* Complete implementation of Messaging and Notification systems.
* Implement robust CSRF protection on all state-changing forms.
* Add server-side pagination for large tables (Users, Logs, Submissions).
* Develop more detailed Admin analytics and reporting features (Chart.js integration).
* Implement functional Database Backup and Restore.
* Add rich text editors (e.g., TinyMCE) for content creation.
* Implement "Edit" functionality for Notes and Assignments.
* Refine search functionality.
* Consider refactoring towards a more formal MVC structure.
* Add unit/integration tests.

---

## 12. License 📄

This project is licensed under the **MIT License**. See the [LICENSE](LICENSE) file for details.
