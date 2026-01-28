<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
require_once __DIR__ . '/../includes/session_init.php';
require_once __DIR__ . '/../config/database.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Missing credentials']);
    exit;
}

try {
    $db = (new Database())->getConnection();

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }

    session_regenerate_id(true);

    // ✅ FIXED SESSION VALUES
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id']   = $user['user_id'];   // ✅ CORRECT
    $_SESSION['email']     = $user['email'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['user_type'] = $user['user_type'];

    echo json_encode([
        'success'  => true,
        'redirect' => '../pages/dashboard.php'
    ]);

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error'
    ]);
}
