<?php
// Admin Requests Management Page for Star-Clicks Clone
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

// Handle admin request approval/rejection
if (isset($_POST['update_request'])) {
    $request_id = intval($_POST['request_id'] ?? 0);
    $new_status = sanitizeInput($_POST['new_status'] ?? '');
    $review_notes = sanitizeInput($_POST['review_notes'] ?? '');
    
    if (in_array($new_status, ['approved', 'rejected'])) {
        try {
            $pdo->beginTransaction();
            
            // Update the admin request
            $stmt = $pdo->prepare("
                UPDATE admin_requests 
                SET status = ?, reviewed_at = NOW(), reviewed_by = ?, review_notes = ?
                WHERE id = ?
            ");
            $result = $stmt->execute([$new_status, $user['id'], $review_notes, $request_id]);
            
            if ($result) {
                // If approved, update the user's account to admin
                if ($new_status === 'approved') {
                    $stmt = $pdo->prepare("
                        UPDATE admin_requests 
                        SET reviewed_at = NOW(), reviewed_by = ?, review_notes = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$user['id'], $review_notes, $request_id]);
                    
                    // Get the user ID from the request
                    $stmt = $pdo->prepare("SELECT user_id FROM admin_requests WHERE id = ?");
                    $stmt->execute([$request_id]);
                    $request = $stmt->fetch();
                    
                    if ($request) {
                        // Update user to admin
                        $stmt = $pdo->prepare("UPDATE users SET user_type = 'admin' WHERE id = ?");
                        $stmt->execute([$request['user_id']]);
                        
                        $success_message = 'Admin request approved successfully. User account updated to admin.';
                        logActivity($user['id'], 'admin_request_approved', "Approved admin request ID $request_id for user ID {$request['user_id']}");
                    } else {
                        $error_message = 'Error: Request not found.';
                    }
                } else {
                    $success_message = 'Admin request status updated successfully.';
                    logActivity($user['id'], 'admin_request_updated', "Updated admin request ID $request_id status to $new_status");
                }
                
                $pdo->commit();
            } else {
                $error_message = 'Error updating admin request.';
                $pdo->rollback();
            }
        } catch (Exception $e) {
            $error_message = 'Error processing admin request: ' . $e->getMessage();
            $pdo->rollback();
        }
    } else {
        $error_message = 'Invalid status.';
    }
}

// Get all admin requests with pagination
$page = intval($_GET['page'] ?? 1);
$limit = 15;
$offset = ($page - 1) * $limit;

// Get total requests count
$stmt = $pdo->query("SELECT COUNT(*) as total FROM admin_requests");
$total_requests = $stmt->fetch();
$total_pages = ceil($total_requests['total'] / $limit);

// Get requests for current page with user info
$stmt = $pdo->prepare("
    SELECT ar.*, u.email as user_email, u.first_name, u.last_name, u.user_type as current_user_type,
           reviewed_user.email as reviewed_by_email
    FROM admin_requests ar
    JOIN users u ON ar.user_id = u.id
    LEFT JOIN users reviewed_user ON ar.reviewed_by = reviewed_user.id
    ORDER BY ar.requested_at DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$requests = $stmt->fetchAll();

// Get request stats
$stmt = $pdo->query("
    SELECT status, COUNT(*) as count 
    FROM admin_requests 
    GROUP BY status
");
$request_statuses = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Requests - Star-Clicks Admin</title>
    
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
                <h1 class="text-3xl font-bold text-gray-900">Admin Access Requests</h1>
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
            
            <!-- Request Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-blue-600"><?php echo $request_statuses['pending'] ?? 0; ?></div>
                    <div class="text-gray-600">Pending Requests</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-green-600"><?php echo $request_statuses['approved'] ?? 0; ?></div>
                    <div class="text-gray-600">Approved Requests</div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow text-center">
                    <div class="text-3xl font-bold text-red-600"><?php echo $request_statuses['rejected'] ?? 0; ?></div>
                    <div class="text-gray-600">Rejected Requests</div>
                </div>
            </div>
            
            <!-- Requests Table -->
            <div class="card">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">All Admin Requests</h2>
                    <div class="text-sm text-gray-500">
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $total_requests['total']); ?> of <?php echo $total_requests['total']; ?> requests
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Current Type</th>
                                <th>Reason</th>
                                <th>Experience</th>
                                <th>Status</th>
                                <th>Requested</th>
                                <th>Reviewed By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $request): ?>
                                <tr>
                                    <td><?php echo $request['id']; ?></td>
                                    <td>
                                        <div><?php echo escape($request['first_name'] . ' ' . $request['last_name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo escape($request['user_email']); ?></div>
                                    </td>
                                    <td><?php echo ucfirst(escape($request['current_user_type'])); ?></td>
                                    <td class="max-w-xs">
                                        <span class="truncate" title="<?php echo escape($request['reason']); ?>">
                                            <?php echo escape(substr($request['reason'], 0, 50)) . (strlen($request['reason']) > 50 ? '...' : ''); ?>
                                        </span>
                                    </td>
                                    <td class="max-w-xs">
                                        <span class="truncate" title="<?php echo escape($request['experience']); ?>">
                                            <?php echo escape(substr($request['experience'], 0, 50)) . (strlen($request['experience']) > 50 ? '...' : ''); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                            <?php 
                                            if ($request['status'] === 'approved') echo 'bg-green-100 text-green-800';
                                            elseif ($request['status'] === 'rejected') echo 'bg-red-100 text-red-800';
                                            else echo 'bg-yellow-100 text-yellow-800';
                                            ?>">
                                            <?php echo ucfirst(escape($request['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($request['requested_at'])); ?></td>
                                    <td>
                                        <?php if ($request['reviewed_by_email']): ?>
                                            <span class="text-sm"><?php echo escape($request['reviewed_by_email']); ?></span>
                                            <div class="text-xs text-gray-500"><?php echo date('M j, Y', strtotime($request['reviewed_at'])); ?></div>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($request['status'] === 'pending'): ?>
                                            <div class="flex flex-col space-y-2">
                                                <form method="POST" action="" class="inline"
                                                      onsubmit="return confirm('Are you sure you want to approve this admin request? The user will gain full admin privileges.');">
                                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                    <input type="hidden" name="new_status" value="approved">
                                                    <textarea name="review_notes" class="form-control text-xs mb-2" rows="2" placeholder="Review notes (optional)"></textarea>
                                                    <button type="submit" name="update_request" class="btn-success text-xs w-full">
                                                        <i class="fas fa-check mr-1"></i>Approve
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" action="" class="inline mt-1"
                                                      onsubmit="return confirm('Are you sure you want to reject this admin request?');">
                                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                    <input type="hidden" name="new_status" value="rejected">
                                                    <textarea name="review_notes" class="form-control text-xs mb-2" rows="2" placeholder="Review notes (optional)"></textarea>
                                                    <button type="submit" name="update_request" class="btn-danger text-xs w-full bg-red-600 hover:bg-red-700">
                                                        <i class="fas fa-times mr-1"></i>Reject
                                                    </button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-xs">Processed</span>
                                        <?php endif; ?>
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