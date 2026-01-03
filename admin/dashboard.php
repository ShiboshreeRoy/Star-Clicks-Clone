<?php
// Admin Dashboard for Star-Clicks Clone
session_start();
include_once '../includes/config.php';
include_once '../includes/functions.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('signup.php');
}

$user = getCurrentUser();
if (!$user) {
    redirect('../portal/signin.php');
}

$pdo = getDBConnection();

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
$total_users = $stmt->fetch();

$stmt = $pdo->query("SELECT COUNT(*) as total_ads FROM advertisements");
$total_ads = $stmt->fetch();

$stmt = $pdo->query("SELECT COUNT(*) as total_clicks FROM ad_clicks");
$total_clicks = $stmt->fetch();

$stmt = $pdo->query("SELECT SUM(amount_earned) as total_earnings FROM ad_clicks WHERE is_valid = 1");
$total_earnings = $stmt->fetch();

$stmt = $pdo->query("SELECT COUNT(*) as pending_withdrawals FROM withdrawals WHERE status = 'pending'");
$pending_withdrawals = $stmt->fetch();

// Get recent users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
$recent_users = $stmt->fetchAll();

// Get recent clicks
$stmt = $pdo->query("
    SELECT ac.*, u.email as publisher_email, a.title as ad_title 
    FROM ad_clicks ac
    JOIN users u ON ac.publisher_id = u.id
    JOIN advertisements a ON ac.ad_id = a.id
    ORDER BY ac.clicked_at DESC
    LIMIT 5
");
$recent_clicks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Star-Clicks Clone</title>
    
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
                        <span class="text-2xl font-bold text-blue-600">Star-Clicks Admin</span>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <span class="text-gray-700">Welcome, <?php echo escape($user['first_name'] . ' ' . $user['last_name']); ?></span>
                    <a href="dashboard.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Dashboard</a>
                    <a href="signup.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Create Admin</a>
                    <a href="../portal/profile.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Profile</a>
                    <a href="../portal/logout.php" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Admin Dashboard</h1>
            
            <!-- Stats Cards -->
            <div class="dashboard-stats mb-8">
                <div class="stat-card bg-blue-50 border border-blue-100">
                    <div class="stat-title">Total Users</div>
                    <div class="stat-value text-blue-600"><?php echo $total_users['total_users']; ?></div>
                </div>
                
                <div class="stat-card bg-green-50 border border-green-100">
                    <div class="stat-title">Total Ads</div>
                    <div class="stat-value text-green-600"><?php echo $total_ads['total_ads']; ?></div>
                </div>
                
                <div class="stat-card bg-yellow-50 border border-yellow-100">
                    <div class="stat-title">Total Clicks</div>
                    <div class="stat-value text-yellow-600"><?php echo $total_clicks['total_clicks']; ?></div>
                </div>
                
                <div class="stat-card bg-purple-50 border border-purple-100">
                    <div class="stat-title">Total Earnings</div>
                    <div class="stat-value text-purple-600"><?php echo formatCurrency($total_earnings['total_earnings'] ?? 0); ?></div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Users -->
                <div class="card">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Recent Users</h2>
                        <a href="users.php" class="text-blue-600 hover:underline">View All</a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_users as $usr): ?>
                                    <tr>
                                        <td><?php echo escape($usr['email']); ?></td>
                                        <td><?php echo ucfirst(escape($usr['user_type'])); ?></td>
                                        <td>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                <?php 
                                                if ($usr['status'] === 'active') echo 'bg-green-100 text-green-800';
                                                elseif ($usr['status'] === 'pending') echo 'bg-yellow-100 text-yellow-800';
                                                elseif ($usr['status'] === 'suspended') echo 'bg-red-100 text-red-800';
                                                else echo 'bg-gray-100 text-gray-800';
                                                ?>">
                                                <?php echo ucfirst(escape($usr['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j', strtotime($usr['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Recent Clicks -->
                <div class="card">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Recent Clicks</h2>
                        <a href="clicks.php" class="text-blue-600 hover:underline">View All</a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Publisher</th>
                                    <th>Ad</th>
                                    <th>Earned</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_clicks as $click): ?>
                                    <tr>
                                        <td><?php echo escape($click['publisher_email']); ?></td>
                                        <td><?php echo escape($click['ad_title']); ?></td>
                                        <td><?php echo formatCurrency($click['amount_earned']); ?></td>
                                        <td><?php echo date('M j, g:i A', strtotime($click['clicked_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Admin Actions -->
            <div class="card mt-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Admin Actions</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="users.php" class="bg-blue-100 hover:bg-blue-200 p-4 rounded-lg text-center transition duration-300">
                        <i class="fas fa-users text-2xl text-blue-600 mb-2"></i>
                        <h3 class="font-semibold">Manage Users</h3>
                    </a>
                    
                    <a href="ads.php" class="bg-green-100 hover:bg-green-200 p-4 rounded-lg text-center transition duration-300">
                        <i class="fas fa-ad text-2xl text-green-600 mb-2"></i>
                        <h3 class="font-semibold">Manage Ads</h3>
                    </a>
                    
                    <a href="withdrawals.php" class="bg-yellow-100 hover:bg-yellow-200 p-4 rounded-lg text-center transition duration-300">
                        <i class="fas fa-money-bill-wave text-2xl text-yellow-600 mb-2"></i>
                        <h3 class="font-semibold">Withdrawals</h3>
                    </a>
                    
                    <a href="clicks.php" class="bg-purple-100 hover:bg-purple-200 p-4 rounded-lg text-center transition duration-300">
                        <i class="fas fa-mouse-pointer text-2xl text-purple-600 mb-2"></i>
                        <h3 class="font-semibold">Click Reports</h3>
                    </a>
                    
                    <a href="settings.php" class="bg-red-100 hover:bg-red-200 p-4 rounded-lg text-center transition duration-300">
                        <i class="fas fa-cog text-2xl text-red-600 mb-2"></i>
                        <h3 class="font-semibold">Settings</h3>
                    </a>
                    
                    <a href="reports.php" class="bg-indigo-100 hover:bg-indigo-200 p-4 rounded-lg text-center transition duration-300">
                        <i class="fas fa-chart-bar text-2xl text-indigo-600 mb-2"></i>
                        <h3 class="font-semibold">Reports</h3>
                    </a>
                    
                    <a href="admin_requests.php" class="bg-pink-100 hover:bg-pink-200 p-4 rounded-lg text-center transition duration-300">
                        <i class="fas fa-user-shield text-2xl text-pink-600 mb-2"></i>
                        <h3 class="font-semibold">Admin Requests</h3>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>