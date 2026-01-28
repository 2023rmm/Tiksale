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

try {
    // Get all products posted by this user
    $query = "
        SELECT 
            p.id,
            p.name,
            p.description,
            p.condition,
            p.created_at,
            pi.image_url,
            a.status,
            a.starting_price,
            a.current_bid as current_price,
            a.start_date,
            a.end_date,
            COUNT(DISTINCT b.id) as bids,
            0 as views
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        LEFT JOIN auctions a ON p.id = a.product_id
        LEFT JOIN bids b ON a.id = b.auction_id
        WHERE p.seller_id = ?
        GROUP BY p.id
        ORDER BY p.created_at DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get statistics
    $statsQuery = "
        SELECT 
            COUNT(DISTINCT p.id) as total_products,
            COUNT(CASE WHEN a.status = 'active' THEN 1 END) as active_auctions,
            COALESCE(SUM(t.amount), 0) as total_revenue,
            COUNT(DISTINCT b.user_id) as total_bidders
        FROM products p
        LEFT JOIN auctions a ON p.id = a.product_id
        LEFT JOIN bids b ON a.id = b.auction_id
        LEFT JOIN transactions t ON a.id = t.auction_id AND t.status = 'completed'
        WHERE p.seller_id = ?
    ";
    
    $statsStmt = $pdo->prepare($statsQuery);
    $statsStmt->execute([$userId]);
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'stats' => $stats
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
