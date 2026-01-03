<?php
// Utility functions for the Star-Clicks clone

/**
 * Redirect to a specific page
 */
function redirect($page) {
    header("Location: $page");
    exit();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user info
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return false;
}

/**
 * Sanitize user input
 */
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

/**
 * Generate a random string
 */
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Send email (placeholder function)
 */
function sendEmail($to, $subject, $message) {
    // In a real application, you would use PHPMailer or similar
    // For now, we'll just return true
    return true;
}

/**
 * Check if email is valid
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if user is publisher
 */
function isPublisher() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'publisher';
}

/**
 * Check if user is advertiser
 */
function isAdvertiser() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'advertiser';
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

/**
 * Format currency
 */
function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

/**
 * Log activity
 */
function logActivity($user_id, $activity, $details = '') {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("INSERT INTO user_activities (user_id, activity, details, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, $activity, $details]);
}

/**
 * Check CSRF token
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Escape output for HTML
 */
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}