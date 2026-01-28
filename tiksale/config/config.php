<?php
/**
 * Tiksale Auction System - Main Configuration
 */

// Start session if not started (uses project sessions dir)
require_once __DIR__ . '/../includes/session_init.php';

// Site Configuration
define('SITE_NAME', 'Tiksale Auction');
define('SITE_URL', 'http://localhost/tiksale');
define('BASE_PATH', dirname(__DIR__));

// Security Configuration
define('JWT_SECRET', 'your_secret_key_change_this_in_production_min_32_chars');
define('JWT_EXPIRE', 7 * 24 * 60 * 60); // 7 days in seconds
define('PASSWORD_SALT_ROUNDS', 12);

// Upload Configuration
define('UPLOAD_PATH', BASE_PATH . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Pagination
define('ITEMS_PER_PAGE', 12);

// Timezone
date_default_timezone_set('Africa/Nairobi');

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS Headers for API
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database
require_once __DIR__ . '/database.php';

// Helper function to send JSON response
function sendJSON($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

// Helper function to get JSON input
function getJSONInput() {
    return json_decode(file_get_contents('php://input'), true);
}

// Helper function to validate required fields
function validateRequired($data, $fields) {
    $missing = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $missing[] = $field;
        }
    }
    return $missing;
}
?>
