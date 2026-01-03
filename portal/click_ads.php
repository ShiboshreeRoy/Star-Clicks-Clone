<?php
// Click Ads Page for Star-Clicks Clone
session_start();
include_once '../includes/config.php';
include_once '../includes/functions.php';

// Check if user is logged in and is a publisher
if (!isLoggedIn() || !isPublisher()) {
    redirect('signin.php');
}

$user = getCurrentUser();
if (!$user) {
    redirect('signin.php');
}

$pdo = getDBConnection();

// Get available ads for the publisher
$stmt = $pdo->prepare("
    SELECT a.*, u.first_name as advertiser_name
    FROM advertisements a
    JOIN users u ON a.advertiser_id = u.id
    WHERE a.status = 'active' 
    AND a.start_date <= CURDATE() 
    AND a.end_date >= CURDATE()
    AND a.id NOT IN (
        SELECT ad_id FROM ad_clicks 
        WHERE publisher_id = ? 
        AND DATE(clicked_at) = CURDATE()
    )
    ORDER BY a.cpc DESC
    LIMIT 10
");
$stmt->execute([$user['id']]);
$available_ads = $stmt->fetchAll();

// Handle ad clicks
if (isset($_POST['click_ad'])) {
    $ad_id = intval($_POST['ad_id'] ?? 0);
    
    // Verify the ad exists and is active
    $stmt = $pdo->prepare("
        SELECT a.*, u.balance as advertiser_balance
        FROM advertisements a
        JOIN users u ON a.advertiser_id = u.id
        WHERE a.id = ? AND a.status = 'active'
        AND a.start_date <= CURDATE() AND a.end_date >= CURDATE()
    ");
    $stmt->execute([$ad_id]);
    $ad = $stmt->fetch();
    
    if ($ad && $ad['advertiser_balance'] >= $ad['cpc']) {
        // Check if publisher has already clicked this ad today
        $stmt = $pdo->prepare("
            SELECT id FROM ad_clicks 
            WHERE ad_id = ? AND publisher_id = ? AND DATE(clicked_at) = CURDATE()
        ");
        $stmt->execute([$ad_id, $user['id']]);
        
        if ($stmt->rowCount() === 0) {
            // Calculate earnings (50% of CPC goes to publisher)
            $publisher_earnings = $ad['cpc'] * 0.50;
            $advertiser_cost = $ad['cpc'];
            
            try {
                $pdo->beginTransaction();
                
                // Insert the click record
                $stmt = $pdo->prepare("
                    INSERT INTO ad_clicks (ad_id, publisher_id, ip_address, user_agent, amount_earned, amount_paid)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $ad_id, 
                    $user['id'], 
                    $_SERVER['REMOTE_ADDR'],
                    $_SERVER['HTTP_USER_AGENT'] ?? '',
                    $publisher_earnings,
                    $advertiser_cost
                ]);
                
                // Update publisher balance
                $new_publisher_balance = $user['balance'] + $publisher_earnings;
                $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
                $stmt->execute([$new_publisher_balance, $user['id']]);
                
                // Update advertiser balance
                $new_advertiser_balance = $ad['advertiser_balance'] - $advertiser_cost;
                $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
                $stmt->execute([$new_advertiser_balance, $ad['advertiser_id']]);
                
                // Update advertisement stats
                $stmt = $pdo->prepare("
                    UPDATE advertisements 
                    SET clicks_count = clicks_count + 1, 
                        spent = spent + ? 
                    WHERE id = ?
                ");
                $stmt->execute([$advertiser_cost, $ad_id]);
                
                $pdo->commit();
                
                // Update user object
                $user['balance'] = $new_publisher_balance;
                
                logActivity($user['id'], 'ad_clicked', "Clicked ad ID $ad_id");
                
                // Redirect to prevent resubmission
                redirect('click_ads.php?success=1');
            } catch (Exception $e) {
                $pdo->rollback();
                $error_message = "Error processing click. Please try again.";
            }
        } else {
            $error_message = "You have already clicked this ad today.";
        }
    } else {
        $error_message = "This ad is no longer available.";
    }
}

$success_message = isset($_GET['success']) ? 'Ad clicked successfully! Earnings added to your balance.' : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Click Ads - Star-Clicks Clone</title>
    
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

    <div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Click Ads & Earn</h1>
                <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg">
                    <span class="font-semibold">Current Balance:</span> <?php echo formatCurrency($user['balance']); ?>
                </div>
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
            
            <?php if (empty($available_ads)): ?>
                <div class="card text-center py-12">
                    <i class="fas fa-ad text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No ads available at the moment</h3>
                    <p class="text-gray-500 mb-4">Check back later for new advertisements to click.</p>
                    <a href="dashboard.php" class="btn-primary inline-block">Return to Dashboard</a>
                </div>
            <?php else: ?>
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Available Ads to Click</h2>
                    <p class="text-gray-600 mb-4">Click on the ads below to earn money. You can click each ad once per day.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($available_ads as $ad): ?>
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                <div class="p-5">
                                    <div class="flex justify-between items-start mb-3">
                                        <h3 class="text-lg font-semibold text-gray-900"><?php echo escape($ad['title']); ?></h3>
                                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                            <?php echo formatCurrency($ad['cpc'] * 0.50); ?> per click
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-600 text-sm mb-4">
                                        <?php echo !empty($ad['description']) ? escape(substr($ad['description'], 0, 100)) . '...' : 'No description provided.'; ?>
                                    </p>
                                    
                                    <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                                        <span>By: <?php echo escape($ad['advertiser_name']); ?></span>
                                        <span>CPC: <?php echo formatCurrency($ad['cpc']); ?></span>
                                    </div>
                                    
                                    <form method="POST" action="" class="mt-4">
                                        <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                                        <button type="submit" name="click_ad" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-300">
                                            <i class="fas fa-mouse-pointer mr-2"></i>Click & Earn <?php echo formatCurrency($ad['cpc'] * 0.50); ?>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="card">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">How It Works</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center p-4">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-search text-blue-600 text-2xl"></i>
                            </div>
                            <h3 class="font-semibold text-lg mb-2">Browse Ads</h3>
                            <p class="text-gray-600">View available advertisements that you can click to earn money.</p>
                        </div>
                        
                        <div class="text-center p-4">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-mouse-pointer text-green-600 text-2xl"></i>
                            </div>
                            <h3 class="font-semibold text-lg mb-2">Click & Earn</h3>
                            <p class="text-gray-600">Click on the ads to earn a percentage of the cost-per-click.</p>
                        </div>
                        
                        <div class="text-center p-4">
                            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-wallet text-purple-600 text-2xl"></i>
                            </div>
                            <h3 class="font-semibold text-lg mb-2">Withdraw Earnings</h3>
                            <p class="text-gray-600">Withdraw your earnings when you reach the minimum payout threshold.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>