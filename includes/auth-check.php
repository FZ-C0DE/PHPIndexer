<?php
// Authentication check
require_once __DIR__ . '/../config/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_PATH . "/auth/login.php");
    exit();
}

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    header("Location: " . BASE_PATH . "/auth/login.php?timeout=1");
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Function to check user role
function requireRole($required_role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $required_role) {
        header("Location: " . BASE_PATH . "/auth/unauthorized.php");
        exit();
    }
}

// Function to check if user has admin role
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Function to check if user has teacher role
function isTeacher() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'teacher';
}
?>