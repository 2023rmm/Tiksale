<?php
require_once __DIR__ . '/includes/session_init.php';

/**
 * 🔁 Run background auction closer (silent & safe)
 * Does not affect UI even if it fails
 */
@file_get_contents('http://localhost/tiksale/api/system/close-auctions.php');

/**
 * 🔐 Redirect logged-in users to dashboard
 */
if (!empty($_SESSION['user_id'])) {
    header("Location: pages/dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiksale - Premium Online Auction Platform</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <div class="logo">
                    <i class="fas fa-gavel"></i>
                    <span>Tiksale</span>
                </div>
                <ul class="nav-links">
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="#auctions">Auctions</a></li>
                    <li><a href="#categories">Categories</a></li>
                    <li><a href="#how-it-works">How It Works</a></li>
                </ul>
                <div class="nav-actions">
                    <a href="pages/login.php" class="btn btn-outline">Login</a>
                    <a href="pages/register.php" class="btn btn-primary">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">Discover Unique Items at <span class="gradient-text">Unbeatable Prices</span></h1>
                    <p class="hero-subtitle">Join thousands of buyers and sellers in the most trusted online auction platform. Bid now and win amazing deals!</p>
                    <div class="hero-buttons">
                        <a href="pages/register.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-rocket"></i> Start Bidding
                        </a>
                        <a href="#how-it-works" class="btn btn-outline btn-lg">
                            <i class="fas fa-play-circle"></i> How It Works
                        </a>
                    </div>
                    <div class="hero-stats">
                        <div class="stat">
                            <h3>100+</h3>
                            <p>Active Users</p>
                        </div>
                        <div class="stat">
                            <h3>50+</h3>
                            <p>Items Sold</p>
                        </div>
                        <div class="stat">
                            <h3>Ksh2M+</h3>
                            <p>Total Value</p>
                        </div>
                    </div>
                </div>
                <div class="hero-image">
                    <div class="floating-card card-1">
                        <i class="fas fa-trophy"></i>
                        <p>Win Amazing Deals</p>
                    </div>
                    <div class="floating-card card-2">
                        <i class="fas fa-shield-alt"></i>
                        <p>100% Secure</p>
                    </div>
                    <div class="floating-card card-3">
                        <i class="fas fa-users"></i>
                        <p>Trusted Community</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="categories">
        <div class="container">
            <div class="section-header">
                <h2>Browse by Category</h2>
                <p>Explore our wide range of auction categories</p>
            </div>
            <div class="category-grid">
                <div class="category-card category-card--electronics">
                    <span class="category-card__icon">
                        <i class="fas fa-laptop-code"></i>
                    </span>
                    <h3>Electronics</h3>
                    <p>50+ items</p>
                </div>
                <div class="category-card category-card--art">
                    <span class="category-card__icon">
                        <i class="fas fa-palette"></i>
                    </span>
                    <h3>Art & Collectibles</h3>
                    <p>30+ items</p>
                </div>
                <div class="category-card category-card--fashion">
                    <span class="category-card__icon">
                        <i class="fas fa-shirt"></i>
                    </span>
                    <h3>Fashion</h3>
                    <p>20+ items</p>
                </div>
                <div class="category-card category-card--home">
                    <span class="category-card__icon">
                        <i class="fas fa-seedling"></i>
                    </span>
                    <h3>Home & Garden</h3>
                    <p>18+ items</p>
                </div>
                <div class="category-card category-card--sports">
                    <span class="category-card__icon">
                        <i class="fas fa-futbol"></i>
                    </span>
                    <h3>Sports</h3>
                    <p>95+ items</p>
                </div>
                <div class="category-card category-card--books">
                    <span class="category-card__icon">
                        <i class="fas fa-book-open"></i>
                    </span>
                    <h3>Books & Media</h3>
                    <p>140+ items</p>
                </div>
                <div class="category-card category-card--vehicles">
                    <span class="category-card__icon">
                        <i class="fas fa-car-side"></i>
                    </span>
                    <h3>Vehicles</h3>
                    <p>65+ items</p>
                </div>
                <div class="category-card category-card--estate">
                    <span class="category-card__icon">
                        <i class="fas fa-city"></i>
                    </span>
                    <h3>Real Estate</h3>
                    <p>45+ items</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2>How It Works</h2>
                <p>Start bidding in 3 simple steps</p>
            </div>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <i class="fas fa-user-plus step-icon"></i>
                    <h3>Create Account</h3>
                    <p>Sign up for free and complete your profile in minutes</p>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <i class="fas fa-search step-icon"></i>
                    <h3>Browse & Bid</h3>
                    <p>Find items you love and place your bids confidently</p>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <i class="fas fa-trophy step-icon"></i>
                    <h3>Win & Enjoy</h3>
                    <p>Complete payment and get your items delivered</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features">
        <div class="container">
            <div class="section-header">
                <h2>Why Choose Tiksale?</h2>
                <p>The most trusted auction platform</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Secure Transactions</h3>
                    <p>Your payments and data are protected with industry-leading security</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-bell"></i>
                    <h3>Real-time Notifications</h3>
                    <p>Get instant alerts when you're outbid or win an auction</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-headset"></i>
                    <h3>24/7 Support</h3>
                    <p>Our dedicated team is always here to help you</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-star"></i>
                    <h3>Verified Sellers</h3>
                    <p>All sellers are verified and rated by our community</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Start Winning?</h2>
                <p>Join thousands of satisfied buyers and sellers today!</p>
                <a href="pages/register.php" class="btn btn-white btn-lg">
                    <i class="fas fa-rocket"></i> Get Started Free
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <i class="fas fa-gavel"></i>
                        <span>Tiksale</span>
                    </div>
                    <p>The most trusted online auction platform for buyers and sellers worldwide.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#auctions">Auctions</a></li>
                        <li><a href="#categories">Categories</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#">About Us</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Connect With Us</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Tiksale Auction. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
