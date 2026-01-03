<?php
// Manage Ads Page for Star-Clicks Clone
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

$pdo = getDBConnection();

// Handle ad status changes
if (isset($_POST['change_status'])) {
    $ad_id = intval($_POST['ad_id'] ?? 0);
    $new_status = sanitizeInput($_POST['new_status'] ?? '');
    
    if ($new_status === 'active' || $new_status === 'paused') {
        $stmt = $pdo->prepare("UPDATE advertisements SET status = ? WHERE id = ? AND advertiser_id = ?");
        $result = $stmt->execute([$new_status, $ad_id, $user['id']]);
        
        if ($result) {
            logActivity($user['id'], 'ad_status_changed', "Changed ad ID $ad_id status to $new_status");
        }
    }
}

// Handle ad deletion
if (isset($_POST['delete_ad'])) {
    $ad_id = intval($_POST['ad_id'] ?? 0);
    
    $stmt = $pdo->prepare("DELETE FROM advertisements WHERE id = ? AND advertiser_id = ?");
    $result = $stmt->execute([$ad_id, $user['id']]);
    
    if ($result) {
        logActivity($user['id'], 'ad_deleted', "Deleted ad ID $ad_id");
    }
}

// Get all ads for this advertiser
$stmt = $pdo->prepare("
    SELECT a.*, 
           (SELECT COUNT(*) FROM ad_clicks WHERE ad_id = a.id) as total_clicks,
           (SELECT SUM(amount_paid) FROM ad_clicks WHERE ad_id = a.id) as total_spent
    FROM advertisements a 
    WHERE advertiser_id = ? 
    ORDER BY created_at DESC
");
$stmt->execute([$user['id']]);
$ads = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Ads - Star-Clicks Clone</title>
    
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

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Manage Advertisements</h1>
                <a href="create_ad.php" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Create New Ad
                </a>
            </div>
            
            <?php if (empty($ads)): ?>
                <div class="card text-center py-12">
                    <i class="fas fa-ad text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No advertisements yet</h3>
                    <p class="text-gray-500 mb-4">You haven't created any advertisements yet.</p>
                    <a href="create_ad.php" class="btn-primary inline-block">Create Your First Ad</a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>CPC</th>
                                <th>Budget</th>
                                <th>Spent</th>
                                <th>Clicks</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ads as $ad): ?>
                                <tr>
                                    <td>
                                        <div class="font-medium"><?php echo escape($ad['title']); ?></div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs"><?php echo escape($ad['url']); ?></div>
                                    </td>
                                    <td>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            <?php 
                                            if ($ad['status'] === 'active') echo 'bg-green-100 text-green-800';
                                            elseif ($ad['status'] === 'paused') echo 'bg-yellow-100 text-yellow-800';
                                            elseif ($ad['status'] === 'completed') echo 'bg-gray-100 text-gray-800';
                                            else echo 'bg-red-100 text-red-800';
                                            ?>">
                                            <?php echo ucfirst(escape($ad['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatCurrency($ad['cpc']); ?></td>
                                    <td><?php echo formatCurrency($ad['daily_budget']); ?></td>
                                    <td><?php echo formatCurrency($ad['total_spent'] ?? 0); ?></td>
                                    <td><?php echo $ad['total_clicks'] ?? 0; ?></td>
                                    <td><?php echo date('M j, Y', strtotime($ad['start_date'])); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($ad['end_date'])); ?></td>
                                    <td class="flex space-x-2">
                                        <form method="POST" action="" class="inline" 
                                              onsubmit="return confirm('Are you sure you want to change the status of this ad?');">
                                            <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                                            <?php if ($ad['status'] === 'active'): ?>
                                                <input type="hidden" name="new_status" value="paused">
                                                <button type="submit" name="change_status" class="btn-warning text-sm">
                                                    <i class="fas fa-pause mr-1"></i>Pause
                                                </button>
                                            <?php else: ?>
                                                <input type="hidden" name="new_status" value="active">
                                                <button type="submit" name="change_status" class="btn-primary text-sm">
                                                    <i class="fas fa-play mr-1"></i>Activate
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                        
                                        <form method="POST" action="" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this ad? This action cannot be undone.');">
                                            <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                                            <button type="submit" name="delete_ad" class="btn-danger text-sm bg-red-600 hover:bg-red-700">
                                                <i class="fas fa-trash mr-1"></i>Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Stats Summary -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <div class="stat-card bg-blue-50 border border-blue-100">
                        <div class="stat-title">Total Advertisements</div>
                        <div class="stat-value text-blue-600"><?php echo count($ads); ?></div>
                    </div>
                    
                    <div class="stat-card bg-green-50 border border-green-100">
                        <div class="stat-title">Total Clicks</div>
                        <div class="stat-value text-green-600">
                            <?php 
                            $total_clicks = array_sum(array_column($ads, 'total_clicks'));
                            echo $total_clicks;
                            ?>
                        </div>
                    </div>
                    
                    <div class="stat-card bg-red-50 border border-red-100">
                        <div class="stat-title">Total Spent</div>
                        <div class="stat-value text-red-600">
                            <?php 
                            $total_spent = array_sum(array_column($ads, 'total_spent'));
                            echo formatCurrency($total_spent);
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>