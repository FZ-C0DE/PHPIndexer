
<?php
// System status checker for XAMPP
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
    <title>System Check - School Management System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        .status-box { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>";

echo "<h2>System Status Check</h2>";

// Check PHP version
echo "<div class='status-box'>";
echo "<h3>PHP Configuration</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";

if (version_compare(phpversion(), '7.4.0', '>=')) {
    echo "<p class='success'>✓ PHP version is compatible</p>";
} else {
    echo "<p class='error'>✗ PHP version too old (requires 7.4+)</p>";
}

// Check required extensions
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'session'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='success'>✓ Extension $ext loaded</p>";
    } else {
        echo "<p class='error'>✗ Extension $ext missing</p>";
    }
}
echo "</div>";

// Check file permissions
echo "<div class='status-box'>";
echo "<h3>File Permissions</h3>";
$directories = ['uploads', 'uploads/students', 'uploads/teachers', 'uploads/gallery'];
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "<p class='success'>✓ Directory $dir is writable</p>";
        } else {
            echo "<p class='warning'>⚠ Directory $dir is not writable</p>";
        }
    } else {
        echo "<p class='warning'>⚠ Directory $dir does not exist</p>";
        // Try to create it
        if (mkdir($dir, 0755, true)) {
            echo "<p class='success'>✓ Created directory $dir</p>";
        }
    }
}
echo "</div>";

// Check database connection
echo "<div class='status-box'>";
echo "<h3>Database Connection</h3>";
try {
    require_once 'config/constants.php';
    
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
        DB_USER, 
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<p class='success'>✓ Database connection successful</p>";
    
    // Check tables
    $tables = ['users', 'teachers', 'students', 'classes', 'subjects'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "<p class='success'>✓ Table $table exists ($count records)</p>";
        } catch (Exception $e) {
            echo "<p class='error'>✗ Table $table missing or has error</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><a href='setup_database.php'>Run Database Setup</a></p>";
}
echo "</div>";

// Check configuration files
echo "<div class='status-box'>";
echo "<h3>Configuration Files</h3>";
$config_files = [
    'config/constants.php',
    'config/database.php', 
    'config/functions.php',
    'database/init.sql'
];

foreach ($config_files as $file) {
    if (file_exists($file)) {
        echo "<p class='success'>✓ $file exists</p>";
    } else {
        echo "<p class='error'>✗ $file missing</p>";
    }
}
echo "</div>";

echo "<h3>Quick Actions</h3>";
echo "<p><a href='setup_database.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Setup Database</a></p>";
echo "<p><a href='auth/login.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Login</a></p>";
echo "<p><a href='index.php' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>Main Website</a></p>";

echo "</body></html>";
?>
