<?php
// Database configuration for MySQL (XAMPP)
require_once __DIR__ . '/constants.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
        DB_USER, 
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    // Display detailed error for debugging in XAMPP
    die("Database connection failed: " . $e->getMessage() . "<br>Please make sure:<br>1. XAMPP MySQL service is running<br>2. Database 'school_management' exists<br>3. Import the SQL file from database/init.sql");
}
?>