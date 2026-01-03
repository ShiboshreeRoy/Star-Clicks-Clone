<?php
// Referrals Page for Star-Clicks Clone
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

// Get referral information
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total_referrals,
           SUM(r.commission_earned) as total_earnings
    FROM referrals r
    WHERE r.referrer_id = ?
");
$stmt->execute([$user['id']]);
$referral_stats = $stmt->fetch();

// Get referral history
$stmt = $pdo->prepare("
    SELECT r.*, u.email as referred_email, u.created_at as referred_date
    FROM referrals r
    JOIN users u ON r.referred_id = u.id
    WHERE r.referrer_id = ?
    ORDER BY r.referred_at DESC
    LIMIT 20
");
$stmt->execute([$user['id']]);
$referrals = $stmt->fetchAll();

// Get site settings for referral commission
$stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = 'referral_commission'");
$stmt->execute();
$referral_setting = $stmt->fetch();
$referral_commission = $referral_setting ? floatval($referral_setting['setting_value']) : 0.10; // Default 10%
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referrals - Star-Clicks Clone</title>
    
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
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Referral Program</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-blue-600"><?php echo $referral_stats['total_referrals']; ?></div>
                    <div class="text-gray-600">Total Referrals</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-green-600"><?php echo formatCurrency($referral_stats['total_earnings']); ?></div>
                    <div class="text-gray-600">Total Earnings</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-purple-600"><?php echo ($referral_commission * 100); ?>%</div>
                    <div class="text-gray-600">Commission Rate</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-yellow-600"><?php echo formatCurrency($user['balance']); ?></div>
                    <div class="text-gray-600">Current Balance</div>
                </div>
            </div>
            
            <div class="card mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Your Referral Link</h2>
                
                <div class="flex flex-col sm:flex-row items-center p-4 bg-gray-50 rounded-lg">
                    <div class="flex-grow mb-3 sm:mb-0 sm:mr-3">
                        <input type="text" id="referralLink" class="form-control" 
                               value="<?php echo SITE_URL; ?>/portal/signup.php?ref=<?php echo $user['id']; ?>" 
                               readonly>
                    </div>
                    <button id="copyLink" class="btn-primary w-full sm:w-auto">
                        <i class="fas fa-copy mr-2"></i>Copy Link
                    </button>
                </div>
                
                <div class="mt-4 bg-blue-50 p-4 rounded-lg">
                    <h3 class="font-bold text-blue-800 mb-2">How to Use Your Referral Link</h3>
                    <ul class="list-disc pl-5 text-blue-700 space-y-1">
                        <li>Share your referral link with friends and colleagues</li>
                        <li>When someone signs up using your link, they become your referral</li>
                        <li>You earn a commission for each valid click your referrals make</li>
                        <li>Commissions are added to your account balance automatically</li>
                    </ul>
                </div>
            </div>
            
            <div class="card">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Your Referrals</h2>
                
                <?php if (empty($referrals)): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-users text-4xl mb-4"></i>
                        <p>You don't have any referrals yet.</p>
                        <p class="mt-2">Start sharing your referral link to earn commissions!</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Referred User</th>
                                    <th>Joined Date</th>
                                    <th>Commission Earned</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($referrals as $referral): ?>
                                    <tr>
                                        <td><?php echo escape($referral['referred_email']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($referral['referred_date'])); ?></td>
                                        <td><?php echo formatCurrency($referral['commission_earned']); ?></td>
                                        <td>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                <?php echo $referral['is_paid'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                                <?php echo $referral['is_paid'] ? 'Paid' : 'Pending'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="card mt-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Referral Program Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-800 mb-3">How It Works</h3>
                        <ul class="list-disc pl-5 space-y-2 text-gray-600">
                            <li>Share your unique referral link with others</li>
                            <li>When someone signs up using your link, they become your referral</li>
                            <li>You earn a commission for each valid click your referrals make</li>
                            <li>Commissions are automatically added to your account balance</li>
                            <li>Your referrals also become publishers on our platform</li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-lg text-gray-800 mb-3">Earnings</h3>
                        <ul class="list-disc pl-5 space-y-2 text-gray-600">
                            <li>You earn <?php echo ($referral_commission * 100); ?>% commission on each click made by your referrals</li>
                            <li>There's no limit to how much you can earn through referrals</li>
                            <li>Commissions are paid out automatically with your regular earnings</li>
                            <li>Referral commissions are added to your account balance immediately</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Copy referral link to clipboard
        document.getElementById('copyLink').addEventListener('click', function() {
            const referralLink = document.getElementById('referralLink');
            referralLink.select();
            referralLink.setSelectionRange(0, 99999); // For mobile devices
            
            document.execCommand('copy');
            
            // Show feedback
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
            
            setTimeout(() => {
                this.innerHTML = originalText;
            }, 2000);
        });
    </script>
</body>
</html>