<?php
// Admin Withdrawals Management Page for Star-Clicks Clone
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

// Handle withdrawal status changes
if (isset($_POST['update_status'])) {
    $withdrawal_id = intval($_POST['withdrawal_id'] ?? 0);
    $new_status = sanitizeInput($_POST['new_status'] ?? '');
    
    if (in_array($new_status, ['pending', 'processing', 'completed', 'cancelled', 'rejected'])) {
        // Get withdrawal details
        $stmt = $pdo->prepare("SELECT * FROM withdrawals WHERE id = ?");
        $stmt->execute([$withdrawal_id]);
        $withdrawal = $stmt->fetch();
        
        if ($withdrawal) {
            $old_status = $withdrawal['status'];
            
            // Update status
            $stmt = $pdo->prepare("UPDATE withdrawals SET status = ?, updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([$new_status, $withdrawal_id]);
            
            if ($result) {
                $success_message = 'Withdrawal status updated successfully.';
                
                // If status changed from pending to completed, update user balance
                if ($old_status === 'pending' && $new_status === 'completed') {
                    // In a real system, we would process the actual payment here
                    // For now, we'll just log the activity
                } elseif ($old_status === 'pending' && $new_status === 'rejected') {
                    // If rejected, return funds to user balance
                    $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                    $stmt->execute([$withdrawal['amount'], $withdrawal['user_id']]);
                }
                
                logActivity($user['id'], 'admin_withdrawal_status_changed', "Changed withdrawal ID $withdrawal_id status from $old_status to $new_status");
            } else {
                $error_message = 'Error updating withdrawal status.';
            }
        }
    }
}

// Get all withdrawals with pagination
$page = intval($_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

// Get total withdrawals count
$stmt = $pdo->query("SELECT COUNT(*) as total FROM withdrawals");
$total_withdrawals = $stmt->fetch();
$total_pages = ceil($total_withdrawals['total'] / $limit);

// Get withdrawals for current page with user info
$stmt = $pdo->prepare("
    SELECT w.*, u.email as user_email, u.first_name, u.last_name
    FROM withdrawals w
    JOIN users u ON w.user_id = u.id
    ORDER BY w.requested_at DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$withdrawals = $stmt->fetchAll();

// Get withdrawal stats
$stmt = $pdo->query("
    SELECT status, COUNT(*) as count 
    FROM withdrawals 
    GROUP BY status
");
$withdrawal_statuses = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Get total amount requested
$stmt = $pdo->query("SELECT SUM(amount) as total FROM withdrawals");
$total_requested = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Withdrawals - Star-Clicks Admin</title>
    
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
                <h1 class="text-3xl font-bold text-gray-900">Manage Withdrawals</h1>
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
            
            <!-- Withdrawal Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-blue-600"><?php echo $withdrawal_statuses['pending'] ?? 0; ?></div>
                    <div class="text-gray-600">Pending</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-yellow-600"><?php echo $withdrawal_statuses['processing'] ?? 0; ?></div>
                    <div class="text-gray-600">Processing</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-green-600"><?php echo $withdrawal_statuses['completed'] ?? 0; ?></div>
                    <div class="text-gray-600">Completed</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-purple-600"><?php echo formatCurrency($total_requested['total'] ?? 0); ?></div>
                    <div class="text-gray-600">Total Requested</div>
                </div>
            </div>
            
            <!-- Withdrawals Table -->
            <div class="card">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">All Withdrawal Requests</h2>
                    <div class="text-sm text-gray-500">
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $total_withdrawals['total']); ?> of <?php echo $total_withdrawals['total']; ?> requests
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Details</th>
                                <th>Status</th>
                                <th>Requested</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($withdrawals as $withdrawal): ?>
                                <tr>
                                    <td><?php echo $withdrawal['id']; ?></td>
                                    <td>
                                        <div><?php echo escape($withdrawal['first_name'] . ' ' . $withdrawal['last_name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo escape($withdrawal['user_email']); ?></div>
                                    </td>
                                    <td><?php echo formatCurrency($withdrawal['amount']); ?></td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', escape($withdrawal['method']))); ?></td>
                                    <td class="max-w-xs">
                                        <?php if (!empty($withdrawal['details'])): ?>
                                            <span class="truncate" title="<?php echo escape($withdrawal['details']); ?>">
                                                <?php echo escape(substr($withdrawal['details'], 0, 30)) . (strlen($withdrawal['details']) > 30 ? '...' : ''); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            <?php 
                                            if ($withdrawal['status'] === 'completed') echo 'bg-green-100 text-green-800';
                                            elseif ($withdrawal['status'] === 'processing') echo 'bg-yellow-100 text-yellow-800';
                                            elseif ($withdrawal['status'] === 'pending') echo 'bg-blue-100 text-blue-800';
                                            elseif ($withdrawal['status'] === 'rejected') echo 'bg-red-100 text-red-800';
                                            else echo 'bg-gray-100 text-gray-800';
                                            ?>">
                                            <?php echo ucfirst(escape($withdrawal['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($withdrawal['requested_at'])); ?></td>
                                    <td>
                                        <form method="POST" action="" class="inline"
                                              onsubmit="return confirm('Are you sure you want to change the status of this withdrawal?');">
                                            <input type="hidden" name="withdrawal_id" value="<?php echo $withdrawal['id']; ?>">
                                            <select name="new_status" class="form-control text-xs p-1" onchange="this.form.submit()">
                                                <option value="">Change Status</option>
                                                <option value="pending" <?php echo $withdrawal['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="processing" <?php echo $withdrawal['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="completed" <?php echo $withdrawal['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                <option value="cancelled" <?php echo $withdrawal['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                <option value="rejected" <?php echo $withdrawal['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                            </select>
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