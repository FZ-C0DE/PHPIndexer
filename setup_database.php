
<?php
// Database setup script for XAMPP
require_once 'config/constants.php';

echo "<h2>Database Setup for School Management System</h2>";

try {
    // Connect to MySQL without specifying database
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4", 
        DB_USER, 
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color: green;'>✓ Database '" . DB_NAME . "' created successfully</p>";

    // Select the database
    $pdo->exec("USE " . DB_NAME);

    // Read and execute SQL file
    $sqlFile = __DIR__ . '/database/init.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        
        // Split SQL commands by semicolon and execute each
        $commands = explode(';', $sql);
        
        foreach ($commands as $command) {
            $command = trim($command);
            if (!empty($command)) {
                try {
                    $pdo->exec($command);
                } catch(PDOException $e) {
                    // Skip errors for existing tables/data
                    if (!strpos($e->getMessage(), 'already exists') && !strpos($e->getMessage(), 'Duplicate entry')) {
                        echo "<p style='color: orange;'>Warning: " . $e->getMessage() . "</p>";
                    }
                }
            }
        }
        echo "<p style='color: green;'>✓ Database tables and sample data imported successfully</p>";
    } else {
        echo "<p style='color: red;'>✗ SQL file not found: " . $sqlFile . "</p>";
    }

    echo "<h3>Setup Complete!</h3>";
    echo "<p>You can now access the application:</p>";
    echo "<ul>";
    echo "<li><a href='index.php'>Main Website</a></li>";
    echo "<li><a href='auth/login.php'>Admin Login</a> (Username: admin, Password: password)</li>";
    echo "</ul>";

} catch(PDOException $e) {
    echo "<p style='color: red;'>✗ Setup failed: " . $e->getMessage() . "</p>";
    echo "<p>Please make sure:</p>";
    echo "<ul>";
    echo "<li>XAMPP MySQL service is running</li>";
    echo "<li>MySQL credentials are correct in config/constants.php</li>";
    echo "</ul>";
}
?>
