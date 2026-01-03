<?php
// Admin Login Page for Star-Clicks Clone
session_start();
include_once '../includes/config.php';
include_once '../includes/functions.php';

// If admin is already logged in, redirect to admin dashboard
if (isLoggedIn() && isAdmin()) {
    redirect('dashboard.php');
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha = sanitizeInput($_POST['captcha'] ?? '');
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $error_message = 'Please fill in all fields.';
    } elseif (!isValidEmail($email)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Verify user credentials
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND user_type = 'admin'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && verifyPassword($password, $user['password'])) {
            if ($user['status'] === 'suspended') {
                $error_message = 'Your admin account has been suspended.';
            } elseif (empty($captcha) || !isset($_SESSION['captcha']) || strtolower($captcha) !== strtolower($_SESSION['captcha'])) {
                $error_message = 'Invalid CAPTCHA. Please try again.';
            } else {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['email'] = $user['email'];
                
                // Clear the CAPTCHA session
                unset($_SESSION['captcha']);
                
                // Update last login
                $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                // Log activity
                logActivity($user['id'], 'admin_login', 'Admin logged in from IP: ' . $_SERVER['REMOTE_ADDR']);
                
                // Redirect to admin dashboard
                redirect('dashboard.php');
            }
        } else {
            $error_message = 'Invalid admin credentials.';
        }
    }
}

// Generate CSRF token
$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Star-Clicks Clone</title>
    
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
            <p class="text-gray-600 mt-2">Admin Sign In</p>
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
            <input type="hidden" name="csrf_token" value="<?php echo escape($csrf_token); ?>">
            
            <div class="form-group">
                <label for="email" class="form-label">Admin Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="captcha" class="form-label">Captcha</label>
                <div class="flex items-center">
                    <img src="../api/captcha.php" alt="Captcha" class="mr-3 border rounded">
                    <input type="text" id="captcha" name="captcha" class="form-control flex-grow" required>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-primary w-full">Sign In as Admin</button>
            </div>
        </form>
        
        <div class="mt-6 text-center space-y-2">
            <p class="text-gray-600">
                <a href="../portal/signin.php" class="text-blue-600 hover:underline">Back to User Login</a>
            </p>
            <p class="text-gray-600">
                <a href="signup.php" class="text-blue-600 hover:underline">Create New Admin Account</a>
            </p>
        </div>
    </div>
</body>
</html>