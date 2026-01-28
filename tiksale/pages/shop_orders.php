<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<?php include __DIR__ . '/layout/header.php'; ?>

<body>
    <div class="dashboard-container">
        <?php include __DIR__ . '/layout/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="content-header">
                <h1><i class="fas fa-truck"></i> Shop Orders</h1>
                <p>Manage orders from your auctions</p>
            </div>

            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fff3cd;">
                        <i class="fas fa-clock" style="color: #856404;"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="pendingOrders">0</h3>
                        <p>Pending Orders</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #d1ecf1;">
                        <i class="fas fa-check-circle" style="color: #0c5460;"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="confirmedOrders">0</h3>
                        <p>Confirmed Orders</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #d4edda;">
                        <i class="fas fa-shipping-fast" style="color: #155724;"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="shippedOrders">0</h3>
                        <p>Shipped Orders</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #e8f5e9;">
                        <i class="fas fa-dollar-sign" style="color: #4caf50;"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="totalEarnings">$0</h3>
                        <p>Total Earnings</p>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <button class="filter-tab active" data-status="all">
                    <i class="fas fa-list"></i> All Orders
                </button>
                <button class="filter-tab" data-status="pending">
                    <i class="fas fa-clock"></i> Pending
                </button>
                <button class="filter-tab" data-status="confirmed">
                    <i class="fas fa-check"></i> Confirmed
                </button>
                <button class="filter-tab" data-status="shipped">
                    <i class="fas fa-truck"></i> Shipped
                </button>
                <button class="filter-tab" data-status="completed">
                    <i class="fas fa-box"></i> Completed
                </button>
            </div>

            <!-- Orders List -->
            <div class="orders-list" id="ordersContainer">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i> Loading shop orders...
                </div>
            </div>

            <!-- Empty State -->
            <div class="empty-state" id="emptyState" style="display: none;">
                <i class="fas fa-truck"></i>
                <h3>No Orders Yet</h3>
                <p>When customers win your auctions, their orders will appear here</p>
                <a href="my_shop.php" class="btn btn-primary">
                    <i class="fas fa-store"></i> Go to My Shop
                </a>
            </div>
        </main>
    </div>

    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 15px;
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
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .stat-info p {
            color: #666;
            font-size: 14px;
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 10px 20px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-tab:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .filter-tab.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .orders-list {
            display: grid;
            gap: 20px;
        }

        .order-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .order-number {
            font-weight: 600;
            color: #2c3e50;
            font-size: 16px;
        }

        .order-date {
            color: #666;
            font-size: 14px;
        }

        .order-status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #d1ecf1; color: #0c5460; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-completed { background: #28a745; color: white; }

        .order-body {
            display: flex;
            gap: 20px;
        }

        .order-product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }

        .order-details {
            flex: 1;
        }

        .product-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .order-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #666;
        }

        .info-item i {
            color: var(--primary-color);
            width: 16px;
        }

        .order-price {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .order-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-small {
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
        }

        .btn-outline {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        .buyer-info {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .buyer-info h4 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2c3e50;
        }

        .buyer-details {
            display: grid;
            gap: 5px;
            font-size: 13px;
            color: #666;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
        }

        .empty-state i {
            font-size: 80px;
            color: #e0e0e0;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .empty-state p {
            color: #666;
            margin-bottom: 20px;
        }

        .loading-spinner {
            text-align: center;
            padding: 40px;
            font-size: 18px;
            color: #666;
        }

        .loading-spinner i {
            font-size: 30px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
    </style>

    <script>
        // Fetch shop orders from API
        async function fetchShopOrders(status = 'all') {
            const container = document.getElementById('ordersContainer');
            const emptyState = document.getElementById('emptyState');
            
            container.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading shop orders...</div>';
            emptyState.style.display = 'none';

            try {
                const response = await fetch(`../api/get_shop_orders.php?status=${status}`);
                const data = await response.json();

                if (data.success && data.orders.length > 0) {
                    displayOrders(data.orders);
                    updateStats(data.stats);
                } else {
                    container.innerHTML = '';
                    emptyState.style.display = 'block';
                }
            } catch (error) {
                console.error('Error:', error);
                container.innerHTML = '<div class="error-message">Error loading orders. Please try again.</div>';
            }
        }

        function displayOrders(orders) {
            const container = document.getElementById('ordersContainer');
            
            container.innerHTML = orders.map(order => `
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-number">Order #${order.id}</div>
                            <div class="order-date">${formatDate(order.created_at)}</div>
                        </div>
                        <span class="order-status-badge status-${order.status}">${order.status}</span>
                    </div>
                    <div class="order-body">
                        <img src="${order.product_image || '../assets/images/placeholder.jpg'}" alt="${order.product_name}" class="order-product-image">
                        <div class="order-details">
                            <h3 class="product-name">${order.product_name}</h3>
                            <div class="order-info-grid">
                                <div class="info-item">
                                    <i class="fas fa-gavel"></i>
                                    <span>Sold for: $${parseFloat(order.winning_bid).toFixed(2)}</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-user"></i>
                                    <span>Buyer: ${order.buyer_name}</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Won: ${formatDate(order.auction_end_date)}</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-envelope"></i>
                                    <span>${order.buyer_email}</span>
                                </div>
                            </div>
                            <div class="order-price">$${parseFloat(order.total_amount).toFixed(2)}</div>
                            
                            ${order.buyer_address ? `
                            <div class="buyer-info">
                                <h4><i class="fas fa-map-marker-alt"></i> Shipping Address</h4>
                                <div class="buyer-details">
                                    <div>${order.buyer_name}</div>
                                    <div>${order.buyer_address}</div>
                                    <div>${order.buyer_phone || 'No phone provided'}</div>
                                </div>
                            </div>
                            ` : ''}
                            
                            <div class="order-actions">
                                ${getSellerActions(order)}
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function getSellerActions(order) {
            if (order.status === 'pending') {
                return `
                    <button class="btn-small btn-success" onclick="confirmOrder(${order.id})">
                        <i class="fas fa-check"></i> Confirm Order
                    </button>
                    <button class="btn-small btn-outline" onclick="contactBuyer(${order.id})">
                        <i class="fas fa-envelope"></i> Contact Buyer
                    </button>
                `;
            } else if (order.status === 'confirmed') {
                return `
                    <button class="btn-small btn-primary" onclick="markAsShipped(${order.id})">
                        <i class="fas fa-truck"></i> Mark as Shipped
                    </button>
                    <button class="btn-small btn-outline" onclick="viewDetails(${order.id})">
                        <i class="fas fa-eye"></i> View Details
                    </button>
                `;
            } else if (order.status === 'shipped') {
                return `
                    <button class="btn-small btn-outline" onclick="updateTracking(${order.id})">
                        <i class="fas fa-map-marker-alt"></i> Update Tracking
                    </button>
                    <button class="btn-small btn-outline" onclick="viewDetails(${order.id})">
                        <i class="fas fa-eye"></i> View Details
                    </button>
                `;
            } else {
                return `
                    <button class="btn-small btn-outline" onclick="viewDetails(${order.id})">
                        <i class="fas fa-eye"></i> View Details
                    </button>
                `;
            }
        }

        function updateStats(stats) {
            document.getElementById('pendingOrders').textContent = stats.pending_orders || 0;
            document.getElementById('confirmedOrders').textContent = stats.confirmed_orders || 0;
            document.getElementById('shippedOrders').textContent = stats.shipped_orders || 0;
            document.getElementById('totalEarnings').textContent = '$' + (stats.total_earnings || 0).toFixed(2);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        }

        // Filter tabs functionality
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                fetchShopOrders(tab.dataset.status);
            });
        });

        // Seller action handlers
        async function confirmOrder(orderId) {
            if (confirm('Confirm this order and notify the buyer?')) {
                try {
                    const response = await fetch('../api/update_order_status.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ order_id: orderId, status: 'confirmed' })
                    });
                    const data = await response.json();
                    if (data.success) {
                        alert('Order confirmed successfully!');
                        fetchShopOrders();
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error confirming order');
                }
            }
        }

        async function markAsShipped(orderId) {
            const trackingNumber = prompt('Enter tracking number:');
            if (trackingNumber) {
                try {
                    const response = await fetch('../api/update_order_status.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ 
                            order_id: orderId, 
                            status: 'shipped',
                            tracking_number: trackingNumber 
                        })
                    });
                    const data = await response.json();
                    if (data.success) {
                        alert('Order marked as shipped!');
                        fetchShopOrders();
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error updating order');
                }
            }
        }

        function updateTracking(orderId) {
            const trackingNumber = prompt('Update tracking number:');
            if (trackingNumber) {
                // Call API to update tracking
                alert('Tracking updated! (API not yet implemented)');
            }
        }

        function contactBuyer(orderId) {
            window.location.href = `messages.php?order_id=${orderId}`;
        }

        function viewDetails(orderId) {
            window.location.href = `order_details.php?id=${orderId}`;
        }

        // Load shop orders on page load
        fetchShopOrders();
    </script>

    <?php include __DIR__ . '/layout/footer.php'; ?>

</body>
</html>
