<?php
declare(strict_types=1);

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/session_init.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

try {
    // Validate inputs
    if (
        empty($_POST['name']) ||
        empty($_POST['description']) ||
        empty($_POST['starting_price']) ||
        empty($_POST['duration_days'])
    ) {
        throw new Exception('All fields are required');
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Image upload failed');
    }

    $userId = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $startingPrice = (float) $_POST['starting_price'];
    $durationDays = (int) $_POST['duration_days'];

    // Upload image
    $uploadDir = __DIR__ . '/../uploads/products/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('product_', true) . '.' . $ext;
    $targetPath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        throw new Exception('Failed to save uploaded image');
    }

    $imagePath = 'uploads/products/' . $fileName;

    // Calculate auction end date
    $endDate = date('Y-m-d H:i:s', strtotime("+{$durationDays} days"));

    // Insert into DB
    $stmt = $pdo->prepare("
        INSERT INTO products 
        (user_id, name, description, starting_price, current_price, image_url, auction_end, status, created_at)
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, 'active', NOW())
    ");

    $stmt->execute([
        $userId,
        $name,
        $description,
        $startingPrice,
        $startingPrice,
        $imagePath,
        $endDate
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Product uploaded successfully'
    ]);
    exit;

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
