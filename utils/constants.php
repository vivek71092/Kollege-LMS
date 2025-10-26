<?php
// /utils/constants.php

// User Roles (consistent naming)
define('ROLE_STUDENT', 'student');
define('ROLE_TEACHER', 'teacher');
define('ROLE_ADMIN', 'admin');

// Statuses
define('STATUS_ACTIVE', 'active');
define('STATUS_PENDING', 'pending');
define('STATUS_SUSPENDED', 'suspended');

// Default items per page for pagination
define('DEFAULT_PER_PAGE', 15);

// File Upload Settings (Can override defaults in FileHandler class if needed)
define('MAX_UPLOAD_SIZE_MB', 10); // Max size in Megabytes
define('ALLOWED_NOTE_TYPES', ['application/pdf', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'application/zip']);
define('ALLOWED_SUBMISSION_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'text/plain', 'application/sql']);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

?>