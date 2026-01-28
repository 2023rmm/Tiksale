-- =====================================================
-- TIKSALE AUCTION SYSTEM - ULTIMATE DATABASE MIGRATION
-- =====================================================
-- This script fixes all foreign key issues and creates
-- a complete, production-ready database schema
-- =====================================================

-- Drop database if exists and recreate (FRESH START)
DROP DATABASE IF EXISTS tiksale_auction;
CREATE DATABASE tiksale_auction CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tiksale_auction;

-- Disable foreign key checks during migration
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';

-- =====================================================
-- TABLE 1: USERS (No dependencies)
-- =====================================================
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    full_name VARCHAR(100),
    country VARCHAR(50),
    user_type ENUM('buyer', 'seller', 'both', 'admin') DEFAULT 'buyer',
    is_verified TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    profile_image VARCHAR(255),
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_user_type (user_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE 2: CATEGORIES (No dependencies)
-- =====================================================
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    parent_category_id INT NULL,
    image_url VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_category_id) REFERENCES categories(category_id) ON DELETE SET NULL,
    INDEX idx_parent (parent_category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE 3: PRODUCTS (Depends on: users, categories)
-- =====================================================
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    seller_id INT NOT NULL,
    category_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    country VARCHAR(50),
    condition_type ENUM('new', 'like_new', 'good', 'fair', 'poor') DEFAULT 'good',
    starting_price DECIMAL(10,2) NOT NULL,
    buy_now_price DECIMAL(10,2),
    quantity INT DEFAULT 1,
    status ENUM('draft', 'active', 'sold', 'expired', 'removed') DEFAULT 'draft',
    views_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE RESTRICT,
    INDEX idx_seller (seller_id),
    INDEX idx_category (category_id),
    INDEX idx_status (status),
    FULLTEXT idx_search (product_name, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE 4: PRODUCT_IMAGES (Depends on: products)
-- =====================================================
CREATE TABLE product_images (
    image_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    display_order INT DEFAULT 0,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE 5: AUCTIONS (Depends on: products, users)
-- =====================================================
CREATE TABLE auctions (
    auction_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    seller_id INT NOT NULL,
    auction_type ENUM('english', 'dutch', 'sealed') DEFAULT 'english',
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    starting_bid DECIMAL(10,2) NOT NULL,
    current_bid DECIMAL(10,2) DEFAULT 0.00,
    reserve_price DECIMAL(10,2),
    bid_increment DECIMAL(10,2) DEFAULT 1.00,
    total_bids INT DEFAULT 0,
    winner_id INT NULL,
    status ENUM('pending', 'active', 'ended', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (winner_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_product (product_id),
    INDEX idx_seller (seller_id),
    INDEX idx_status (status),
    INDEX idx_end_time (end_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE 6: BIDS (Depends on: auctions, users)
-- =====================================================
CREATE TABLE bids (
    bid_id INT PRIMARY KEY AUTO_INCREMENT,
    auction_id INT NOT NULL,
    bidder_id INT NOT NULL,
    bid_amount DECIMAL(10,2) NOT NULL,
    bid_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_auto_bid TINYINT(1) DEFAULT 0,
    max_auto_bid DECIMAL(10,2),
    is_winning TINYINT(1) DEFAULT 0,
    ip_address VARCHAR(45),
    FOREIGN KEY (auction_id) REFERENCES auctions(auction_id) ON DELETE CASCADE,
    FOREIGN KEY (bidder_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_auction (auction_id),
    INDEX idx_bidder (bidder_id),
    INDEX idx_bid_time (bid_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE 7: WATCHLIST (Depends on: users, products)
-- =====================================================
CREATE TABLE watchlist (
    watchlist_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_watch (user_id, product_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE 8: TRANSACTIONS (Depends on: auctions, users)
-- =====================================================
CREATE TABLE transactions (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    auction_id INT NOT NULL,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('mpesa', 'paypal', 'stripe', 'bank_transfer') NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    transaction_ref VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (auction_id) REFERENCES auctions(auction_id) ON DELETE RESTRICT,
    FOREIGN KEY (buyer_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (seller_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_buyer (buyer_id),
    INDEX idx_seller (seller_id),
    INDEX idx_status (payment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE 9: REVIEWS_RATINGS (Depends on: users, transactions)
-- =====================================================
CREATE TABLE reviews_ratings (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    transaction_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    reviewed_user_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(transaction_id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_reviewed_user (reviewed_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE 10: MESSAGES (Depends on: users)
-- =====================================================
CREATE TABLE messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    subject VARCHAR(200),
    message_text TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_sender (sender_id),
    INDEX idx_receiver (receiver_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE 11: NOTIFICATIONS (Depends on: users)
-- =====================================================
CREATE TABLE notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    notification_type ENUM('bid', 'outbid', 'won', 'message', 'system') NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    related_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE 12: DISPUTES (Depends on: transactions, users)
-- =====================================================
CREATE TABLE disputes (
    dispute_id INT PRIMARY KEY AUTO_INCREMENT,
    transaction_id INT NOT NULL,
    complainant_id INT NOT NULL,
    defendant_id INT NOT NULL,
    dispute_reason TEXT NOT NULL,
    status ENUM('open', 'under_review', 'resolved', 'closed') DEFAULT 'open',
    resolution TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY (transaction_id) REFERENCES transactions(transaction_id) ON DELETE RESTRICT,
    FOREIGN KEY (complainant_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (defendant_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_transaction (transaction_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE 13: ACTIVITY_LOGS (Depends on: users)
-- =====================================================
CREATE TABLE activity_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action_type VARCHAR(50) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE 14: ADMIN_SETTINGS (No dependencies)
-- =====================================================
CREATE TABLE admin_settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VIEWS
-- =====================================================
CREATE OR REPLACE VIEW view_active_auctions AS
SELECT 
    a.auction_id,
    a.product_id,
    p.product_name,
    p.description,
    a.current_bid,
    a.total_bids,
    a.end_time,
    u.username AS seller_username,
    TIMESTAMPDIFF(HOUR, NOW(), a.end_time) AS hours_remaining
FROM auctions a
JOIN products p ON a.product_id = p.product_id
JOIN users u ON a.seller_id = u.user_id
WHERE a.status = 'active' AND a.end_time > NOW();

CREATE OR REPLACE VIEW view_user_statistics AS
SELECT 
    u.user_id,
    u.username,
    COUNT(DISTINCT p.product_id) AS total_products,
    COUNT(DISTINCT a.auction_id) AS total_auctions,
    COUNT(DISTINCT b.bid_id) AS total_bids,
    u.rating,
    u.total_reviews
FROM users u
LEFT JOIN products p ON u.user_id = p.seller_id
LEFT JOIN auctions a ON u.user_id = a.seller_id
LEFT JOIN bids b ON u.user_id = b.bidder_id
GROUP BY u.user_id;

-- =====================================================
-- TRIGGERS
-- =====================================================
DELIMITER $$

CREATE TRIGGER after_bid_insert
AFTER INSERT ON bids
FOR EACH ROW
BEGIN
    UPDATE auctions 
    SET current_bid = NEW.bid_amount,
        total_bids = total_bids + 1
    WHERE auction_id = NEW.auction_id;
    
    INSERT INTO notifications (user_id, notification_type, title, message, related_id)
    SELECT seller_id, 'bid', 'New Bid Received', 
           CONCAT('New bid of $', NEW.bid_amount, ' placed on your auction'),
           NEW.auction_id
    FROM auctions WHERE auction_id = NEW.auction_id;
END$$

CREATE TRIGGER after_auction_end
BEFORE UPDATE ON auctions
FOR EACH ROW
BEGIN
    IF NEW.status = 'ended' AND OLD.status = 'active' THEN
        SELECT bidder_id INTO @winner FROM bids 
        WHERE auction_id = NEW.auction_id 
        ORDER BY bid_amount DESC LIMIT 1;
        
        SET NEW.winner_id = @winner;
    END IF;
END$$

CREATE TRIGGER after_review_insert
AFTER INSERT ON reviews_ratings
FOR EACH ROW
BEGIN
    UPDATE users 
    SET rating = (SELECT AVG(rating) FROM reviews_ratings WHERE reviewed_user_id = NEW.reviewed_user_id),
        total_reviews = (SELECT COUNT(*) FROM reviews_ratings WHERE reviewed_user_id = NEW.reviewed_user_id)
    WHERE user_id = NEW.reviewed_user_id;
END$$

DELIMITER ;

-- =====================================================
-- SEED DATA
-- =====================================================

-- Insert admin user (password: admin123)
INSERT INTO users (username, email, password_hash, full_name, user_type, is_verified) VALUES
('admin', 'admin@tiksale.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Admin', 'admin', 1),
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'both', 1),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', 'buyer', 1);

-- Insert categories
INSERT INTO categories (category_name, description) VALUES
('Electronics', 'Computers, phones, cameras, and electronic devices'),
('Art & Collectibles', 'Paintings, sculptures, and rare collectibles'),
('Fashion & Accessories', 'Clothing, jewelry, watches, and accessories'),
('Home & Garden', 'Furniture, decor, and gardening items'),
('Sports & Outdoors', 'Sports equipment and outdoor gear'),
('Books & Media', 'Books, movies, music, and games'),
('Vehicles', 'Cars, motorcycles, and automotive parts'),
('Real Estate', 'Properties and land');

-- Insert admin settings
INSERT INTO admin_settings (setting_key, setting_value, description) VALUES
('site_name', 'Tiksale Auction', 'Website name'),
('site_email', 'support@tiksale.com', 'Contact email'),
('bid_extension_minutes', '5', 'Minutes to extend auction on last-minute bids'),
('commission_rate', '10', 'Platform commission percentage'),
('min_bid_increment', '1.00', 'Minimum bid increment amount');

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- VERIFICATION QUERY
-- =====================================================
SELECT 
    'Database Migration Complete!' AS status,
    COUNT(*) AS total_tables,
    (SELECT COUNT(*) FROM users) AS total_users,
    (SELECT COUNT(*) FROM categories) AS total_categories
FROM information_schema.tables 
WHERE table_schema = 'tiksale_auction';

