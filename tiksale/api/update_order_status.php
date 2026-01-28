<?php
require_once __DIR__ . '/../includes/session_init.php';
header('Content-Type: application/json');

require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $orderId = $input['order_id'] ?? null;
    $newStatus = $input['status'] ?? null;
    $trackingNumber = $input['tracking_number'] ?? null;
    
    if (!$orderId || !$newStatus) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }
    
    // Verify order belongs to seller
    $verifyQuery = "
        SELECT t.id 
        FROM transactions t
        INNER JOIN auctions a ON t.auction_id = a.id
        INNER JOIN products p ON a.product_id = p.id
        WHERE t.id = ? AND p.seller_id = ?
    ";
    
    $verifyStmt = $pdo->prepare($verifyQuery);
    $verifyStmt->execute([$orderId, $_SESSION['user_id']]);
    
    if ($verifyStmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
        exit();
    }
    
    // Update order status
    $updateQuery = "UPDATE transactions SET status = ?, updated_at = NOW()";
    $params = [$newStatus];
    
    if ($trackingNumber) {
        $updateQuery .= ", tracking_number = ?";
        $params[] = $trackingNumber;
    }
    
    $updateQuery .= " WHERE id = ?";
    $params[] = $orderId;
    
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute($params);
    
    // Log activity
    $logQuery = "
        INSERT INTO activity_logs (user_id, action, details, created_at)
        VALUES (?, 'order_status_updated', ?, NOW())
    ";
    
    $logStmt = $pdo->prepare($logQuery);
    $logStmt->execute([
        $_SESSION['user_id'],
        json_encode(['order_id' => $orderId, 'new_status' => $newStatus])
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Order status updated successfully'
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
