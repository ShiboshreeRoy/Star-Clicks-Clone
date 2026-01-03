<?php
// Admin Reports Page for Star-Clicks Clone
session_start();
include_once '../includes/config.php';
include_once '../includes/functions.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../portal/signin.php');
}

$user = getCurrentUser();
if (!$user) {
    redirect('../portal/signin.php');
}

$pdo = getDBConnection();

// Get overall stats
$stmt = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM users) as total_users,
        (SELECT COUNT(*) FROM users WHERE user_type = 'publisher') as total_publishers,
        (SELECT COUNT(*) FROM users WHERE user_type = 'advertiser') as total_advertisers,
        (SELECT COUNT(*) FROM advertisements) as total_ads,
        (SELECT COUNT(*) FROM ad_clicks) as total_clicks,
        (SELECT SUM(amount_earned) FROM ad_clicks WHERE is_valid = 1) as total_publisher_earnings,
        (SELECT SUM(amount_paid) FROM ad_clicks WHERE is_valid = 1) as total_advertiser_spent,
        (SELECT COUNT(*) FROM withdrawals) as total_withdrawals,
        (SELECT SUM(amount) FROM withdrawals WHERE status = 'completed') as total_paid_out
");
$stats = $stmt->fetch();

// Get today's stats
$stmt = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()) as new_users_today,
        (SELECT COUNT(*) FROM advertisements WHERE DATE(created_at) = CURDATE()) as new_ads_today,
        (SELECT COUNT(*) FROM ad_clicks WHERE DATE(clicked_at) = CURDATE()) as clicks_today,
        (SELECT SUM(amount_earned) FROM ad_clicks WHERE DATE(clicked_at) = CURDATE() AND is_valid = 1) as earnings_today,
        (SELECT COUNT(*) FROM withdrawals WHERE DATE(requested_at) = CURDATE()) as withdrawals_today
");
$todays_stats = $stmt->fetch();

// Get user growth data for chart (last 7 days)
$stmt = $pdo->query("
    SELECT DATE(created_at) as date, COUNT(*) as count
    FROM users 
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date
");
$user_growth = $stmt->fetchAll();

// Get ad clicks data for chart (last 7 days)
$stmt = $pdo->query("
    SELECT DATE(clicked_at) as date, COUNT(*) as count
    FROM ad_clicks 
    WHERE clicked_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(clicked_at)
    ORDER BY date
");
$clicks_data = $stmt->fetchAll();

// Get earnings data for chart (last 7 days)
$stmt = $pdo->query("
    SELECT DATE(clicked_at) as date, SUM(amount_earned) as total
    FROM ad_clicks 
    WHERE clicked_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(clicked_at)
    ORDER BY date
");
$earnings_data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Star-Clicks Admin</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Chart.js for visualizations -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <a href="../portal/profile.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Profile</a>
                    <a href="../portal/logout.php" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Reports & Analytics</h1>
            </div>
            
            <!-- Stats Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-blue-600"><?php echo $stats['total_users']; ?></div>
                    <div class="text-gray-600">Total Users</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-green-600"><?php echo $stats['total_publishers']; ?></div>
                    <div class="text-gray-600">Publishers</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-yellow-600"><?php echo $stats['total_advertisers']; ?></div>
                    <div class="text-gray-600">Advertisers</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-purple-600"><?php echo $stats['total_ads']; ?></div>
                    <div class="text-gray-600">Total Ads</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-red-600"><?php echo $stats['total_clicks']; ?></div>
                    <div class="text-gray-600">Total Clicks</div>
                </div>
            </div>
            
            <!-- Earnings Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-green-600"><?php echo formatCurrency($stats['total_publisher_earnings']); ?></div>
                    <div class="text-gray-600">Total Publisher Earnings</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-red-600"><?php echo formatCurrency($stats['total_advertiser_spent']); ?></div>
                    <div class="text-gray-600">Total Advertiser Spent</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-blue-600"><?php echo formatCurrency($stats['total_paid_out']); ?></div>
                    <div class="text-gray-600">Total Paid Out</div>
                </div>
            </div>
            
            <!-- Today's Stats -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-blue-600"><?php echo $todays_stats['new_users_today']; ?></div>
                    <div class="text-gray-600">New Users Today</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-green-600"><?php echo $todays_stats['new_ads_today']; ?></div>
                    <div class="text-gray-600">New Ads Today</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-yellow-600"><?php echo $todays_stats['clicks_today']; ?></div>
                    <div class="text-gray-600">Clicks Today</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-purple-600"><?php echo formatCurrency($todays_stats['earnings_today']); ?></div>
                    <div class="text-gray-600">Earnings Today</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-2xl font-bold text-red-600"><?php echo $todays_stats['withdrawals_today']; ?></div>
                    <div class="text-gray-600">Withdrawals Today</div>
                </div>
            </div>
            
            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- User Growth Chart -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">User Growth (Last 7 Days)</h3>
                    <canvas id="userGrowthChart"></canvas>
                </div>
                
                <!-- Clicks Chart -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Ad Clicks (Last 7 Days)</h3>
                    <canvas id="clicksChart"></canvas>
                </div>
            </div>
            
            <!-- Earnings Chart -->
            <div class="bg-white p-6 rounded-lg shadow mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Daily Earnings (Last 7 Days)</h3>
                <canvas id="earningsChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Prepare data for charts
        const userGrowthData = {
            labels: [
                <?php foreach ($user_growth as $data): ?>
                    '<?php echo date('M j', strtotime($data['date'])); ?>',
                <?php endforeach; ?>
            ],
            datasets: [{
                label: 'New Users',
                data: [
                    <?php foreach ($user_growth as $data): ?>
                        <?php echo $data['count']; ?>,
                    <?php endforeach; ?>
                ],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                tension: 0.1
            }]
        };

        const clicksData = {
            labels: [
                <?php foreach ($clicks_data as $data): ?>
                    '<?php echo date('M j', strtotime($data['date'])); ?>',
                <?php endforeach; ?>
            ],
            datasets: [{
                label: 'Ad Clicks',
                data: [
                    <?php foreach ($clicks_data as $data): ?>
                        <?php echo $data['count']; ?>,
                    <?php endforeach; ?>
                ],
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                tension: 0.1
            }]
        };

        const earningsData = {
            labels: [
                <?php foreach ($earnings_data as $data): ?>
                    '<?php echo date('M j', strtotime($data['date'])); ?>',
                <?php endforeach; ?>
            ],
            datasets: [{
                label: 'Earnings',
                data: [
                    <?php foreach ($earnings_data as $data): ?>
                        <?php echo $data['total'] ?: 0; ?>,
                    <?php endforeach; ?>
                ],
                borderColor: 'rgb(234, 179, 8)',
                backgroundColor: 'rgba(234, 179, 8, 0.2)',
                tension: 0.1
            }]
        };

        // Create charts
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        const userGrowthChart = new Chart(userGrowthCtx, {
            type: 'line',
            data: userGrowthData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const clicksCtx = document.getElementById('clicksChart').getContext('2d');
        const clicksChart = new Chart(clicksCtx, {
            type: 'line',
            data: clicksData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const earningsCtx = document.getElementById('earningsChart').getContext('2d');
        const earningsChart = new Chart(earningsCtx, {
            type: 'line',
            data: earningsData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>