<?php
// Admin Click Reports Page for Star-Clicks Clone
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

// Get all clicks with pagination
$page = intval($_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

// Get total clicks count
$stmt = $pdo->query("SELECT COUNT(*) as total FROM ad_clicks");
$total_clicks = $stmt->fetch();
$total_pages = ceil($total_clicks['total'] / $limit);

// Get clicks for current page with user and ad info
$stmt = $pdo->prepare("
    SELECT ac.*, u.email as publisher_email, a.title as ad_title, a.url as ad_url
    FROM ad_clicks ac
    JOIN users u ON ac.publisher_id = u.id
    JOIN advertisements a ON ac.ad_id = a.id
    ORDER BY ac.clicked_at DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$clicks = $stmt->fetchAll();

// Get click stats
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_clicks,
        SUM(amount_earned) as total_publisher_earnings,
        SUM(amount_paid) as total_advertiser_spent,
        COUNT(CASE WHEN is_valid = 1 THEN 1 END) as valid_clicks,
        COUNT(CASE WHEN is_valid = 0 THEN 1 END) as invalid_clicks
    FROM ad_clicks
");
$click_stats = $stmt->fetch();

// Get today's stats
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as today_clicks,
        SUM(amount_earned) as today_publisher_earnings,
        SUM(amount_paid) as today_advertiser_spent
    FROM ad_clicks
    WHERE DATE(clicked_at) = CURDATE()
");
$today_stats = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Click Reports - Star-Clicks Admin</title>
    
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
                <h1 class="text-3xl font-bold text-gray-900">Click Reports</h1>
            </div>
            
            <!-- Click Stats -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-blue-600"><?php echo $click_stats['total_clicks']; ?></div>
                    <div class="text-gray-600">Total Clicks</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-green-600"><?php echo formatCurrency($click_stats['total_publisher_earnings']); ?></div>
                    <div class="text-gray-600">Total Publisher Earnings</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-red-600"><?php echo formatCurrency($click_stats['total_advertiser_spent']); ?></div>
                    <div class="text-gray-600">Total Advertiser Spent</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-yellow-600"><?php echo $click_stats['valid_clicks']; ?></div>
                    <div class="text-gray-600">Valid Clicks</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-purple-600"><?php echo $today_stats['today_clicks']; ?></div>
                    <div class="text-gray-600">Today's Clicks</div>
                </div>
            </div>
            
            <!-- Clicks Table -->
            <div class="card">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">All Clicks</h2>
                    <div class="text-sm text-gray-500">
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $total_clicks['total']); ?> of <?php echo $total_clicks['total']; ?> clicks
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Publisher</th>
                                <th>Ad</th>
                                <th>Amount Earned</th>
                                <th>Amount Paid</th>
                                <th>Valid</th>
                                <th>IP Address</th>
                                <th>Clicked At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clicks as $click): ?>
                                <tr>
                                    <td><?php echo $click['id']; ?></td>
                                    <td><?php echo escape($click['publisher_email']); ?></td>
                                    <td>
                                        <div><?php echo escape($click['ad_title']); ?></div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs"><?php echo escape($click['ad_url']); ?></div>
                                    </td>
                                    <td><?php echo formatCurrency($click['amount_earned']); ?></td>
                                    <td><?php echo formatCurrency($click['amount_paid']); ?></td>
                                    <td>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            <?php echo $click['is_valid'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo $click['is_valid'] ? 'Valid' : 'Invalid'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo escape($click['ip_address']); ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($click['clicked_at'])); ?></td>
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