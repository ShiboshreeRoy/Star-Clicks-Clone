<?php
// Admin Request Form for Star-Clicks Clone
session_start();
include_once '../includes/config.php';
include_once '../includes/functions.php';

// Check if user is logged in (any user type can request admin access)
if (!isLoggedIn()) {
    redirect('../portal/signin.php');
}

$user = getCurrentUser();
if (!$user) {
    redirect('../portal/signin.php');
}

$error_message = '';
$success_message = '';

// Handle admin request submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reason = sanitizeInput($_POST['reason'] ?? '');
    $experience = sanitizeInput($_POST['experience'] ?? '');
    
    // Validation
    if (empty($reason) || strlen($reason) < 10) {
        $error_message = 'Please provide a detailed reason for requesting admin access (at least 10 characters).';
    } elseif (empty($experience) || strlen($experience) < 10) {
        $error_message = 'Please describe your experience (at least 10 characters).';
    } else {
        // Check if user already has an active admin request
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id FROM admin_requests WHERE user_id = ? AND status = 'pending'");
        $stmt->execute([$user['id']]);
        
        if ($stmt->rowCount() > 0) {
            $error_message = 'You already have a pending admin request. Please wait for it to be reviewed.';
        } else {
            // Insert admin request
            $stmt = $pdo->prepare("
                INSERT INTO admin_requests (user_id, reason, experience, status, requested_at) 
                VALUES (?, ?, ?, 'pending', NOW())
            ");
            $result = $stmt->execute([$user['id'], $reason, $experience]);
            
            if ($result) {
                $success_message = 'Your admin request has been submitted successfully. An existing admin will review your request.';
                logActivity($user['id'], 'admin_request_submitted', "Submitted admin access request");
            } else {
                $error_message = 'Error submitting admin request. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Admin Access - Star-Clicks Clone</title>
    
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
                    <a href="../portal/dashboard.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Dashboard</a>
                    <a href="../portal/profile.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Profile</a>
                    <a href="../portal/logout.php" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Request Admin Access</h1>
            </div>
            
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
            
            <div class="card">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Admin Access Request Form</h2>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h3 class="font-bold text-yellow-800 mb-2">Important Information</h3>
                    <p class="text-yellow-700">Admin access is granted only to trusted individuals who demonstrate the need for such privileges. Admin access provides full control over the platform, including user management, ad management, and system settings.</p>
                </div>
                
                <form method="POST" action="">
                    <div class="form-group mb-6">
                        <label for="reason" class="form-label">Reason for Requesting Admin Access</label>
                        <textarea id="reason" name="reason" class="form-control" rows="4" placeholder="Please explain why you need admin access and what you plan to do with it..."><?php echo isset($_POST['reason']) ? escape($_POST['reason']) : ''; ?></textarea>
                        <p class="text-sm text-gray-500 mt-1">Describe in detail why you need admin access</p>
                    </div>
                    
                    <div class="form-group mb-6">
                        <label for="experience" class="form-label">Relevant Experience</label>
                        <textarea id="experience" name="experience" class="form-control" rows="4" placeholder="Please describe your experience with managing systems, websites, or similar responsibilities..."><?php echo isset($_POST['experience']) ? escape($_POST['experience']) : ''; ?></textarea>
                        <p class="text-sm text-gray-500 mt-1">Describe your experience that qualifies you for admin access</p>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>Submit Request
                        </button>
                        <a href="../portal/dashboard.php" class="btn-secondary ml-2">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>