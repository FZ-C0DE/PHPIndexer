<?php
require_once 'config/constants.php';
require_once 'config/database.php';

// Check if user is already logged in
session_start();
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit();
    } elseif ($_SESSION['role'] === 'teacher') {
        header("Location: teacher/dashboard.php");
        exit();
    }
}

// Redirect to public homepage
header("Location: public/index.php");
exit();
?>