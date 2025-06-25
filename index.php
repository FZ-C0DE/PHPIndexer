
<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Include required files
try {
    require_once 'config/constants.php';
    require_once 'config/database.php';
    require_once 'config/functions.php';
} catch (Exception $e) {
    die("Error loading configuration: " . $e->getMessage() . "<br><a href='setup_database.php'>Setup Database</a>");
}

// Get the current path
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Remove base path for XAMPP
$basePath = '/projectAi/PHPIndexer';
if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Remove leading slash
$path = ltrim($path, '/');

// Handle routing
switch ($path) {
    case '':
    case 'index.php':
        require_once 'public/index.php';
        break;
        
    case 'about':
    case 'about.php':
        require_once 'public/about.php';
        break;
        
    case 'contact':
    case 'contact.php':
        require_once 'public/contact.php';
        break;
        
    case 'gallery':
    case 'gallery.php':
        require_once 'public/gallery.php';
        break;
        
    default:
        // Check if it's a direct file access
        if (file_exists($path) && is_file($path)) {
            require_once $path;
        } else {
            // Show 404 for unknown routes
            http_response_code(404);
            require_once '404.php';
        }
        break;
}
?>
