<?php
// /index.php

// Load core configuration and functions
// config.php should handle session_start()
require_once 'config.php';
require_once 'functions.php';

// Set the page title for the header
$page_title = "Welcome to " . SITE_NAME;

// Include the shared header template
require_once 'includes/header.php';

// --- Include the main content for the homepage ---
// Wrap content in a <main> tag for semantics and flexbox footer
echo '<main>';
require_once 'pages/home.php';
echo '</main>';
// --- End main content ---

// Include the shared footer template
require_once 'includes/footer.php';

?>