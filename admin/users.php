<?php
// Admin Users Management Page for Star-Clicks Clone
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

// Handle user status changes
if (isset($_POST['change_status'])) {
    $user_id = intval($_POST['user_id'] ?? 0);
    $new_status = sanitizeInput($_POST['new_status'] ?? '');
    
    if (in_array($new_status, ['active', 'suspended', 'pending'])) {
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $result = $stmt->execute([$new_status, $user_id]);
        
        if ($result) {
            $success_message = 'User status updated successfully.';
            logActivity($user['id'], 'admin_user_status_changed', "Changed user ID $user_id status to $new_status");
        } else {
            $error_message = 'Error updating user status.';
        }
    }
}

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id'] ?? 0);
    
    // Prevent admin from deleting themselves
    if ($user_id == $user['id']) {
        $error_message = 'You cannot delete your own account.';
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND user_type != 'admin'");
        $result = $stmt->execute([$user_id]);
        
        if ($result) {
            $success_message = 'User deleted successfully.';
            logActivity($user['id'], 'admin_user_deleted', "Deleted user ID $user_id");
        } else {
            $error_message = 'Error deleting user.';
        }
    }
}

// Get all users with pagination
$page = intval($_GET['page'] ?? 1);
$limit = 20;
$offset = ($page - 1) * $limit;

// Get total users count
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$total_users = $stmt->fetch();
$total_pages = ceil($total_users['total'] / $limit);

// Get users for current page
$stmt = $pdo->prepare("
    SELECT * FROM users 
    ORDER BY created_at DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();

// Get user counts by type
$stmt = $pdo->query("
    SELECT user_type, COUNT(*) as count 
    FROM users 
    GROUP BY user_type
");
$user_types = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Star-Clicks Admin</title>
    
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
                <h1 class="text-3xl font-bold text-gray-900">Manage Users</h1>
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
            
            <!-- User Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-blue-600"><?php echo $user_types['publisher'] ?? 0; ?></div>
                    <div class="text-gray-600">Publishers</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-green-600"><?php echo $user_types['advertiser'] ?? 0; ?></div>
                    <div class="text-gray-600">Advertisers</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-purple-600"><?php echo $user_types['admin'] ?? 0; ?></div>
                    <div class="text-gray-600">Admins</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-yellow-600"><?php echo $total_users['total']; ?></div>
                    <div class="text-gray-600">Total Users</div>
                </div>
            </div>
            
            <!-- Users Table -->
            <div class="card">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">All Users</h2>
                    <div class="text-sm text-gray-500">
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $total_users['total']); ?> of <?php echo $total_users['total']; ?> users
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Balance</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $usr): ?>
                                <tr>
                                    <td><?php echo $usr['id']; ?></td>
                                    <td><?php echo escape($usr['email']); ?></td>
                                    <td><?php echo escape($usr['first_name'] . ' ' . $usr['last_name']); ?></td>
                                    <td>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            <?php 
                                            if ($usr['user_type'] === 'publisher') echo 'bg-blue-100 text-blue-800';
                                            elseif ($usr['user_type'] === 'advertiser') echo 'bg-green-100 text-green-800';
                                            else echo 'bg-red-100 text-red-800';
                                            ?>">
                                            <?php echo ucfirst(escape($usr['user_type'])); ?>
                                        </span>
                                    </td>
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
                                    <td><?php echo formatCurrency($usr['balance']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($usr['created_at'])); ?></td>
                                    <td class="flex space-x-2">
                                        <form method="POST" action="" class="inline"
                                              onsubmit="return confirm('Are you sure you want to change the status of this user?');">
                                            <input type="hidden" name="user_id" value="<?php echo $usr['id']; ?>">
                                            <select name="new_status" class="form-control text-xs p-1" onchange="this.form.submit()">
                                                <option value="">Change Status</option>
                                                <option value="active" <?php echo $usr['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                                <option value="pending" <?php echo $usr['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="suspended" <?php echo $usr['status'] === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                                            </select>
                                        </form>
                                        
                                        <form method="POST" action="" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                            <input type="hidden" name="user_id" value="<?php echo $usr['id']; ?>">
                                            <button type="submit" name="delete_user" class="btn-danger text-xs bg-red-600 hover:bg-red-700">
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