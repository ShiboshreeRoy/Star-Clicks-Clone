<?php
// Withdrawals Page for Star-Clicks Clone
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

$error_message = '';
$success_message = '';

// Get minimum payout from settings
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = 'minimum_payout'");
$stmt->execute();
$min_payout_result = $stmt->fetch();
$minimum_payout = $min_payout_result ? floatval($min_payout_result['setting_value']) : 5.00;

// Handle withdrawal request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_withdrawal'])) {
    $amount = floatval($_POST['amount'] ?? 0);
    $method = sanitizeInput($_POST['method'] ?? '');
    $details = sanitizeInput($_POST['details'] ?? '');
    
    // Validation
    if ($amount <= 0) {
        $error_message = 'Please enter a valid amount.';
    } elseif ($amount < $minimum_payout) {
        $error_message = 'Minimum withdrawal amount is ' . formatCurrency($minimum_payout) . '.';
    } elseif ($amount > $user['balance']) {
        $error_message = 'Insufficient balance. Your current balance is ' . formatCurrency($user['balance']) . '.';
    } elseif (!in_array($method, ['paypal', 'bank_transfer', 'bitcoin'])) {
        $error_message = 'Invalid withdrawal method.';
    } elseif (empty($details) && $method === 'paypal') {
        $error_message = 'PayPal email is required for PayPal withdrawals.';
    } else {
        try {
            $pdo->beginTransaction();
            
            // Insert withdrawal request
            $stmt = $pdo->prepare("
                INSERT INTO withdrawals (user_id, amount, method, details, status) 
                VALUES (?, ?, ?, ?, 'pending')
            ");
            $result = $stmt->execute([$user['id'], $amount, $method, $details]);
            
            if ($result) {
                // Update user balance (hold the amount until withdrawal is processed)
                $new_balance = $user['balance'] - $amount;
                $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
                $stmt->execute([$new_balance, $user['id']]);
                
                $pdo->commit();
                
                $success_message = 'Withdrawal request submitted successfully! It will be processed shortly.';
                logActivity($user['id'], 'withdrawal_requested', "Requested withdrawal of " . formatCurrency($amount) . " via $method");
                
                // Refresh user data
                $user = getCurrentUser();
            } else {
                $error_message = 'Error submitting withdrawal request. Please try again.';
            }
        } catch (Exception $e) {
            $pdo->rollback();
            $error_message = 'Error processing withdrawal request. Please try again.';
        }
    }
}

// Get withdrawal history
$stmt = $pdo->prepare("
    SELECT * FROM withdrawals 
    WHERE user_id = ? 
    ORDER BY requested_at DESC 
    LIMIT 10
");
$stmt->execute([$user['id']]);
$withdrawal_history = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw Funds - Star-Clicks Clone</title>
    
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

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Withdraw Funds</h1>
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
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900"><?php echo formatCurrency($user['balance']); ?></div>
                        <div class="text-gray-600">Available Balance</div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600"><?php echo formatCurrency($user['total_earned']); ?></div>
                        <div class="text-gray-600">Total Earned</div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-red-600"><?php echo formatCurrency($minimum_payout); ?></div>
                        <div class="text-gray-600">Minimum Withdrawal</div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Request Withdrawal</h2>
                
                <form method="POST" action="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="amount" class="form-label">Amount to Withdraw</label>
                            <input type="number" id="amount" name="amount" class="form-control" 
                                   step="0.01" min="<?php echo $minimum_payout; ?>" max="<?php echo $user['balance']; ?>" 
                                   value="<?php echo $user['balance'] >= $minimum_payout ? min($user['balance'], 100) : $minimum_payout; ?>" required>
                            <p class="text-sm text-gray-500 mt-1">Minimum: <?php echo formatCurrency($minimum_payout); ?> | Maximum: <?php echo formatCurrency($user['balance']); ?></p>
                        </div>
                        
                        <div class="form-group">
                            <label for="method" class="form-label">Withdrawal Method</label>
                            <select id="method" name="method" class="form-control" required>
                                <option value="">Select Method</option>
                                <option value="paypal">PayPal</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="bitcoin">Bitcoin</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group" id="details-group" style="display: none;">
                        <label for="details" id="details-label" class="form-label"></label>
                        <input type="text" id="details" name="details" class="form-control">
                        <p class="text-sm text-gray-500 mt-1" id="details-help"></p>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" name="request_withdrawal" class="btn-primary">
                            <i class="fas fa-money-bill-wave mr-2"></i>Request Withdrawal
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="card">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Withdrawal History</h2>
                
                <?php if (empty($withdrawal_history)): ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-history text-4xl mb-4"></i>
                        <p>No withdrawal history yet.</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($withdrawal_history as $withdrawal): ?>
                                    <tr>
                                        <td><?php echo date('M j, Y g:i A', strtotime($withdrawal['requested_at'])); ?></td>
                                        <td><?php echo formatCurrency($withdrawal['amount']); ?></td>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $withdrawal['method'])); ?></td>
                                        <td>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                <?php 
                                                if ($withdrawal['status'] === 'completed') echo 'bg-green-100 text-green-800';
                                                elseif ($withdrawal['status'] === 'pending') echo 'bg-yellow-100 text-yellow-800';
                                                elseif ($withdrawal['status'] === 'processing') echo 'bg-blue-100 text-blue-800';
                                                elseif ($withdrawal['status'] === 'rejected') echo 'bg-red-100 text-red-800';
                                                else echo 'bg-gray-100 text-gray-800';
                                                ?>">
                                                <?php echo ucfirst(escape($withdrawal['status'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Show/hide details field based on selected method
        document.getElementById('method').addEventListener('change', function() {
            const method = this.value;
            const detailsGroup = document.getElementById('details-group');
            const detailsLabel = document.getElementById('details-label');
            const detailsHelp = document.getElementById('details-help');
            
            if (method === 'paypal') {
                detailsGroup.style.display = 'block';
                detailsLabel.textContent = 'PayPal Email';
                detailsHelp.textContent = 'Enter the PayPal email address where you want to receive funds';
                document.getElementById('details').placeholder = 'your-paypal-email@example.com';
                document.getElementById('details').required = true;
            } else if (method === 'bank_transfer') {
                detailsGroup.style.display = 'block';
                detailsLabel.textContent = 'Bank Details';
                detailsHelp.textContent = 'Enter your bank account details (account number, bank name, etc.)';
                document.getElementById('details').placeholder = 'Account number, Bank name, etc.';
                document.getElementById('details').required = true;
            } else if (method === 'bitcoin') {
                detailsGroup.style.display = 'block';
                detailsLabel.textContent = 'Bitcoin Address';
                detailsHelp.textContent = 'Enter your Bitcoin wallet address';
                document.getElementById('details').placeholder = '1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa';
                document.getElementById('details').required = true;
            } else {
                detailsGroup.style.display = 'none';
                document.getElementById('details').required = false;
            }
        });
        
        // Update max value when balance changes
        const balance = <?php echo $user['balance']; ?>;
        const amountInput = document.getElementById('amount');
        
        amountInput.addEventListener('input', function() {
            if (parseFloat(this.value) > balance) {
                this.value = balance;
            }
        });
    </script>
</body>
</html>