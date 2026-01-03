<?php
// Admin Signup Page for Star-Clicks Clone
session_start();
include_once '../includes/config.php';
include_once '../includes/functions.php';

// Check if there are any admins in the system
$pdo = getDBConnection();
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'admin'");
$admin_count = $stmt->fetch();

// If there are existing admins, only allow admin users to create new admin accounts
if ($admin_count['count'] > 0) {
    if (!isLoggedIn() || !isAdmin()) {
        redirect('../portal/signin.php');
    }
    $user = getCurrentUser();
    if (!$user) {
        redirect('../portal/signin.php');
    }
} else {
    // If no admins exist, allow anyone (even non-logged-in users) to create the first admin account
    $user = null;
    // Check if user is already logged in to prefill their info
    if (isLoggedIn()) {
        $user = getCurrentUser();
    }
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $first_name = sanitizeInput($_POST['first_name'] ?? '');
    $last_name = sanitizeInput($_POST['last_name'] ?? '');
    
    // Validation
    if (empty($email) || empty($password) || empty($confirm_password) || empty($first_name) || empty($last_name)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!isValidEmail($email)) {
        $error_message = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error_message = 'Email address already exists.';
        } else {
            // Create admin user
            $hashed_password = hashPassword($password);
            
            $stmt = $pdo->prepare("INSERT INTO users (email, password, first_name, last_name, user_type, status) VALUES (?, ?, ?, ?, 'admin', 'active')");
            $result = $stmt->execute([$email, $hashed_password, $first_name, $last_name]);
            
            if ($result) {
                $success_message = 'Admin account created successfully!';
                
                // Log registration activity
                $new_user_id = $pdo->lastInsertId();
                if ($user) {
                    logActivity($user['id'], 'admin_created', "Created new admin account for $email (ID: $new_user_id)");
                } else {
                    // First admin creation - no existing user to log activity for
                }
                
                // Send welcome email (placeholder)
                sendEmail($email, 'Admin Account Created - Star-Clicks Clone', 'Your admin account has been created successfully.');
            } else {
                $error_message = 'Error creating admin account. Please try again.';
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
    <title>Create Admin - Star-Clicks Admin</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-600">Star-Clicks Admin</h1>
            <p class="text-gray-600 mt-2">Create New Admin Account</p>
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
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" id="first_name" name="first_name" class="form-control" 
                       value="<?php echo isset($user) && $user ? escape($user['first_name']) : (isset($_POST['first_name']) ? escape($_POST['first_name']) : ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" id="last_name" name="last_name" class="form-control" 
                       value="<?php echo isset($user) && $user ? escape($user['last_name']) : (isset($_POST['last_name']) ? escape($_POST['last_name']) : ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?php echo isset($user) && $user ? escape($user['email']) : (isset($_POST['email']) ? escape($_POST['email']) : ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-primary w-full">Create Admin Account</button>
            </div>
        </form>
        
        <?php if (isLoggedIn()): ?>
        <div class="mt-6 text-center">
            <p class="text-gray-600">
                <a href="dashboard.php" class="text-blue-600 hover:underline">Back to Dashboard</a>
            </p>
        </div>
        <?php else: ?>
        <div class="mt-6 text-center">
            <p class="text-gray-600">
                <a href="../portal/dashboard.php" class="text-blue-600 hover:underline">Back to User Dashboard</a>
            </p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>