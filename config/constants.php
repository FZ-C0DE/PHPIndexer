<?php
// Application constants
define('APP_NAME', 'SMA Negeri 1 Jakarta');
define('APP_VERSION', '1.0.0');
define('BASE_PATH', dirname(__DIR__));

// Database constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'school_management');
define('DB_USER', 'root');
define('DB_PASS', '');

// File upload constants
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// Academic constants
define('CURRENT_ACADEMIC_YEAR', '2024/2025');
define('CURRENT_SEMESTER', 'Ganjil');

// Pagination constants
define('ITEMS_PER_PAGE', 25);

// Session timeout (in seconds)
define('SESSION_TIMEOUT', 3600); // 1 hour

// Default timezone
date_default_timezone_set('Asia/Jakarta');
?>