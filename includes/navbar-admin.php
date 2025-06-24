<?php
// This file is deprecated - all admin pages now use sidebar.php
// Redirect to prevent 404 errors
if (file_exists('includes/sidebar.php')) {
    include 'includes/sidebar.php';
} else if (file_exists('../includes/sidebar.php')) {
    include '../includes/sidebar.php';
} else {
    echo '<!-- navbar-admin.php deprecated - using sidebar instead -->';
}
?>