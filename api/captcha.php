<?php
// Simple text-based CAPTCHA generation for Star-Clicks Clone
session_start();

// Generate a random string for CAPTCHA
$captcha_code = substr(str_shuffle('0123456789'), 0, 6);

// Store the CAPTCHA code in session
$_SESSION['captcha'] = $captcha_code;

// Output the CAPTCHA as plain text
header('Content-Type: text/plain');
echo $captcha_code;
?>