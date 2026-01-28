<?php
require_once __DIR__ . '/../includes/session_init.php';
header('Content-Type: application/json');

require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$sellerId = $_SESSION['user_id'];
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

try {
    // Get orders for products sold by this user
    $query = "
        SELECT 
            t.id,
            t.amount as total_amount,
            t.status,
            t.created_at,
            t.tracking_number,
            p.name as product_name,
            pi.image_url as product_image,
            u.id as buyer_id,
            u.full_name as buyer_name,
            u.email as buyer_email,
            u.phone as buyer_phone,
            u.address as buyer_address,
            a.current_bid as winning_bid,
            a.end_date as auction_end_date
        FROM transactions t
        INNER JOIN auctions a ON t.auction_id = a.id
        INNER JOIN products p ON a.product_id = p.id
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        INNER JOIN users u ON t.buyer_id = u.id
        WHERE p.seller_id = ?
    ";
    
    if ($status !== 'all') {
        $query .= " AND t.status = ?";
    }
    
    $query .= " ORDER BY t.created_at DESC";
    
    $stmt = $pdo->prepare($query);
    
    if ($status !== 'all') {
        $stmt->execute([$sellerId, $status]);
    } else {
        $stmt->execute([$sellerId]);
    }
    
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get statistics
    $statsQuery = "
        SELECT 
            COUNT(CASE WHEN t.status = 'pending' THEN 1 END) as pending_orders,
            COUNT(CASE WHEN t.status = 'confirmed' THEN 1 END) as confirmed_orders,
            COUNT(CASE WHEN t.status = 'shipped' THEN 1 END) as shipped_orders,
            SUM(CASE WHEN t.status IN ('completed', 'shipped', 'confirmed') THEN t.amount ELSE 0 END) as total_earnings
        FROM transactions t
        INNER JOIN auctions a ON t.auction_id = a.id
        INNER JOIN products p ON a.product_id = p.id
        WHERE p.seller_id = ?
    ";
    
    $statsStmt = $pdo->prepare($statsQuery);
    $statsStmt->execute([$sellerId]);
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
