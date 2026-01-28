<aside class="sidebar">
    <div class="sidebar-header">
        <h2>Tiksale Auction</h2>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':'' ?>">
            <i class="fas fa-home"></i> Home
        </a>

        <a href="profile.php" class="nav-item <?= basename($_SERVER['PHP_SELF'])=='profile.php'?'active':'' ?>">
            <i class="fas fa-user"></i> My Profile
        </a>

        <a href="my_orders.php" class="nav-item <?= basename($_SERVER['PHP_SELF'])=='my_orders.php'?'active':'' ?>">
            <i class="fas fa-box"></i> My Orders
        </a>

        <a href="my_shop.php" class="nav-item <?= basename($_SERVER['PHP_SELF'])=='my_shop.php'?'active':'' ?>">
            <i class="fas fa-store"></i> My Shop
        </a>

        <a href="shop_orders.php" class="nav-item <?= basename($_SERVER['PHP_SELF'])=='shop_orders.php'?'active':'' ?>">
            <i class="fas fa-truck"></i> Shop Orders
        </a>

        <a href="../api/logout.php" class="nav-item logout">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</aside>
