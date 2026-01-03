<?php
// Simple CAPTCHA generation for Star-Clicks Clone
session_start();

// Generate a random string for CAPTCHA
$captcha_code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 6);

// Store the CAPTCHA code in session
$_SESSION['captcha'] = $captcha_code;

// Create image
$width = 120;
$height = 40;
$image = imagecreate($width, $height);

// Set colors
$background_color = imagecolorallocate($image, 255, 255, 255); // White background
$text_color = imagecolorallocate($image, 0, 0, 0); // Black text
$line_color = imagecolorallocate($image, 255, 0, 0); // Red lines

// Draw random lines for background
for ($i = 0; $i < 5; $i++) {
    $x1 = rand(0, $width);
    $y1 = rand(0, $height);
    $x2 = rand(0, $width);
    $y2 = rand(0, $height);
    imageline($image, $x1, $y1, $x2, $y2, $line_color);
}

// Add the CAPTCHA text
$font_size = 20;
$x = 15;
$y = 25;
imagestring($image, 5, $x, $y, $captcha_code, $text_color);

// Output the image
header('Content-type: image/png');
imagepng($image);

// Free up memory
imagedestroy($image);