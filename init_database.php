<?php
// Database Initialization Script for Star-Clicks Clone

require_once 'includes/config.php';

try {
    // Create database connection without specifying database name
    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Create the database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "Database '" . DB_NAME . "' created successfully (or already exists).\n";

    // Now connect to the specific database
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Read the SQL schema from the file
    $sqlSchema = file_get_contents('database_schema.sql');
    
    // Split the SQL into individual statements
    $statements = explode(';', $sqlSchema);
    
    // Execute each statement
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }

    echo "Database schema created successfully!\n";
    
    // Create the admin_requests table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_requests (
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
    );");
    
    echo "Admin requests table created successfully!\n";

    // Create a default admin user
    $defaultAdminEmail = 'admin@star-clicks-clone.com';
    $defaultAdminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    
    $checkAdmin = $pdo->prepare("SELECT id FROM users WHERE email = ? AND user_type = 'admin'");
    $checkAdmin->execute([$defaultAdminEmail]);
    
    if ($checkAdmin->rowCount() === 0) {
        $stmt = $pdo->prepare("INSERT INTO users (email, password, first_name, last_name, user_type, status) VALUES (?, ?, ?, ?, 'admin', 'active')");
        $stmt->execute([$defaultAdminEmail, $defaultAdminPassword, 'Admin', 'User']);
        echo "Default admin user created:\n";
        echo "  Email: $defaultAdminEmail\n";
        echo "  Password: admin123\n";
    } else {
        echo "Admin user already exists.\n";
    }

    echo "\nDatabase initialization completed successfully!\n";
    echo "You can now run the application.\n";

} catch (PDOException $e) {
    die("Database initialization failed: " . $e->getMessage());
}