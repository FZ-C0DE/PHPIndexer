
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Debug Dashboard</h3>";

// Check if files exist
$files_to_check = [
    '../includes/auth-check.php',
    '../config/database.php',
    '../config/constants.php',
    'includes/header.php',
    'includes/sidebar.php',
    'includes/footer.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ File exists: $file</p>";
    } else {
        echo "<p style='color: red;'>✗ File missing: $file</p>";
    }
}

// Check database connection
try {
    require_once '../config/database.php';
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Check session
session_start();
if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✓ User logged in: " . $_SESSION['username'] . "</p>";
} else {
    echo "<p style='color: red;'>✗ User not logged in</p>";
}
?>
