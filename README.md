# Kollege LMS - PHP Learning Management System

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT) **Kollege LMS** is a full-stack Learning Management System built with PHP, MySQL, HTML, CSS, and JavaScript. It provides features for students, teachers, and administrators to manage courses, assignments, attendance, grades, and communication within an educational institution.

**Note:** This project was developed as an academic requirement (Major Project) for the 6th semester.

**Live Demo (if applicable):** [https://kollege.ct.ws/](https://kollege.ct.ws/)

---

## Features ✨

* **Public Portal:** Homepage, About, Contact, Announcements, etc.
* **Secure Authentication:** Login, Student Self-Registration, Password Reset.
* **Role-Based Access Control:** Separate dashboards and permissions for Admins, Teachers, and Students.
* **Student Dashboard:**
    * View Enrolled Subjects & Details
    * Access Notes & Materials
    * View & Submit Assignments
    * Check Grades & Attendance
    * View Class Schedule
* **Teacher Dashboard:**
    * Manage Assigned Subjects
    * Upload/Manage Notes
    * Create/Manage Assignments & View Submissions
    * Grade Submissions
    * Mark & Manage Attendance
    * Input & Manage Marks
    * View Enrolled Students
    * Manage Schedule & Generate Reports
* **Admin Dashboard:**
    * System Analytics Overview
    * Full User Management (CRUD, Roles)
    * Course (Program) & Subject Management
    * Content Moderation (Notes, Assignments)
    * View System-wide Attendance & Marks
    * Manage Announcements
    * Generate System Reports (Users, Enrollment, etc.)
    * View Audit Logs
    * Configure System Settings

---

## Technology Stack 💻

* **Backend:** PHP 7.4+ (Procedural & OOP)
* **Database:** MySQL 5.7+
* **Frontend:** HTML5, CSS3, JavaScript (ES6)
* **UI Framework:** Bootstrap 5
* **JavaScript Libraries:** jQuery, DataTables, Chart.js
* **Server:** Apache (mod_rewrite enabled), Nginx compatible
* **Dependencies:** (Optional: Mention Composer if used, e.g., for phpdotenv)

---

## Project Structure 📁

/
│
├── .htaccess           # Apache config
├── .env.example        # Environment variables template
├── README.md           # This file
├── LICENSE             # License file (e.g., MIT)
│
├── index.php           # Main entry point (Homepage)
├── config.php          # Core configuration, DB connection
├── functions.php       # Global helper functions
├── error_handler.php   # Custom error handling
│
├── api/                # PHP scripts handling AJAX requests (return JSON)
│   ├── attendance/
│   ├── assignments/
│   ├── courses/
│   ├── marks/
│   ├── messages/
│   ├── notes/
│   ├── notifications/
│   ├── users/
│   └── search.php
│
├── auth/               # Authentication pages (login, register, logout, etc.)
│   ├── check_auth.php
│   ├── forgot-password.php
│   ├── login.php
│   ├── logout.php
│   ├── register.php
│   ├── reset-password.php
│   └── verify-email.php
│
├── classes/            # PHP Class definitions (OOP structure)
│   ├── Authentication.php
│   ├── Assignment.php
│   ├── Attendance.php
│   ├── Course.php
│   ├── Database.php
│   ├── Email.php
│   ├── FileHandler.php
│   ├── Logger.php
│   ├── Marks.php
│   └── User.php
│
├── dashboard/          # Authenticated user dashboards
│   ├── index.php       # Role-based redirector
│   ├── admin/
│   │   ├── analytics/
│   │   ├── announcements/
│   │   ├── attendance-marks/
│   │   ├── content/
│   │   ├── courses/
│   │   ├── reports/
│   │   ├── settings/
│   │   ├── subjects/
│   │   ├── users/
│   │   ├── audit-logs.php
│   │   ├── dashboard.php
│   │   ├── profile.php
│   │   └── settings.php
│   ├── student/
│   │   ├── assignments.php
│   │   ├── attendance.php
│   │   ├── courses.php
│   │   ├── dashboard.php
│   │   ├── marks.php
│   │   ├── messages.php
│   │   ├── notes.php
│   │   ├── profile.php
│   │   ├── schedule.php
│   │   ├── settings.php
│   │   ├── submit-assignment.php
│   │   ├── view-assignment.php
│   │   └── view-course.php
│   └── teacher/
│       ├── courses.php
│       ├── create-assignment.php
│       ├── dashboard.php
│       ├── grade-assignment.php
│       ├── manage-assignments.php
│       ├── manage-course.php
│       ├── manage-marks.php
│       ├── manage-notes.php
│       ├── mark-attendance.php
│       ├── messages.php
│       ├── profile.php
│       ├── reports.php
│       ├── schedule.php
│       ├── settings.php
│       ├── students.php
│       ├── upload-notes.php
│       └── view-submissions.php
│
├── includes/           # Reusable PHP components
│   ├── breadcrumb.php
│   ├── footer.php
│   ├── header.php
│   ├── modals.php
│   ├── navbar.php
│   └── sidebar.php
│
├── migrations/         # Database setup files
│   ├── create_tables.sql
│   └── seed_data_large_v2.sql # Or seed_data.sql
│
├── pages/              # Publicly accessible static pages
│   ├── 404.php
│   ├── about.php
│   ├── announcements.php
│   ├── contact.php
│   ├── faq.php
│   ├── gallery.php
│   ├── home.php
│   ├── privacy.php
│   ├── terms.php
│   └── vision-mission.php
│
├── public/             # Web-accessible assets (CSS, JS, images, uploads)
│   ├── css/
│   │   ├── admin.css
│   │   ├── dashboard.css
│   │   ├── responsive.css
│   │   └── style.css
│   ├── images/
│   │   ├── banners/
│   │   ├── icons/
│   │   ├── logo/
│   │   └── placeholders/ # Includes profile/, course images etc.
│   ├── js/
│   │   ├── admin.js
│   │   ├── confirm-modal.js
│   │   ├── dashboard.js
│   │   ├── main.js
│   │   └── validation.js
│   └── uploads/        # User-uploaded files (notes, assignments, etc.)
│       ├── assignments/
│       ├── certificates/
│       ├── notes/
│       ├── profile_images/ # Or handled via images/placeholders/profile/
│       └── submissions/
│
├── utils/              # Utility functions and helpers
│   ├── constants.php
│   ├── date-formatter.php
│   ├── email-templates.php
│   ├── helpers.php
│   └── validators.php
│
└── logs/               # Application log files (should be in .gitignore)
    └── app_error.log

---

## Setup & Installation 🚀

1.  **Clone Repository:**
    ```bash
    git clone [https://github.com/vivek71092/kollege.git](https://github.com/vivek71092/kollege.git) your-directory-name
    cd your-directory-name
    ```
2.  **Database:**
    * Create a MySQL database (e.g., `if0_40212246_kollege`).
    * Import the schema: `mysql -u YOUR_USER -p YOUR_DB_NAME < migrations/create_tables.sql`
    * (Optional) Import seed data: `mysql -u YOUR_USER -p YOUR_DB_NAME < migrations/seed_data_large_v2.sql`
3.  **Configuration:**
    * Copy `.env.example` to `.env`: `cp .env.example .env`
    * Edit `.env` and fill in your correct `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`, `DB_NAME`.
    * **Crucially, set `BASE_URL`** to your project's root URL (e.g., `http://localhost/kollege/` or `https://kollege.ct.ws/`). **Include the trailing slash!**
    * Set `ENVIRONMENT` to `development` or `production`.
    * *(Optional: Install phpdotenv via Composer: `composer require vlucas/phpdotenv` and ensure `config.php` loads it)*
4.  **Web Server:**
    * Point your web server's document root to the project's **root directory**.
    * Ensure `mod_rewrite` is enabled for Apache.
5.  **Permissions:**
    * Make sure the web server has **write permissions** for the `/public/uploads/` directory and its subdirectories (`notes`, `assignments`, `submissions`, `images/placeholders/profile`).
    * Make sure the web server has **write permissions** for the `/logs/` directory.

---

## Usage

* Navigate to the `BASE_URL` in your browser.
* **Default Credentials (if using seed data):**
    * **Admin:** `admin@kollege.ct.ws` / `password`
    * **Teacher:** `teacher1@kollege.ct.ws` / `password`
    * **Student:** `student1@example.com` / `password`
    * *(Change default passwords immediately!)*

---

## License 📄

This project is licensed under the **MIT License**. See the [LICENSE](LICENSE) file for details.
