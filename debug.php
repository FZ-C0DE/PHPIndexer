
<?php
// Debug script to identify errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug Information</h2>";

// Test basic PHP
echo "<p>✓ PHP is working</p>";

// Test file includes
echo "<p>Testing file includes...</p>";

try {
    require_once 'config/constants.php';
    echo "<p>✓ Constants loaded</p>";
} catch (Exception $e) {
    echo "<p>✗ Constants error: " . $e->getMessage() . "</p>";
}

try {
    require_once 'config/database.php';
    echo "<p>✓ Database connection established</p>";
} catch (Exception $e) {
    echo "<p>✗ Database error: " . $e->getMessage() . "</p>";
}

try {
    require_once 'config/functions.php';
    echo "<p>✓ Functions loaded</p>";
} catch (Exception $e) {
    echo "<p>✗ Functions error: " . $e->getMessage() . "</p>";
}

// Test session
session_start();
echo "<p>✓ Session started</p>";

echo "<p>Session data: " . print_r($_SESSION, true) . "</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Server info: " . print_r($_SERVER, true) . "</p>";
?>
