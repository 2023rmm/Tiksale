<?php
/**
 * User Registration API
 * Handles new user registration
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../config/database.php';

// Start session
require_once __DIR__ . '/../includes/session_init.php';

// Get posted data
$data = [
    'full_name' => $_POST['full_name'] ?? '',
    'username' => $_POST['username'] ?? '',
    'email' => $_POST['email'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'country' => $_POST['country'] ?? '',
    'user_type' => $_POST['user_type'] ?? 'buyer',
    'password' => $_POST['password'] ?? ''
];

// Validate required fields
if (empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['full_name'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please fill in all required fields'
    ]);
    exit;
}

// Validate email format
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email format'
    ]);
    exit;
}

// Validate password strength
if (strlen($data['password']) < 8) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must be at least 8 characters long'
    ]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check if username already exists
    $check_username = $db->prepare("SELECT user_id FROM users WHERE username = ?");
    $check_username->execute([$data['username']]);
    
    if ($check_username->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Username already exists'
        ]);
        exit;
    }

    // Check if email already exists
    $check_email = $db->prepare("SELECT user_id FROM users WHERE email = ?");
    $check_email->execute([$data['email']]);
    
    if ($check_email->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Email already registered'
        ]);
        exit;
    }

    // Hash password
    $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);

    // Insert new user
    $query = "INSERT INTO users (username, email, password_hash, full_name, phone, country, user_type) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    $result = $stmt->execute([
        $data['username'],
        $data['email'],
        $password_hash,
        $data['full_name'],
        $data['phone'],
        $data['country'],
        $data['user_type']
    ]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully! Please login to continue.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Registration failed. Please try again.'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
