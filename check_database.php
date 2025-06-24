
<?php
// Database connection checker for XAMPP
require_once 'config/constants.php';

echo "<h2>Database Connection Check</h2>";

try {
    // Test connection to MySQL server
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4", 
        DB_USER, 
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "<p style='color: green;'>✓ MySQL connection successful</p>";

    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Database '" . DB_NAME . "' exists</p>";
        
        // Select database and check tables
        $pdo->exec("USE " . DB_NAME);
        
        // Check for required tables
        $required_tables = ['users', 'teachers', 'students', 'classes'];
        
        foreach ($required_tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<p style='color: green;'>✓ Table '$table' exists</p>";
                
                // Count records
                if ($table == 'teachers') {
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                    $count = $stmt->fetch()['count'];
                    echo "<p style='color: blue;'>→ Total records in $table: $count</p>";
                }
            } else {
                echo "<p style='color: red;'>✗ Table '$table' missing</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>✗ Database '" . DB_NAME . "' does not exist</p>";
        echo "<p><a href='setup_database.php'>Click here to setup database</a></p>";
    }

} catch(PDOException $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>XAMPP MySQL service is running</li>";
    echo "<li>Database credentials in config/constants.php</li>";
    echo "<li>Database '" . DB_NAME . "' exists</li>";
    echo "</ul>";
}
?>
