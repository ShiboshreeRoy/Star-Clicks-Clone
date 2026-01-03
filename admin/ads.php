<?php
// Admin Ads Management Page for Star-Clicks Clone
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
$error_message = '';
$success_message = '';

// Handle ad status changes
if (isset($_POST['change_status'])) {
    $ad_id = intval($_POST['ad_id'] ?? 0);
    $new_status = sanitizeInput($_POST['new_status'] ?? '');
    
    if (in_array($new_status, ['active', 'paused', 'completed', 'pending'])) {
        $stmt = $pdo->prepare("UPDATE advertisements SET status = ? WHERE id = ?");
        $result = $stmt->execute([$new_status, $ad_id]);
        
        if ($result) {
            $success_message = 'Ad status updated successfully.';
            logActivity($user['id'], 'admin_ad_status_changed', "Changed ad ID $ad_id status to $new_status");
        } else {
            $error_message = 'Error updating ad status.';
        }
    }
}

// Handle ad deletion
if (isset($_POST['delete_ad'])) {
    $ad_id = intval($_POST['ad_id'] ?? 0);
    
    $stmt = $pdo->prepare("DELETE FROM advertisements WHERE id = ?");
    $result = $stmt->execute([$ad_id]);
    
    if ($result) {
        $success_message = 'Ad deleted successfully.';
        logActivity($user['id'], 'admin_ad_deleted', "Deleted ad ID $ad_id");
    } else {
        $error_message = 'Error deleting ad.';
    }
}

// Get all ads with pagination
$page = intval($_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

// Get total ads count
$stmt = $pdo->query("SELECT COUNT(*) as total FROM advertisements");
$total_ads = $stmt->fetch();
$total_pages = ceil($total_ads['total'] / $limit);

// Get ads for current page
$stmt = $pdo->prepare("
    SELECT a.*, u.email as advertiser_email
    FROM advertisements a
    JOIN users u ON a.advertiser_id = u.id
    ORDER BY a.created_at DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$ads = $stmt->fetchAll();

// Get ad stats
$stmt = $pdo->query("
    SELECT status, COUNT(*) as count 
    FROM advertisements 
    GROUP BY status
");
$ad_statuses = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Ads - Star-Clicks Admin</title>
    
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
                    <a href="../portal/profile.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Profile</a>
                    <a href="../portal/logout.php" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Manage Advertisements</h1>
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
            
            <!-- Ad Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-blue-600"><?php echo $ad_statuses['active'] ?? 0; ?></div>
                    <div class="text-gray-600">Active Ads</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-yellow-600"><?php echo $ad_statuses['paused'] ?? 0; ?></div>
                    <div class="text-gray-600">Paused Ads</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-green-600"><?php echo $ad_statuses['completed'] ?? 0; ?></div>
                    <div class="text-gray-600">Completed Ads</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-purple-600"><?php echo $total_ads['total']; ?></div>
                    <div class="text-gray-600">Total Ads</div>
                </div>
            </div>
            
            <!-- Ads Table -->
            <div class="card">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">All Advertisements</h2>
                    <div class="text-sm text-gray-500">
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $total_ads['total']); ?> of <?php echo $total_ads['total']; ?> ads
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Advertiser</th>
                                <th>Status</th>
                                <th>CPC</th>
                                <th>Budget</th>
                                <th>Spent</th>
                                <th>Clicks</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ads as $ad): ?>
                                <tr>
                                    <td><?php echo $ad['id']; ?></td>
                                    <td>
                                        <div class="font-medium"><?php echo escape($ad['title']); ?></div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs"><?php echo escape($ad['url']); ?></div>
                                    </td>
                                    <td><?php echo escape($ad['advertiser_email']); ?></td>
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
                                    <td><?php echo formatCurrency($ad['spent']); ?></td>
                                    <td><?php echo $ad['clicks_count']; ?></td>
                                    <td><?php echo date('M j, Y', strtotime($ad['created_at'])); ?></td>
                                    <td class="flex space-x-2">
                                        <form method="POST" action="" class="inline"
                                              onsubmit="return confirm('Are you sure you want to change the status of this ad?');">
                                            <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                                            <select name="new_status" class="form-control text-xs p-1" onchange="this.form.submit()">
                                                <option value="">Change Status</option>
                                                <option value="active" <?php echo $ad['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                                <option value="paused" <?php echo $ad['status'] === 'paused' ? 'selected' : ''; ?>>Paused</option>
                                                <option value="completed" <?php echo $ad['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                <option value="pending" <?php echo $ad['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            </select>
                                        </form>
                                        
                                        <form method="POST" action="" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this ad? This action cannot be undone.');">
                                            <input type="hidden" name="ad_id" value="<?php echo $ad['id']; ?>">
                                            <button type="submit" name="delete_ad" class="btn-danger text-xs bg-red-600 hover:bg-red-700">
                                                <i class="fas fa-trash mr-1"></i>Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="flex justify-center mt-6">
                        <nav class="flex items-center space-x-2">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-2 rounded-md bg-white border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Previous
                                </a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <a href="?page=<?php echo $i; ?>" class="px-3 py-2 rounded-md <?php echo $i == $page ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-2 rounded-md bg-white border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Next
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>