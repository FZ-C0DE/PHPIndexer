<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
try {
    require_once 'config/constants.php';
    require_once 'config/database.php';
    require_once 'config/functions.php';
} catch (Exception $e) {
    die("Error loading configuration: " . $e->getMessage() . "<br><a href='setup_database.php'>Setup Database</a>");
}

// Check if user is already logged in
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit();
    } elseif ($_SESSION['role'] === 'teacher') {
        header("Location: teacher/dashboard.php");
        exit();
    } elseif ($_SESSION['role'] === 'student') {
        header("Location: student/dashboard.php");
        exit();
    }
}

// Redirect to public homepage
header("Location: public/index.php");
exit();
?>