
<?php
// Database setup script for XAMPP
require_once 'config/constants.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Setup - School Management System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        .button { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0; }
    </style>
</head>
<body>";

echo "<h2>Database Setup untuk School Management System</h2>";

try {
    // Connect to MySQL without specifying database
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4", 
        DB_USER, 
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "<p class='success'>✓ Koneksi ke MySQL server berhasil</p>";

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p class='success'>✓ Database '" . DB_NAME . "' berhasil dibuat</p>";

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
                        echo "<p class='warning'>Warning: " . htmlspecialchars($e->getMessage()) . "</p>";
                    }
                }
            }
        }
        echo "<p class='success'>✓ Tabel database dan data sample berhasil diimport</p>";
    } else {
        echo "<p class='error'>✗ File SQL tidak ditemukan: " . $sqlFile . "</p>";
    }

    // Verify admin user exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    $stmt->execute();
    $adminCount = $stmt->fetchColumn();
    
    if ($adminCount > 0) {
        echo "<p class='success'>✓ Admin user sudah tersedia</p>";
    } else {
        echo "<p class='warning'>⚠ Admin user belum tersedia, akan dibuat sekarang...</p>";
        $pdo->exec("INSERT INTO users (username, email, password, role, full_name) VALUES 
                   ('admin', 'admin@school.edu', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'admin', 'Administrator')");
        echo "<p class='success'>✓ Admin user berhasil dibuat</p>";
    }

    echo "<h3 class='success'>Setup Database Selesai!</h3>";
    echo "<p>Sekarang Anda dapat mengakses aplikasi:</p>";
    echo "<ul>";
    echo "<li><a href='index.php' class='button'>Website Utama</a></li>";
    echo "<li><a href='auth/login.php' class='button'>Login Admin</a> (Username: admin, Password: password)</li>";
    echo "<li><a href='check_database.php' class='button'>Cek Koneksi Database</a></li>";
    echo "</ul>";

} catch(PDOException $e) {
    echo "<p class='error'>✗ Setup gagal: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Pastikan:</p>";
    echo "<ul>";
    echo "<li>XAMPP MySQL service sedang berjalan</li>";
    echo "<li>Kredensial MySQL benar di config/constants.php</li>";
    echo "<li>Port 3306 tidak diblokir firewall</li>";
    echo "</ul>";
}

echo "</body></html>";
?>
