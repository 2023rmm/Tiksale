<?php
require_once __DIR__ . '/../includes/session_init.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<?php include __DIR__ . '/layout/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - Tiksale</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Dashboard Styles ONLY -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <!-- Icons & Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 10px 18px;
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: .3s;
        }

        .filter-tab.active,
        .filter-tab:hover {
            background: #6366f1;
            color: #fff;
            border-color: #6366f1;
        }

        .orders-container {
            display: grid;
            gap: 20px;
        }

        .order-card {
            background: #fff;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 4px 12px rgba(0,0,0,.06);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
        }

        .order-id {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }

        .order-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-paid { background: #d1ecf1; color: #0c5460; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-completed { background: #16a34a; color: #fff; }

        .order-content {
            display: flex;
            gap: 20px;
        }

        .order-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 10px;
        }

        .order-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .order-info {
            display: grid;
            gap: 8px;
            font-size: 14px;
            color: #475569;
        }

        .order-price {
            font-size: 22px;
            font-weight: 700;
            color: #6366f1;
            margin: 12px 0;
        }

        .order-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-small {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: .3s;
        }

        .btn-primary {
            background: #6366f1;
            color: #fff;
        }

        .btn-outline {
            background: #fff;
            border: 2px solid #6366f1;
            color: #6366f1;
        }

        .btn-outline:hover {
            background: #6366f1;
            color: #fff;
        }

        .empty-state {
            background: #fff;
            border-radius: 16px;
            padding: 60px 20px;
            text-align: center;
        }

        .empty-state i {
            font-size: 70px;
            color: #cbd5f5;
            margin-bottom: 20px;
        }

        .loading-spinner {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }
    </style>
</head>

<body class="dashboard-body">
<div class="dashboard-container">
    
        <?php include 'layout/header.php'; ?>
        <?php include 'layout/sidebar.php'; ?>

<div class="main-content">
    <header class="page-header">
        <h1><i class="fas fa-shopping-bag"></i> My Orders</h1>
        <p>Track your winning bids and purchases</p>
    </header>

    <div class="content-wrapper">

        <div class="filter-tabs">
            <button class="filter-tab active" data-status="all"><i class="fas fa-list"></i> All</button>
            <button class="filter-tab" data-status="pending"><i class="fas fa-clock"></i> Pending</button>
            <button class="filter-tab" data-status="paid"><i class="fas fa-check-circle"></i> Paid</button>
            <button class="filter-tab" data-status="shipped"><i class="fas fa-truck"></i> Shipped</button>
            <button class="filter-tab" data-status="completed"><i class="fas fa-box"></i> Completed</button>
        </div>

        <div id="ordersContainer" class="orders-container"></div>

        <div id="emptyState" class="empty-state" style="display:none;">
            <i class="fas fa-shopping-bag"></i>
            <h3>No Orders Yet</h3>
            <p>Start bidding on auctions to see your orders here.</p>
            <a href="home.php" class="btn-small btn-primary">
                <i class="fas fa-gavel"></i> Browse Auctions
            </a>
        </div>

    </div>
</div>

<script>
async function fetchOrders(status = 'all') {
    const container = document.getElementById('ordersContainer');
    const empty = document.getElementById('emptyState');

    container.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading orders...</div>';
    empty.style.display = 'none';

    try {
        const res = await fetch(`../api/get_orders.php?status=${status}`);
        const data = await res.json();

        if (data.success && data.orders.length) {
            renderOrders(data.orders);
        } else {
            container.innerHTML = '';
            empty.style.display = 'block';
        }
    } catch {
        container.innerHTML = '<p>Error loading orders.</p>';
    }
}

function renderOrders(orders) {
    const container = document.getElementById('ordersContainer');

    container.innerHTML = orders.map(o => `
        <div class="order-card">
            <div class="order-header">
                <div class="order-id">Order #${o.id} • ${new Date(o.created_at).toDateString()}</div>
                <span class="order-status status-${o.status}">${o.status}</span>
            </div>
            <div class="order-content">
                <img src="${o.product_image || '../assets/images/placeholder.jpg'}" class="order-image">
                <div>
                    <div class="order-title">${o.product_name}</div>
                    <div class="order-info">
                        <div><i class="fas fa-user"></i> Seller: ${o.seller_name}</div>
                        <div><i class="fas fa-gavel"></i> Winning Bid: $${o.winning_bid}</div>
                    </div>
                    <div class="order-price">$${o.total_amount}</div>
                    <div class="order-actions">
                        <button class="btn-small btn-outline" onclick="viewDetails(${o.id})">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function viewDetails(id) {
    window.location.href = `order_details.php?id=${id}`;
}

document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.onclick = () => {
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        fetchOrders(tab.dataset.status);
    };
});

fetchOrders();
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>

</body>
</html>
