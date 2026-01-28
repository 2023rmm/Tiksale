<?php
require_once __DIR__ . '/../includes/session_init.php';
header('Content-Type: application/json');

require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$userId = $_SESSION['user_id'];
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

try {
    // Build query based on status filter
    $query = "
        SELECT 
            t.id,
            t.amount as total_amount,
            t.status,
            t.created_at,
            p.name as product_name,
            pi.image_url as product_image,
            u.full_name as seller_name,
            a.current_bid as winning_bid,
            a.end_date as auction_end_date
        FROM transactions t
        INNER JOIN auctions a ON t.auction_id = a.id
        INNER JOIN products p ON a.product_id = p.id
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        INNER JOIN users u ON p.seller_id = u.id
        WHERE t.buyer_id = ?
    ";
    
    if ($status !== 'all') {
        $query .= " AND t.status = ?";
    }
    
    $query .= " ORDER BY t.created_at DESC";
    
    $stmt = $pdo->prepare($query);
    
    if ($status !== 'all') {
        $stmt->execute([$userId, $status]);
    } else {
        $stmt->execute([$userId]);
    }
    
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get order statistics
    $statsQuery = "
        SELECT 
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
            COUNT(CASE WHEN status = 'paid' THEN 1 END) as paid_count,
            COUNT(CASE WHEN status = 'shipped' THEN 1 END) as shipped_count,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_count,
            SUM(amount) as total_spent
        FROM transactions
        WHERE buyer_id = ?
    ";
    
    $statsStmt = $pdo->prepare($statsQuery);
    $statsStmt->execute([$userId]);
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'orders' => $orders,
        'stats' => $stats
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
