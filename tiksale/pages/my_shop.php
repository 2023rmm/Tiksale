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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Shop - Tiksale Auction</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">

        <?php include 'layout/header.php'; ?>
        <?php include 'layout/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="content-header">
                <h1><i class="fas fa-store"></i> My Shop</h1>
                <p>Manage your auction listings</p>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
            <button class="btn btn-primary" onclick="openUploadModal()">
                <i class="fas fa-plus"></i> Upload New Product
            </button>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search your products...">
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #e3f2fd;">
                        <i class="fas fa-box" style="color: #2196f3;"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="totalProducts">0</h3>
                        <p>Total Products</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #e8f5e9;">
                        <i class="fas fa-gavel" style="color: #4caf50;"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="activeAuctions">0</h3>
                        <p>Active Auctions</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fff3e0;">
                        <i class="fas fa-dollar-sign" style="color: #ff9800;"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="totalRevenue">$0</h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fce4ec;">
                        <i class="fas fa-users" style="color: #e91e63;"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="totalBidders">0</h3>
                        <p>Total Bidders</p>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="products-container" id="productsContainer">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i> Loading your products...
                </div>
            </div>
        </main>
    </div>

    <!-- Upload Modal -->
<div id="uploadModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeUploadModal()">&times;</span>
        <h2><i class="fas fa-upload"></i> Upload New Product</h2>

        <form id="uploadForm" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Description *</label>
                <textarea name="description" required></textarea>
            </div>

            <div class="form-group">
                <label>Starting Price (KES)</label>
                <input type="number" name="starting_price" required>
            </div>

            <div class="form-group">
                <label>Duration (days)</label>
                <select name="duration_days">
                    <option value="3">3</option>
                    <option value="5">5</option>
                    <option value="7" selected>7</option>
                </select>
            </div>

            <div class="form-group">
                <label>Images</label>
                <input type="file" name="images[]" multiple accept="image/*">
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="closeUploadModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>

    <style>
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            flex: 1;
            max-width: 400px;
        }

        .search-box i {
            color: #999;
            margin-right: 10px;
        }

        .search-box input {
            border: none;
            outline: none;
            flex: 1;
            font-size: 14px;
        }

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

        .products-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-active { background: #4caf50; color: white; }
        .badge-pending { background: #ff9800; color: white; }
        .badge-sold { background: #f44336; color: white; }

        .product-info {
            padding: 15px;
        }

        .product-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2c3e50;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 13px;
            color: #666;
        }

        .product-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 12px;
        }

        .product-actions {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: all 0.3s;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            overflow-y: auto;
        }

        .modal-content {
            background: white;
            margin: 50px auto;
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 28px;
            font-weight: bold;
            color: #999;
            cursor: pointer;
        }

        .close:hover {
            color: #333;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .image-preview {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .preview-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 99999 !important;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
        }

        .modal-content {
            background: #fff;
            margin: 80px auto;
            padding: 25px;
            border-radius: 10px;
            max-width: 600px;
        }

    </style>

    <script>
        // Fetch user products
        async function fetchProducts() {
            const container = document.getElementById('productsContainer');
            
            try {
                const response = await fetch('../api/get_my_products.php');
                const data = await response.json();

                if (data.success) {
                    displayProducts(data.products);
                    updateStats(data.stats);
                } else {
                    container.innerHTML = '<div class="empty-state"><i class="fas fa-box"></i><h3>No Products Yet</h3><p>Upload your first product to start selling</p></div>';
                }
            } catch (error) {
                console.error('Error:', error);
                container.innerHTML = '<div class="error-message">Error loading products</div>';
            }
        }

        function displayProducts(products) {
            const container = document.getElementById('productsContainer');
            
            container.innerHTML = products.map(product => `
    <div class="product-card">
        <img src="${product.image_url || '../assets/images/placeholder.jpg'}"
             alt="${product.name}" class="product-image">

        <span class="product-badge badge-${product.status}">
            ${product.status}
        </span>

        <div class="product-info">
            <h3 class="product-title">${product.name}</h3>

            <div class="product-stats">
                <span><i class="fas fa-eye"></i> ${product.views} views</span>
                <span><i class="fas fa-gavel"></i> ${product.bids} bids</span>
            </div>

            <div class="product-price">
                KES ${parseFloat(product.current_price).toFixed(2)}
            </div>

            <div class="product-actions">
                <button class="btn-icon btn-primary" onclick="viewProduct(${product.id})">
                    <i class="fas fa-eye"></i> View
                </button>
                <button class="btn-icon btn-outline" onclick="editProduct(${product.id})">
                    <i class="fas fa-edit"></i> Edit
                </button>
            </div>
        </div>
    </div>
`).join('');

        }

        function updateStats(stats) {
            document.getElementById('totalProducts').textContent = stats.total_products || 0;
            document.getElementById('activeAuctions').textContent = stats.active_auctions || 0;
            document.getElementById('totalRevenue').textContent = '$' + (stats.total_revenue || 0).toFixed(2);
            document.getElementById('totalBidders').textContent = stats.total_bidders || 0;
        }

        // Modal functions

        function previewImages(event) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            Array.from(event.target.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'preview-img';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }

        // Upload form submission
        document.getElementById('uploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            
            try {
                const response = await fetch('../api/upload_product.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                closeUploadModal();
                fetchProducts();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error uploading product');
            }
        });

        function viewProduct(productId) {
    window.location.href = `product_details.php?id=${productId}`;
}

function editProduct(productId) {
    window.location.href = `edit_product.php?id=${productId}`;
}

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const products = document.querySelectorAll('.product-card');
            
            products.forEach(product => {
                const title = product.querySelector('.product-title').textContent.toLowerCase();
                product.style.display = title.includes(searchTerm) ? 'block' : 'none';
            });
        });

        // Load products on page load
        fetchProducts();
        console.log("Inline JS loaded");

function openUploadModal() {
    console.log("openUploadModal clicked");
    const modal = document.getElementById("uploadModal");
    if (!modal) {
        alert("Modal element not found");
        return;
    }
    modal.style.display = "block";
}

function closeUploadModal() {
    document.getElementById("uploadModal").style.display = "none";
}

    </script>

    <?php include __DIR__ . '/layout/footer.php'; ?>

</body>
</html>
