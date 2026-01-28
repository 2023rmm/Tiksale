<?php
/**
 * HOME PAGE - Display Live Auctions
 */
require_once __DIR__ . '/../includes/session_init.php';
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

$user_id  = $_SESSION['user_id'] ?? null;
$username = $_SESSION['email'] ?? 'User';

// Get live auctions with product details
$query = "
SELECT 
    a.auction_id,
    a.product_id,
    a.starting_price,
    a.current_price,
    a.total_bids,
    a.end_time,

    p.name AS product_name,
    p.description,

    COALESCE(
        (SELECT image_url 
         FROM product_images 
         WHERE product_id = p.product_id 
         AND is_primary = 1 
         LIMIT 1),
        p.image_url
    ) AS image_url,

    u.username AS seller_name,

    TIMESTAMPDIFF(HOUR, NOW(), a.end_time) AS hours_remaining,
    TIMESTAMPDIFF(MINUTE, NOW(), a.end_time) AS minutes_remaining

FROM auctions a
JOIN products p ON a.product_id = p.product_id
JOIN users u ON p.seller_id = u.user_id

WHERE a.status = 'active'
AND a.end_time > NOW()

ORDER BY a.end_time ASC
";

$stmt = $db->prepare($query);
$stmt->execute();
$auctions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Live Auctions</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">
    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <?php include 'layout/header.php'; ?>
    <?php include 'layout/sidebar.php'; ?>
    
    <div class="main-content">
        <header class="page-header">
            <h1><i class="fas fa-fire"></i> Live Auctions</h1>
            <p>Browse and bid on active auctions</p>
        </header>

        <div class="content-wrapper">
            <?php if (empty($auctions)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h2>No Active Auctions</h2>
                    <p>There are currently no live auctions. Check back later!</p>
                </div>
            <?php else: ?>
                <div class="auctions-grid">
                    <?php foreach ($auctions as $auction): ?>
                        <div class="auction-card">
                            <div class="auction-image">
                                <?php if ($auction['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($auction['image_url']); ?>" alt="<?php echo htmlspecialchars($auction['product_name']); ?>">
                                <?php else: ?>
                                    <div class="placeholder-image">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="auction-badge">
                                    <?php 
                                    $hours = $auction['hours_remaining'];
                                    if ($hours < 1) {
                                        echo '<span class="badge-urgent">Ending Soon!</span>';
                                    } else if ($hours < 24) {
                                        echo '<span class="badge-hot">Hot</span>';
                                    } else {
                                        echo '<span class="badge-new">New</span>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="auction-details">
                                <h3 class="product-name"><?php echo htmlspecialchars($auction['product_name']); ?></h3>
                                <p class="product-desc"><?php echo substr(htmlspecialchars($auction['description']), 0, 100); ?>...</p>

                                <div class="auction-info-grid">
                                    <div class="info-item">
                                        <label><i class="fas fa-user"></i> Seller</label>
                                        <span><?php echo htmlspecialchars($auction['seller_name']); ?></span>
                                    </div>

                                    <div class="info-item">
                                        <label><i class="fas fa-users"></i> Total Bidders</label>
                                        <span><?php echo $auction['total_bids']; ?> bids</span>
                                    </div>

                                    <div class="info-item">
                                        <label><i class="fas fa-trophy"></i> Highest Bidder</label>
                                        <span>
                                            <?php 
                                            if ($auction['highest_bidder_name']) {
                                                echo htmlspecialchars($auction['highest_bidder_name']);
                                            } else {
                                                echo 'No bids yet';
                                            }
                                            ?>
                                        </span>
                                    </div>

                                    <div class="info-item">
                                        <label><i class="fas fa-clock"></i> Time Remaining</label>
                                        <span class="time-remaining">
                                            <?php 
                                         $hours = $auction['hours_remaining'];
                                            $minutes = $auction['minutes_remaining'] % 60;
                                            if ($hours > 24) {
                                                $days = floor($hours / 24);
                                                echo $days . ' days';
                                            } else if ($hours > 0) {
                                                echo $hours . 'h ' . $minutes . 'm';
                                            } else {
                                                echo $minutes . ' minutes';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="price-section">
                                    <div class="current-price">
                                        <label>Current Highest Bid</label>
                                        <span class="price">$<?php echo number_format($auction['current_bid'], 2); ?></span>
                                    </div>
                                    <div class="starting-price">
                                        <label>Starting Price</label>
                                        <span>$<?php echo number_format($auction['starting_bid'], 2); ?></span>
                                    </div>
                                </div>

                                <div class="auction-actions">
                                    <a href="view-auction.php?id=<?php echo $auction['auction_id']; ?>" class="btn btn-outline">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="place-bid.php?id=<?php echo $auction['auction_id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-gavel"></i> Place Bid
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
