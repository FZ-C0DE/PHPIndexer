<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/functions.php';

// Check session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    // Session expired
    session_unset();
    session_destroy();
    redirect('../auth/login.php?error=session_expired');
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../auth/login.php');
}

// Update user's last seen timestamp
if (isset($_SESSION['user_id'])) {
    try {
        require_once __DIR__ . '/../config/database.php';
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } catch(PDOException $e) {
        // Continue silently if update fails
    }
}

// Check if user has student role
function isStudent() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

// Redirect based on role if accessing wrong dashboard
function checkRoleAccess() {
    if (!isLoggedIn()) {
        return;
    }

    $current_path = $_SERVER['REQUEST_URI'];

    // Check if admin trying to access other dashboards
    if ($_SESSION['role'] === 'admin' && (strpos($current_path, '/student/') !== false || strpos($current_path, '/teacher/') !== false)) {
        header("Location: ../admin/dashboard.php");
        exit();
    }

    // Check if teacher trying to access admin or student dashboards
    if ($_SESSION['role'] === 'teacher' && (strpos($current_path, '/admin/') !== false || strpos($current_path, '/student/') !== false)) {
        header("Location: ../teacher/dashboard.php");
        exit();
    }

    // Check if student trying to access admin or teacher dashboards
    if ($_SESSION['role'] === 'student' && (strpos($current_path, '/admin/') !== false || strpos($current_path, '/teacher/') !== false)) {
        header("Location: ../student/dashboard.php");
        exit();
    }
}

// Call the function
checkRoleAccess();
?>