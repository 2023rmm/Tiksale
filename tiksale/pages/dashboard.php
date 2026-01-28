<?php
/**
 * Dashboard – after login
 */
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
$pdo = (new Database())->getConnection();

$userId = $_SESSION['user_id'];
$userName = !empty($_SESSION['full_name'])
    ? trim($_SESSION['full_name'])
    : (isset($_SESSION['email']) ? explode('@', $_SESSION['email'])[0] : 'User');

/**
 * =======================
 * LIVE AUCTIONS
 * =======================
 */
$liveQuery = "
    SELECT
        a.auction_id,
        a.product_id,
        a.starting_bid,
        a.current_bid,
        a.end_time,
        a.status,
        a.total_bids,

        p.product_name,
        p.description,
        p.seller_id,
        pi.image_url,

        u.full_name AS seller_name,

        TIMESTAMPDIFF(HOUR, NOW(), a.end_time) AS hours_remaining

    FROM auctions a
    JOIN products p ON a.product_id = p.product_id
    LEFT JOIN product_images pi ON pi.product_id = p.product_id AND pi.is_primary = 1
    JOIN users u ON p.seller_id = u.user_id
    WHERE a.status = 'active'
      AND a.end_time > NOW()
    ORDER BY a.end_time ASC
    LIMIT 24
";
$stmt = $pdo->prepare($liveQuery);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

/**
 * =======================
 * MY PRODUCTS (SELLER)
 * =======================
 */
$myProducts = [];
$myQuery = "
    SELECT
        p.product_id,
        p.product_name,
        p.description,
        p.status AS product_status,
        pi.image_url,

        a.auction_id,
        a.starting_bid,
        a.current_bid,
        a.end_time,
        a.status AS auction_status,
        a.total_bids,

        TIMESTAMPDIFF(HOUR, NOW(), a.end_time) AS hours_remaining

    FROM products p
    LEFT JOIN auctions a
        ON a.product_id = p.product_id
       AND a.status = 'active'
       AND a.end_time > NOW()
    LEFT JOIN product_images pi ON pi.product_id = p.product_id AND pi.is_primary = 1
    WHERE p.seller_id = ?
    ORDER BY p.created_at DESC
    LIMIT 12
";
$myStmt = $pdo->prepare($myQuery);
$myStmt->execute([$userId]);
$myProducts = $myStmt->fetchAll(PDO::FETCH_ASSOC);

function img_src($url) {
    if (empty($url)) {
        return '../assets/images/placeholder.jpg';
    }
    if (strpos($url, 'http') === 0) {
        return $url;
    }
    return '../uploads/' . $url;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – Tiksale</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="dashboard-body">

<div class="dashboard-container">
    <?php include __DIR__ . '/layout/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="dashboard-hero">
            <?php if (isset($_GET['upload']) && $_GET['upload'] == '1'): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Product uploaded successfully! It's live now.
                </div>
            <?php endif; ?>
            
            <h1 class="dashboard-welcome">
                <i class="fas fa-hand-wave"></i> Hi, <?php echo htmlspecialchars($userName); ?>!
            </h1>
            <p class="dashboard-tagline">Welcome to your auction dashboard</p>
        </div>

        <?php if (!empty($myProducts)): ?>
        <section class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-store"></i> Your Products
                </h2>
                <a href="my_shop.php" class="btn btn-outline">View All</a>
            </div>
        </section>
        <?php endif; ?>

        <section class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-fire"></i> Live Auctions
                </h2>
                <a href="home.php" class="btn btn-outline">View All</a>
            </div>
        </section>

        <!-- Quick Stats -->
        <section class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-icon" style="background: #e3f2fd;">
                    <i class="fas fa-gavel" style="color: #2196f3;"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo count($products); ?></h3>
                    <p>Active Auctions</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #e8f5e9;">
                    <i class="fas fa-box" style="color: #4caf50;"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo count($myProducts); ?></h3>
                    <p>Your Products</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #fff3e0;">
                    <i class="fas fa-fire" style="color: #ff9800;"></i>
                </div>
                <div class="stat-info">
                    <h3>New</h3>
                    <p>Activity</p>
                </div>
            </div>
        </section>
    </main>
</div>

<style>
.dashboard-container {
    display: flex;
    min-height: 100vh;
    background: #f5f7fa;
}

.main-content {
    flex: 1;
    margin-left: 260px;
    padding: 30px;
}

.dashboard-hero {
    margin-bottom: 40px;
}

.dashboard-welcome {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 10px;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.dashboard-tagline {
    font-size: 16px;
    color: #666;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.dashboard-section {
    margin-bottom: 50px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.section-title {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 25px;
}

.product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}

.product-card__img-wrap {
    position: relative;
    display: block;
    width: 100%;
    height: 220px;
    overflow: hidden;
}

.product-card__img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-success {
    background: #4caf50;
    color: white;
}

.product-card__body {
    padding: 15px;
}

.product-card__title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #2c3e50;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-card__desc {
    font-size: 14px;
    color: #666;
    margin-bottom: 10px;
    line-height: 1.4;
}

.product-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 12px;
    font-size: 13px;
    color: #666;
}

.product-card__meta .price {
    font-size: 18px;
    font-weight: 700;
    color: var(--primary-color, #667eea);
}

.product-card__meta span {
    display: flex;
    align-items: center;
    gap: 4px;
}

.product-card__seller {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #666;
    margin-bottom: 12px;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: var(--primary-color, #667eea);
    color: white;
}

.btn-primary:hover {
    background: var(--secondary-color, #764ba2);
    transform: translateY(-2px);
}

.btn-outline {
    background: white;
    color: var(--primary-color, #667eea);
    border: 2px solid var(--primary-color, #667eea);
}

.btn-outline:hover {
    background: var(--primary-color, #667eea);
    color: white;
}

.btn-sm {
    padding: 8px 16px;
    font-size: 14px;
    width: 100%;
    justify-content: center;
}

.dashboard-empty {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
}

.dashboard-empty i {
    font-size: 60px;
    color: #ddd;
    margin-bottom: 20px;
}

.dashboard-empty h3 {
    font-size: 24px;
    margin-bottom: 10px;
    color: #2c3e50;
}

.dashboard-empty p {
    color: #666;
    margin-bottom: 20px;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 20px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.stat-info h3 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 5px;
    color: #2c3e50;
}

.stat-info p {
    color: #666;
    font-size: 14px;
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 70px;
        padding: 20px;
    }
    
    .product-grid {
        grid-template-columns: 1fr;
    }
}
</style>

</body>
</html>
