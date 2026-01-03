<?php
// Create Ad Page for Star-Clicks Clone
session_start();
include_once '../includes/config.php';
include_once '../includes/functions.php';

// Check if user is logged in and is an advertiser
if (!isLoggedIn() || !isAdvertiser()) {
    redirect('signin.php');
}

$user = getCurrentUser();
if (!$user) {
    redirect('signin.php');
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $url = sanitizeInput($_POST['url'] ?? '');
    $cpc = floatval($_POST['cpc'] ?? 0);
    $daily_budget = floatval($_POST['daily_budget'] ?? 0);
    $start_date = sanitizeInput($_POST['start_date'] ?? '');
    $end_date = sanitizeInput($_POST['end_date'] ?? '');
    
    // Validation
    if (empty($title) || empty($url) || $cpc <= 0 || $daily_budget <= 0) {
        $error_message = 'Please fill in all required fields with valid values.';
    } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
        $error_message = 'Please enter a valid URL.';
    } elseif ($cpc < 0.01) {
        $error_message = 'Cost per click must be at least $0.01.';
    } elseif ($daily_budget < 5.00) {
        $error_message = 'Daily budget must be at least $5.00.';
    } elseif (strtotime($start_date) > strtotime($end_date)) {
        $error_message = 'Start date must be before end date.';
    } elseif (strtotime($start_date) < strtotime(date('Y-m-d'))) {
        $error_message = 'Start date cannot be in the past.';
    } else {
        // Check if user has sufficient balance
        if ($user['balance'] < $daily_budget) {
            $error_message = 'Insufficient balance. Your current balance is ' . formatCurrency($user['balance']) . '.';
        } else {
            $pdo = getDBConnection();
            
            // Insert advertisement
            $stmt = $pdo->prepare("
                INSERT INTO advertisements 
                (advertiser_id, title, description, url, cpc, daily_budget, start_date, end_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')
            ");
            $result = $stmt->execute([$user['id'], $title, $description, $url, $cpc, $daily_budget, $start_date, $end_date]);
            
            if ($result) {
                $success_message = 'Advertisement created successfully!';
                
                // Deduct budget from user's balance
                $new_balance = $user['balance'] - $daily_budget;
                $updateStmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
                $updateStmt->execute([$new_balance, $user['id']]);
                
                logActivity($user['id'], 'ad_created', "Created ad: $title");
            } else {
                $error_message = 'Error creating advertisement. Please try again.';
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
    <title>Create Ad - Star-Clicks Clone</title>
    
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
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Create New Advertisement</h1>
            
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
                <form method="POST" action="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="title" class="form-label">Ad Title *</label>
                            <input type="text" id="title" name="title" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="url" class="form-label">Destination URL *</label>
                            <input type="url" id="url" name="url" class="form-control" required>
                            <p class="text-sm text-gray-500 mt-1">The URL users will be redirected to when they click the ad</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="cpc" class="form-label">Cost Per Click (CPC) *</label>
                            <input type="number" id="cpc" name="cpc" class="form-control" step="0.01" min="0.01" value="0.01" required>
                            <p class="text-sm text-gray-500 mt-1">Amount you pay for each valid click</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="daily_budget" class="form-label">Daily Budget *</label>
                            <input type="number" id="daily_budget" name="daily_budget" class="form-control" step="0.01" min="5.00" value="5.00" required>
                            <p class="text-sm text-gray-500 mt-1">Maximum amount to spend per day</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="start_date" class="form-label">Start Date *</label>
                            <input type="date" id="start_date" name="start_date" class="form-control" 
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="end_date" class="form-label">End Date *</label>
                            <input type="date" id="end_date" name="end_date" class="form-control" 
                                   value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                        <p class="text-sm text-gray-500 mt-1">Optional description for your ad</p>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded-lg mb-4">
                        <h3 class="font-bold text-blue-800 mb-2">Account Information</h3>
                        <p class="text-blue-700">Current Balance: <strong><?php echo formatCurrency($user['balance']); ?></strong></p>
                        <p class="text-blue-700">Minimum Daily Budget: <strong>$5.00</strong></p>
                        <p class="text-blue-700">Minimum CPC: <strong>$0.01</strong></p>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn-primary">Create Advertisement</button>
                        <a href="manage_ads.php" class="btn-secondary ml-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>