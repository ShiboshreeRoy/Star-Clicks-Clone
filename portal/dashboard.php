<?php
// Dashboard Page for Star-Clicks Clone
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

// Get user statistics
$pdo = getDBConnection();

// For publishers
if ($user['user_type'] === 'publisher') {
    // Get total earnings
    $stmt = $pdo->prepare("
        SELECT SUM(amount_earned) as total_earnings 
        FROM ad_clicks 
        WHERE publisher_id = ? AND is_valid = 1
    ");
    $stmt->execute([$user['id']]);
    $total_earnings = $stmt->fetch();
    
    // Get today's earnings
    $stmt = $pdo->prepare("
        SELECT SUM(amount_earned) as today_earnings 
        FROM ad_clicks 
        WHERE publisher_id = ? AND DATE(clicked_at) = CURDATE() AND is_valid = 1
    ");
    $stmt->execute([$user['id']]);
    $today_earnings = $stmt->fetch();
    
    // Get total clicks
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total_clicks 
        FROM ad_clicks 
        WHERE publisher_id = ? AND is_valid = 1
    ");
    $stmt->execute([$user['id']]);
    $total_clicks = $stmt->fetch();
    
    // Get pending withdrawals
    $stmt = $pdo->prepare("
        SELECT SUM(amount) as pending_withdrawals 
        FROM withdrawals 
        WHERE user_id = ? AND status = 'pending'
    ");
    $stmt->execute([$user['id']]);
    $pending_withdrawals = $stmt->fetch();
}

// For advertisers
if ($user['user_type'] === 'advertiser') {
    // Get total spent
    $stmt = $pdo->prepare("
        SELECT SUM(amount_paid) as total_spent 
        FROM ad_clicks 
        WHERE ad_id IN (
            SELECT id FROM advertisements WHERE advertiser_id = ?
        )
    ");
    $stmt->execute([$user['id']]);
    $total_spent = $stmt->fetch();
    
    // Get active ads
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as active_ads 
        FROM advertisements 
        WHERE advertiser_id = ? AND status = 'active'
    ");
    $stmt->execute([$user['id']]);
    $active_ads = $stmt->fetch();
    
    // Get total clicks on ads
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total_ad_clicks 
        FROM ad_clicks 
        WHERE ad_id IN (
            SELECT id FROM advertisements WHERE advertiser_id = ?
        )
    ");
    $stmt->execute([$user['id']]);
    $total_ad_clicks = $stmt->fetch();
}

// Log dashboard access
logActivity($user['id'], 'dashboard_access', 'User accessed dashboard');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Star-Clicks Clone</title>
    
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
                    <a href="profile.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Profile</a>
                    <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Dashboard</h1>
            
            <!-- Stats Section -->
            <div class="dashboard-stats">
                <div class="stat-card bg-blue-50 border border-blue-100">
                    <div class="stat-title">Current Balance</div>
                    <div class="stat-value text-blue-600"><?php echo formatCurrency($user['balance']); ?></div>
                </div>
                
                <?php if ($user['user_type'] === 'publisher'): ?>
                    <div class="stat-card bg-green-50 border border-green-100">
                        <div class="stat-title">Total Earnings</div>
                        <div class="stat-value text-green-600"><?php echo formatCurrency($total_earnings['total_earnings'] ?? 0); ?></div>
                    </div>
                    
                    <div class="stat-card bg-yellow-50 border border-yellow-100">
                        <div class="stat-title">Today's Earnings</div>
                        <div class="stat-value text-yellow-600"><?php echo formatCurrency($today_earnings['today_earnings'] ?? 0); ?></div>
                    </div>
                    
                    <div class="stat-card bg-purple-50 border border-purple-100">
                        <div class="stat-title">Total Clicks</div>
                        <div class="stat-value text-purple-600"><?php echo $total_clicks['total_clicks'] ?? 0; ?></div>
                    </div>
                <?php elseif ($user['user_type'] === 'advertiser'): ?>
                    <div class="stat-card bg-red-50 border border-red-100">
                        <div class="stat-title">Total Spent</div>
                        <div class="stat-value text-red-600"><?php echo formatCurrency($total_spent['total_spent'] ?? 0); ?></div>
                    </div>
                    
                    <div class="stat-card bg-indigo-50 border border-indigo-100">
                        <div class="stat-title">Active Ads</div>
                        <div class="stat-value text-indigo-600"><?php echo $active_ads['active_ads'] ?? 0; ?></div>
                    </div>
                    
                    <div class="stat-card bg-pink-50 border border-pink-100">
                        <div class="stat-title">Total Ad Clicks</div>
                        <div class="stat-value text-pink-600"><?php echo $total_ad_clicks['total_ad_clicks'] ?? 0; ?></div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Publisher Section -->
            <?php if ($user['user_type'] === 'publisher'): ?>
                <div class="card mb-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Publisher Tools</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="click_ads.php" class="bg-blue-100 hover:bg-blue-200 p-6 rounded-lg text-center transition duration-300">
                            <i class="fas fa-mouse-pointer text-3xl text-blue-600 mb-2"></i>
                            <h3 class="font-semibold text-lg">Click Ads</h3>
                            <p class="text-gray-600 mt-2">Earn money by clicking ads</p>
                        </a>
                        
                        <a href="withdrawals.php" class="bg-green-100 hover:bg-green-200 p-6 rounded-lg text-center transition duration-300">
                            <i class="fas fa-money-bill-wave text-3xl text-green-600 mb-2"></i>
                            <h3 class="font-semibold text-lg">Withdraw Funds</h3>
                            <p class="text-gray-600 mt-2">Withdraw your earnings</p>
                        </a>
                        
                        <a href="referrals.php" class="bg-purple-100 hover:bg-purple-200 p-6 rounded-lg text-center transition duration-300">
                            <i class="fas fa-users text-3xl text-purple-600 mb-2"></i>
                            <h3 class="font-semibold text-lg">Referrals</h3>
                            <p class="text-gray-600 mt-2">Invite others and earn commissions</p>
                        </a>
                        
                        <a href="../admin/request_admin.php" class="bg-pink-100 hover:bg-pink-200 p-6 rounded-lg text-center transition duration-300 col-start-2">
                            <i class="fas fa-user-shield text-3xl text-pink-600 mb-2"></i>
                            <h3 class="font-semibold text-lg">Request Admin</h3>
                            <p class="text-gray-600 mt-2">Request admin access</p>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Advertiser Section -->
            <?php if ($user['user_type'] === 'advertiser'): ?>
                <div class="card mb-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Advertiser Tools</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="create_ad.php" class="bg-blue-100 hover:bg-blue-200 p-6 rounded-lg text-center transition duration-300">
                            <i class="fas fa-ad text-3xl text-blue-600 mb-2"></i>
                            <h3 class="font-semibold text-lg">Create Ad</h3>
                            <p class="text-gray-600 mt-2">Create a new advertisement</p>
                        </a>
                        
                        <a href="manage_ads.php" class="bg-yellow-100 hover:bg-yellow-200 p-6 rounded-lg text-center transition duration-300">
                            <i class="fas fa-tasks text-3xl text-yellow-600 mb-2"></i>
                            <h3 class="font-semibold text-lg">Manage Ads</h3>
                            <p class="text-gray-600 mt-2">View and manage your ads</p>
                        </a>
                        
                        <a href="ad_stats.php" class="bg-green-100 hover:bg-green-200 p-6 rounded-lg text-center transition duration-300">
                            <i class="fas fa-chart-bar text-3xl text-green-600 mb-2"></i>
                            <h3 class="font-semibold text-lg">Ad Statistics</h3>
                            <p class="text-gray-600 mt-2">View ad performance</p>
                        </a>
                        
                        <a href="../admin/request_admin.php" class="bg-pink-100 hover:bg-pink-200 p-6 rounded-lg text-center transition duration-300 col-start-2">
                            <i class="fas fa-user-shield text-3xl text-pink-600 mb-2"></i>
                            <h3 class="font-semibold text-lg">Request Admin</h3>
                            <p class="text-gray-600 mt-2">Request admin access</p>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Recent Activity -->
            <div class="card">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Activity</h2>
                
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Activity</th>
                                <th>Details</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->prepare("
                                SELECT activity, details, created_at 
                                FROM user_activities 
                                WHERE user_id = ? 
                                ORDER BY created_at DESC 
                                LIMIT 5
                            ");
                            $stmt->execute([$user['id']]);
                            $activities = $stmt->fetchAll();
                            
                            foreach ($activities as $activity):
                            ?>
                                <tr>
                                    <td><?php echo escape($activity['activity']); ?></td>
                                    <td><?php echo escape($activity['details']); ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($activities)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-gray-500 py-4">No recent activity</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>