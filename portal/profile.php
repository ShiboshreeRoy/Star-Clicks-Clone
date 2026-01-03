<?php
// Profile Page for Star-Clicks Clone
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = sanitizeInput($_POST['first_name'] ?? '');
    $last_name = sanitizeInput($_POST['last_name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $country = sanitizeInput($_POST['country'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    $paypal_email = sanitizeInput($_POST['paypal_email'] ?? '');
    
    // Validation
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!isValidEmail($email)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Check if email is already taken by another user
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user['id']]);
        
        if ($stmt->rowCount() > 0) {
            $error_message = 'Email address is already in use.';
        } else {
            // Update user profile
            $stmt = $pdo->prepare("
                UPDATE users 
                SET first_name = ?, last_name = ?, email = ?, country = ?, address = ?, paypal_email = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            $result = $stmt->execute([$first_name, $last_name, $email, $country, $address, $paypal_email, $user['id']]);
            
            if ($result) {
                $success_message = 'Profile updated successfully!';
                
                // Update session if email changed
                if ($user['email'] !== $email) {
                    $_SESSION['email'] = $email;
                }
                
                // Update user object
                $user['first_name'] = $first_name;
                $user['last_name'] = $last_name;
                $user['email'] = $email;
                $user['country'] = $country;
                $user['address'] = $address;
                $user['paypal_email'] = $paypal_email;
                
                logActivity($user['id'], 'profile_update', 'User updated their profile information');
            } else {
                $error_message = 'Error updating profile. Please try again.';
            }
        }
    }
}

// For password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';
    
    if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
        $error_message = 'Please fill in all password fields.';
    } elseif ($new_password !== $confirm_new_password) {
        $error_message = 'New passwords do not match.';
    } elseif (strlen($new_password) < 6) {
        $error_message = 'New password must be at least 6 characters long.';
    } else {
        // Verify current password
        if (verifyPassword($current_password, $user['password'])) {
            // Update password
            $hashed_new_password = hashPassword($new_password);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $result = $stmt->execute([$hashed_new_password, $user['id']]);
            
            if ($result) {
                $success_message = 'Password updated successfully!';
                logActivity($user['id'], 'password_change', 'User changed their password');
            } else {
                $error_message = 'Error updating password. Please try again.';
            }
        } else {
            $error_message = 'Current password is incorrect.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Star-Clicks Clone</title>
    
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
                    <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Profile Settings</h1>
            
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
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Profile Information Form -->
                <div class="card">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Profile Information</h2>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" id="first_name" name="first_name" class="form-control" 
                                   value="<?php echo escape($user['first_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" 
                                   value="<?php echo escape($user['last_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo escape($user['email']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" id="country" name="country" class="form-control" 
                                   value="<?php echo escape($user['country']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="address" class="form-label">Address</label>
                            <textarea id="address" name="address" class="form-control" rows="3"><?php echo escape($user['address']); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="paypal_email" class="form-label">PayPal Email</label>
                            <input type="email" id="paypal_email" name="paypal_email" class="form-control" 
                                   value="<?php echo escape($user['paypal_email']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
                
                <!-- Change Password Form -->
                <div class="card">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Change Password</h2>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                            <input type="password" id="confirm_new_password" name="confirm_new_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="change_password" class="btn-primary">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Account Information -->
            <div class="card mt-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Account Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-500">Account Type</p>
                        <p class="font-medium"><?php echo ucfirst(escape($user['user_type'])); ?></p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-500">Account Status</p>
                        <p class="font-medium"><?php echo ucfirst(escape($user['status'])); ?></p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-500">Balance</p>
                        <p class="font-medium"><?php echo formatCurrency($user['balance']); ?></p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-500">Membership Type</p>
                        <p class="font-medium"><?php echo ucfirst(escape($user['membership_type'])); ?></p>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded md:col-span-2">
                        <p class="text-sm text-gray-500">Member Since</p>
                        <p class="font-medium"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>