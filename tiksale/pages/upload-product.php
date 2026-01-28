<?php
require_once __DIR__ . '/../includes/session_init.php';

/**
 * Access control
 * Keep this SIMPLE for now to avoid login loops
 */
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Product | Tiksale</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="dashboard-body">

<?php include __DIR__ . '/layout/sidebar.php'; ?>
<?php include __DIR__ . '/layout/header.php'; ?>

<div class="main-content">
    <header class="page-header">
        <h1><i class="fas fa-upload"></i> Upload New Product</h1>
        <p>Add a new auction product to your shop</p>
    </header>

    <div class="content-wrapper">

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                Failed to upload product. Please try again.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                Product uploaded successfully!
            </div>
        <?php endif; ?>

        <form method="POST"
              action="../api/upload_product.php"
              enctype="multipart/form-data"
              class="form-card">

            <!-- Product Name -->
            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="name" required>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label>Description *</label>
                <textarea name="description" rows="4" required></textarea>
            </div>

            <!-- Starting Price -->
            <div class="form-row">
                <div class="form-group">
                    <label>Starting Price (KES) *</label>
                    <input type="number"
                           name="starting_price"
                           step="0.01"
                           min="1"
                           required>
                </div>
            </div>

            <!-- Auction Duration -->
            <div class="form-row">
                <div class="form-group">
                    <label>Auction Duration *</label>
                    <select name="duration_days" required>
                        <option value="3">3 Days</option>
                        <option value="5">5 Days</option>
                        <option value="7" selected>7 Days</option>
                        <option value="10">10 Days</option>
                        <option value="14">14 Days</option>
                    </select>
                </div>
            </div>

            <!-- Product Image -->
            <div class="form-group">
                <label>Product Image *</label>
                <input type="file"
                       name="image"
                       accept="image/*"
                       required>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload Product
                </button>
                <a href="my_shop.php" class="btn btn-outline">Cancel</a>
            </div>

        </form>

    </div>
</div>

</body>
</html>
