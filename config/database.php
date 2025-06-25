
<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration for XAMPP
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
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
    
    // Test connection
    $pdo->query("SELECT 1");
    
} catch(PDOException $e) {
    // Log error and show user-friendly message
    error_log("Database connection failed: " . $e->getMessage());
    
    // Check if database exists
    try {
        $tempPdo = new PDO(
            "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4", 
            DB_USER, 
            DB_PASS
        );
        
        die("
        <h3>Database Setup Required</h3>
        <p>Database '" . DB_NAME . "' belum dibuat.</p>
        <p><a href='setup_database.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Setup Database Sekarang</a></p>
        <p>Atau jalankan SQL berikut di phpMyAdmin:</p>
        <code>CREATE DATABASE " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;</code>
        ");
        
    } catch(PDOException $e2) {
        die("
        <h3>MySQL Connection Error</h3>
        <p>Tidak dapat terhubung ke MySQL server.</p>
        <p>Pastikan:</p>
        <ul>
            <li>XAMPP MySQL service sedang berjalan</li>
            <li>Port 3306 tidak diblokir</li>
            <li>Username/password di config/constants.php benar</li>
        </ul>
        <p>Error: " . htmlspecialchars($e->getMessage()) . "</p>
        ");
    }
}
?>
