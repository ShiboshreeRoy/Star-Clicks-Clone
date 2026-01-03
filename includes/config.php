<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP password is empty
define('DB_NAME', 'star_clicks_clone');

// Site Configuration
define('SITE_URL', 'http://localhost/ptc');
define('SITE_NAME', 'Star-Clicks Clone');
define('SITE_EMAIL', 'support@star-clicks-clone.com');

// Security Configuration
define('HASH_COST', 12);
define('SESSION_TIMEOUT', 3600); // 1 hour

// Database Connection
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", 
                      DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Initialize session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}