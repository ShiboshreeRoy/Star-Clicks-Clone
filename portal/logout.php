<?php
// Logout functionality for Star-Clicks Clone
session_start();

include_once '../includes/config.php';
include_once '../includes/functions.php';

// Log the logout activity if user was logged in
if (isset($_SESSION['user_id'])) {
    logActivity($_SESSION['user_id'], 'logout', 'User logged out from IP: ' . $_SERVER['REMOTE_ADDR']);
}

// Destroy all session variables
session_unset();
session_destroy();

// Redirect to home page
redirect('../index.php');