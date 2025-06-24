<?php
/**
 * Common utility functions for the School Management System
 */

/**
 * Sanitize input data
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Redirect to a specific URL
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user has specific role
 */
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Require user to be logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('../auth/login.php');
    }
}

/**
 * Require user to have specific role
 */
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        redirect('../auth/login.php?error=unauthorized');
    }
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '-';
    return date($format, strtotime($date));
}

/**
 * Format currency (Indonesian Rupiah)
 */
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Generate random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (Indonesian format)
 */
function isValidPhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return preg_match('/^(08|62|8)[0-9]{8,13}$/', $phone);
}

/**
 * Upload file with validation
 */
function uploadFile($file, $destination, $allowedTypes = null, $maxSize = null) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }
    
    $allowedTypes = $allowedTypes ?: ALLOWED_IMAGE_TYPES;
    $maxSize = $maxSize ?: MAX_FILE_SIZE;
    
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmp = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Validate file type
    if (!in_array($fileExt, $allowedTypes)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }
    
    // Validate file size
    if ($fileSize > $maxSize) {
        return ['success' => false, 'message' => 'File size too large'];
    }
    
    // Generate unique filename
    $newFileName = uniqid() . '_' . time() . '.' . $fileExt;
    $uploadPath = $destination . '/' . $newFileName;
    
    // Create directory if it doesn't exist
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($fileTmp, $uploadPath)) {
        return ['success' => true, 'filename' => $newFileName, 'path' => $uploadPath];
    } else {
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }
}

/**
 * Log system activity
 */
function logActivity($action, $description = '', $user_id = null) {
    global $pdo;
    
    $user_id = $user_id ?: ($_SESSION['user_id'] ?? null);
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$user_id, $action, $description, $ip_address, $user_agent]);
    } catch(PDOException $e) {
        // Log error but don't stop execution
        error_log("Failed to log activity: " . $e->getMessage());
    }
}

/**
 * Send notification (can be extended for email, SMS, etc.)
 */
function sendNotification($type, $recipient, $subject, $message) {
    // This is a placeholder for notification system
    // Can be extended to support email, SMS, push notifications, etc.
    
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO notifications (type, recipient, subject, message, status, created_at) 
            VALUES (?, ?, ?, ?, 'pending', NOW())
        ");
        $stmt->execute([$type, $recipient, $subject, $message]);
        return true;
    } catch(PDOException $e) {
        error_log("Failed to send notification: " . $e->getMessage());
        return false;
    }
}

/**
 * Calculate age from birth date
 */
function calculateAge($birthDate) {
    if (empty($birthDate)) return 0;
    
    $birth = new DateTime($birthDate);
    $today = new DateTime();
    return $birth->diff($today)->y;
}

/**
 * Generate academic year options
 */
function getAcademicYearOptions($currentYear = null) {
    $currentYear = $currentYear ?: date('Y');
    $options = [];
    
    for ($i = -2; $i <= 2; $i++) {
        $year = $currentYear + $i;
        $nextYear = $year + 1;
        $options[] = "$year/$nextYear";
    }
    
    return $options;
}

/**
 * Check if current time is within working hours
 */
function isWorkingHours($start = '07:00', $end = '17:00') {
    $currentTime = date('H:i');
    return ($currentTime >= $start && $currentTime <= $end);
}

/**
 * Generate breadcrumb navigation
 */
function generateBreadcrumb($items) {
    $breadcrumb = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    $total = count($items);
    foreach ($items as $index => $item) {
        if ($index === $total - 1) {
            // Last item (current page)
            $breadcrumb .= '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($item['text']) . '</li>';
        } else {
            // Navigation items
            if (isset($item['url'])) {
                $breadcrumb .= '<li class="breadcrumb-item"><a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['text']) . '</a></li>';
            } else {
                $breadcrumb .= '<li class="breadcrumb-item">' . htmlspecialchars($item['text']) . '</li>';
            }
        }
    }
    
    $breadcrumb .= '</ol></nav>';
    return $breadcrumb;
}
?>