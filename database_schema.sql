-- Star-Clicks Clone Database Schema

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    user_type ENUM('publisher', 'advertiser', 'admin') DEFAULT 'publisher',
    balance DECIMAL(10, 2) DEFAULT 0.00,
    total_earned DECIMAL(10, 2) DEFAULT 0.00,
    total_spent DECIMAL(10, 2) DEFAULT 0.00,
    status ENUM('active', 'inactive', 'suspended', 'pending') DEFAULT 'pending',
    email_verified BOOLEAN DEFAULT FALSE,
    phone_verified BOOLEAN DEFAULT FALSE,
    country VARCHAR(100),
    address TEXT,
    date_of_birth DATE,
    website_url VARCHAR(255), -- For advertisers
    membership_type ENUM('silver', 'gold', 'platinum') DEFAULT 'silver',
    paypal_email VARCHAR(255),
    bank_details TEXT,
    verification_documents TEXT,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Advertisements table
CREATE TABLE advertisements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    advertiser_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    url VARCHAR(500) NOT NULL,
    cpc DECIMAL(5, 2) NOT NULL, -- Cost per click
    daily_budget DECIMAL(10, 2) NOT NULL,
    spent DECIMAL(10, 2) DEFAULT 0.00,
    clicks_count INT DEFAULT 0,
    impressions_count INT DEFAULT 0,
    status ENUM('active', 'paused', 'completed', 'pending') DEFAULT 'pending',
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (advertiser_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Ad clicks table
CREATE TABLE ad_clicks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad_id INT NOT NULL,
    publisher_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    country VARCHAR(100),
    referrer VARCHAR(500),
    amount_earned DECIMAL(5, 2), -- Amount earned by publisher
    amount_paid DECIMAL(5, 2), -- Amount paid by advertiser
    is_valid BOOLEAN DEFAULT TRUE, -- To track valid/invalid clicks
    clicked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ad_id) REFERENCES advertisements(id) ON DELETE CASCADE,
    FOREIGN KEY (publisher_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Ad impressions table
CREATE TABLE ad_impressions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad_id INT NOT NULL,
    publisher_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    country VARCHAR(100),
    referrer VARCHAR(500),
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ad_id) REFERENCES advertisements(id) ON DELETE CASCADE,
    FOREIGN KEY (publisher_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Withdrawals table
CREATE TABLE withdrawals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    method ENUM('paypal', 'bank_transfer', 'bitcoin') NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled', 'rejected') DEFAULT 'pending',
    details TEXT, -- Additional details like PayPal email, bank info
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Deposits table
CREATE TABLE deposits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    method ENUM('paypal', 'bank_transfer', 'bitcoin', 'credit_card') NOT NULL,
    transaction_id VARCHAR(255),
    status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Referrals table
CREATE TABLE referrals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    referrer_id INT NOT NULL,
    referred_id INT NOT NULL,
    commission_earned DECIMAL(10, 2) DEFAULT 0.00,
    is_paid BOOLEAN DEFAULT FALSE,
    referred_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (referrer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (referred_id) REFERENCES users(id) ON DELETE CASCADE
);

-- User activities table
CREATE TABLE user_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Admin requests table
CREATE TABLE admin_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reason TEXT NOT NULL,
    experience TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT NULL,
    review_notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Site settings table
CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(255) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default site settings
INSERT INTO site_settings (setting_key, setting_value, description) VALUES
('site_name', 'Star-Clicks Clone', 'Name of the website'),
('site_description', 'Earn money online by publishing ads or advertise your website', 'Description of the website'),
('minimum_payout', '5.00', 'Minimum amount for withdrawal'),
('minimum_deposit', '5.00', 'Minimum amount for deposit'),
('cpc_rate', '0.01', 'Default cost per click rate'),
('publisher_commission', '0.50', 'Percentage of CPC that goes to publisher (50%)'),
('auto_payout_enabled', '1', 'Enable auto payout feature (1 for yes, 0 for no)'),
('captcha_enabled', '1', 'Enable CAPTCHA on forms'),
('maintenance_mode', '0', 'Site maintenance mode (1 for on, 0 for off)'),
('paypal_enabled', '1', 'Enable PayPal as payment method'),
('bank_transfer_enabled', '1', 'Enable bank transfer as payment method');

-- Indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_type ON users(user_type);
CREATE INDEX idx_ads_status ON advertisements(status);
CREATE INDEX idx_ads_dates ON advertisements(start_date, end_date);
CREATE INDEX idx_clicks_ad ON ad_clicks(ad_id);
CREATE INDEX idx_clicks_publisher ON ad_clicks(publisher_id);
CREATE INDEX idx_clicks_date ON ad_clicks(clicked_at);
CREATE INDEX idx_withdrawals_user ON withdrawals(user_id);
CREATE INDEX idx_withdrawals_status ON withdrawals(status);
CREATE INDEX idx_deposits_user ON deposits(user_id);
CREATE INDEX idx_deposits_status ON deposits(status);
CREATE INDEX idx_activities_user ON user_activities(user_id);
CREATE INDEX idx_activities_date ON user_activities(created_at);