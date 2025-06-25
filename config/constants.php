
<?php
// Database Configuration for XAMPP
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'school_management');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application Configuration
define('APP_NAME', 'Sistem Manajemen Sekolah');
define('SITE_NAME', 'Sistem Manajemen Sekolah');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/projectAi/PHPIndexer');
define('BASE_URL', 'http://localhost/projectAi/PHPIndexer');

// Path Configuration for XAMPP
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/projectAi/PHPIndexer');
define('UPLOAD_PATH', BASE_PATH . '/uploads/');
define('ASSETS_PATH', BASE_PATH . '/assets/');

// Upload Configuration
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_DOCUMENT_TYPES', ['pdf', 'doc', 'docx', 'xls', 'xlsx']);

// Security Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 6);
define('MAX_LOGIN_ATTEMPTS', 5);

// Pagination Configuration
define('RECORDS_PER_PAGE', 25);
define('PAGINATION_LINKS', 5);

// Academic Year Configuration
define('CURRENT_ACADEMIC_YEAR', '2024/2025');
define('CURRENT_SEMESTER', 'Ganjil');

// Default Values
define('DEFAULT_STUDENT_STATUS', 'active');
define('DEFAULT_TEACHER_STATUS', 'active');
define('DEFAULT_CLASS_CAPACITY', 30);
define('DEFAULT_SUBJECT_CREDIT_HOURS', 2);
?>
