<?php
// 500 Error Page for Star-Clicks Clone
session_start();
include_once '../includes/config.php';
include_once '../includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - Star-Clicks Clone</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center">
    <div class="max-w-2xl mx-auto p-4">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="text-6xl font-bold text-red-500 mb-4">500</div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Server Error</h1>
            <p class="text-gray-600 mb-6">We're sorry, but something went wrong on our server. Please try again later.</p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="../index.php" class="btn-primary">
                    <i class="fas fa-home mr-2"></i>Go to Homepage
                </a>
                <a href="javascript:history.back()" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Go Back
                </a>
            </div>
        </div>
    </div>
</body>
</html>