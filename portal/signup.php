<?php
// Sign Up Page for Star-Clicks Clone
session_start();
include_once '../includes/config.php';
include_once '../includes/functions.php';

// If user is already logged in, redirect to dashboard
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error_message = '';
$success_message = '';
$user_type = $_GET['action'] ?? 'p'; // Default to publisher ('p'), 'a' for advertiser

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $first_name = sanitizeInput($_POST['first_name'] ?? '');
    $last_name = sanitizeInput($_POST['last_name'] ?? '');
    $user_type = sanitizeInput($_POST['user_type'] ?? 'publisher');
    $captcha = sanitizeInput($_POST['captcha'] ?? '');
    
    // Validation
    if (empty($email) || empty($password) || empty($confirm_password) || empty($first_name) || empty($last_name)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!isValidEmail($email)) {
        $error_message = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } elseif (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error_message = 'Invalid CSRF token. Please try again.';
    } elseif (empty($captcha) || !isset($_SESSION['captcha']) || strtolower($captcha) !== strtolower($_SESSION['captcha'])) {
        $error_message = 'Invalid CAPTCHA. Please try again.';
    } else {
        // Validate user type
        $valid_user_types = ['p', 'a', 'publisher', 'advertiser'];
        if (!in_array($user_type, $valid_user_types)) {
            $user_type = 'p'; // Default to publisher
        }
        
        // Check if email already exists
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error_message = 'Email address already exists.';
        } else {
            // Create user
            $hashed_password = hashPassword($password);
            
            // Determine user type based on input or default
            $db_user_type = ($user_type === 'a' || $user_type === 'advertiser') ? 'advertiser' : 'publisher';
            
            $stmt = $pdo->prepare("INSERT INTO users (email, password, first_name, last_name, user_type, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $result = $stmt->execute([$email, $hashed_password, $first_name, $last_name, $db_user_type]);
            
            if ($result) {
                $success_message = 'Account created successfully! Your account is pending approval.';
                
                // Log registration activity
                $new_user_id = $pdo->lastInsertId();
                logActivity($new_user_id, 'registration', 'New user registered with email: ' . $email);
                
                // Clear the CAPTCHA session
                unset($_SESSION['captcha']);
                
                // Send welcome email (placeholder)
                sendEmail($email, 'Welcome to Star-Clicks Clone', 'Thank you for registering with us. Your account is pending approval.');
            } else {
                $error_message = 'Error creating account. Please try again.';
            }
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
    <title>Sign Up - Star-Clicks Clone</title>
    
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
            <h1 class="text-3xl font-bold text-blue-600">Star-Clicks</h1>
            <p class="text-gray-600 mt-2">
                <?php echo ($user_type === 'a') ? 'Sign up as Advertiser' : 'Sign up as Publisher'; ?>
            </p>
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
            <input type="hidden" name="user_type" value="<?php echo escape($user_type); ?>">
            
            <div class="form-group">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" id="first_name" name="first_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" id="last_name" name="last_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required>
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
                <label for="captcha" class="form-label">Captcha</label>
                <div class="flex items-center">
                    <span id="captcha_text" class="mr-3 bg-gray-200 px-4 py-2 rounded border font-mono text-lg"></span>
                    <input type="text" id="captcha" name="captcha" class="form-control flex-grow" required placeholder="Enter the code">
                    <button type="button" class="ml-2 bg-blue-500 hover:bg-blue-600 text-white p-2 rounded" onclick="refreshCaptcha()" title="Refresh Captcha">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            
            <script>
                function refreshCaptcha() {
                    fetch('../api/captcha.php')
                        .then(response => response.text())
                        .then(data => {
                            document.getElementById('captcha_text').textContent = data;
                        });
                }
                
                // Load initial CAPTCHA
                document.addEventListener('DOMContentLoaded', function() {
                    refreshCaptcha();
                });
            </script>
            
            <div class="form-group">
                <button type="submit" class="btn-primary w-full">
                    Sign Up as <?php echo ($user_type === 'a') ? 'Advertiser' : 'Publisher'; ?>
                </button>
            </div>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-gray-600">
                Already have an account? 
                <a href="signin.php" class="text-blue-600 hover:underline">Sign in here</a>
            </p>
        </div>
    </div>
</body>
</html>