<?php
// Support Page for Star-Clicks Clone
session_start();
include_once '../includes/config.php';
include_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('signin.php');
}

$user = getCurrentUser();
if (!$user) {
    redirect('signin.php');
}

$error_message = '';
$success_message = '';

// Handle support ticket submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_ticket'])) {
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    $category = sanitizeInput($_POST['category'] ?? '');
    
    // Validation
    if (empty($subject) || empty($message) || empty($category)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (strlen($subject) < 5) {
        $error_message = 'Subject must be at least 5 characters long.';
    } elseif (strlen($message) < 10) {
        $error_message = 'Message must be at least 10 characters long.';
    } else {
        // In a real application, you would save the ticket to a database
        // For this demo, we'll just show a success message
        $success_message = 'Your support ticket has been submitted successfully. Our team will respond as soon as possible.';
        
        logActivity($user['id'], 'support_ticket_submitted', "Submitted support ticket: $subject");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - Star-Clicks Clone</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-2xl font-bold text-blue-600">Star-Clicks</span>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <span class="text-gray-700">Welcome, <?php echo escape($user['first_name'] . ' ' . $user['last_name']); ?></span>
                    <a href="dashboard.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Dashboard</a>
                    <a href="profile.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Profile</a>
                    <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Support Center</h1>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo escape($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success mb-4">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo escape($success_message); ?>
                </div>
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 p-3 rounded-full mr-4">
                            <i class="fas fa-life-ring text-blue-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Help Center</h2>
                    </div>
                    <p class="text-gray-600 mb-4">Find answers to common questions and learn how to use our platform effectively.</p>
                    <a href="../help.php" class="text-blue-600 hover:underline font-medium">Visit Help Center</a>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center mb-4">
                        <div class="bg-green-100 p-3 rounded-full mr-4">
                            <i class="fas fa-comments text-green-600 text-xl"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Live Chat</h2>
                    </div>
                    <p class="text-gray-600 mb-4">Get instant help from our support team during business hours.</p>
                    <p class="text-gray-600 text-sm">Business hours: Monday to Friday, 9am to 5pm, and Saturday 11am to 3pm</p>
                </div>
            </div>
            
            <div class="card mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Submit a Support Ticket</h2>
                
                <form method="POST" action="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="form-group">
                            <label for="category" class="form-label">Category</label>
                            <select id="category" name="category" class="form-control" required>
                                <option value="">Select Category</option>
                                <option value="account">Account Issues</option>
                                <option value="payment">Payment & Payouts</option>
                                <option value="technical">Technical Issues</option>
                                <option value="advertising">Advertising</option>
                                <option value="publishing">Publishing</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" id="subject" name="subject" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group mb-6">
                        <label for="message" class="form-label">Message</label>
                        <textarea id="message" name="message" class="form-control" rows="6" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" name="submit_ticket" class="btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>Submit Ticket
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="card">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Contact Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 border border-gray-200 rounded-lg">
                        <i class="fas fa-phone text-blue-600 text-2xl mb-3"></i>
                        <h3 class="font-semibold text-lg mb-1">Phone</h3>
                        <p class="text-gray-600">+44 203 290 8015</p>
                        <p class="text-gray-500 text-sm">Mon-Fri: 9am-5pm, Sat: 11am-3pm</p>
                    </div>
                    
                    <div class="text-center p-4 border border-gray-200 rounded-lg">
                        <i class="fas fa-envelope text-blue-600 text-2xl mb-3"></i>
                        <h3 class="font-semibold text-lg mb-1">Email</h3>
                        <p class="text-gray-600">[email protected]</p>
                    </div>
                    
                    <div class="text-center p-4 border border-gray-200 rounded-lg">
                        <i class="fas fa-map-marker-alt text-blue-600 text-2xl mb-3"></i>
                        <h3 class="font-semibold text-lg mb-1">Address</h3>
                        <p class="text-gray-600">147 Botanic Avenue</p>
                        <p class="text-gray-600">Belfast, Northern Ireland</p>
                        <p class="text-gray-600">BT7 1JJ</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>