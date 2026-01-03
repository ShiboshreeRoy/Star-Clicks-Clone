<?php
// Admin Settings Page for Star-Clicks Clone
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

// Get all site settings
$stmt = $pdo->query("SELECT * FROM site_settings ORDER BY setting_key");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings'])) {
    $updated = 0;
    
    // Update each setting
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'setting_') === 0) {
            $setting_key = substr($key, 8); // Remove 'setting_' prefix
            
            // Validate the setting exists
            if (array_key_exists($setting_key, $settings)) {
                $stmt = $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
                $result = $stmt->execute([$value, $setting_key]);
                
                if ($result) {
                    $updated++;
                }
            }
        }
    }
    
    if ($updated > 0) {
        $success_message = "Settings updated successfully ($updated settings changed).";
        logActivity($user['id'], 'admin_settings_updated', "$updated settings were updated");
        
        // Refresh settings
        $stmt = $pdo->query("SELECT * FROM site_settings ORDER BY setting_key");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    } else {
        $error_message = "No settings were updated.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Star-Clicks Admin</title>
    
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

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Site Settings</h1>
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
            
            <div class="card">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Configuration Settings</h2>
                
                <form method="POST" action="">
                    <input type="hidden" name="update_settings" value="1">
                    
                    <div class="space-y-6">
                        <!-- Site Name -->
                        <div class="form-group">
                            <label for="setting_site_name" class="form-label">Site Name</label>
                            <input type="text" id="setting_site_name" name="setting_site_name" class="form-control" 
                                   value="<?php echo escape($settings['site_name'] ?? ''); ?>" required>
                            <p class="text-sm text-gray-500 mt-1">The name of the website</p>
                        </div>
                        
                        <!-- Site Description -->
                        <div class="form-group">
                            <label for="setting_site_description" class="form-label">Site Description</label>
                            <textarea id="setting_site_description" name="setting_site_description" class="form-control" rows="3"><?php echo escape($settings['site_description'] ?? ''); ?></textarea>
                            <p class="text-sm text-gray-500 mt-1">Description of the website for SEO</p>
                        </div>
                        
                        <!-- Minimum Payout -->
                        <div class="form-group">
                            <label for="setting_minimum_payout" class="form-label">Minimum Payout Amount</label>
                            <input type="number" id="setting_minimum_payout" name="setting_minimum_payout" class="form-control" 
                                   value="<?php echo escape($settings['minimum_payout'] ?? '5.00'); ?>" step="0.01" min="0.01" required>
                            <p class="text-sm text-gray-500 mt-1">Minimum amount users can withdraw</p>
                        </div>
                        
                        <!-- Minimum Deposit -->
                        <div class="form-group">
                            <label for="setting_minimum_deposit" class="form-label">Minimum Deposit Amount</label>
                            <input type="number" id="setting_minimum_deposit" name="setting_minimum_deposit" class="form-control" 
                                   value="<?php echo escape($settings['minimum_deposit'] ?? '5.00'); ?>" step="0.01" min="0.01" required>
                            <p class="text-sm text-gray-500 mt-1">Minimum amount advertisers can deposit</p>
                        </div>
                        
                        <!-- CPC Rate -->
                        <div class="form-group">
                            <label for="setting_cpc_rate" class="form-label">Default Cost Per Click Rate</label>
                            <input type="number" id="setting_cpc_rate" name="setting_cpc_rate" class="form-control" 
                                   value="<?php echo escape($settings['cpc_rate'] ?? '0.01'); ?>" step="0.01" min="0.01" required>
                            <p class="text-sm text-gray-500 mt-1">Default cost per click rate</p>
                        </div>
                        
                        <!-- Publisher Commission -->
                        <div class="form-group">
                            <label for="setting_publisher_commission" class="form-label">Publisher Commission Rate</label>
                            <input type="number" id="setting_publisher_commission" name="setting_publisher_commission" class="form-control" 
                                   value="<?php echo escape($settings['publisher_commission'] ?? '0.50'); ?>" step="0.01" min="0" max="1" required>
                            <p class="text-sm text-gray-500 mt-1">Percentage of CPC that goes to publisher (0.50 = 50%)</p>
                        </div>
                        
                        <!-- Referral Commission -->
                        <div class="form-group">
                            <label for="setting_referral_commission" class="form-label">Referral Commission Rate</label>
                            <input type="number" id="setting_referral_commission" name="setting_referral_commission" class="form-control" 
                                   value="<?php echo escape($settings['referral_commission'] ?? '0.10'); ?>" step="0.01" min="0" max="1" required>
                            <p class="text-sm text-gray-500 mt-1">Percentage commission for referrals (0.10 = 10%)</p>
                        </div>
                        
                        <!-- Auto Payout Enabled -->
                        <div class="form-group">
                            <label for="setting_auto_payout_enabled" class="form-label">Auto Payout Enabled</label>
                            <select id="setting_auto_payout_enabled" name="setting_auto_payout_enabled" class="form-control">
                                <option value="1" <?php echo ($settings['auto_payout_enabled'] ?? '1') == '1' ? 'selected' : ''; ?>>Enabled</option>
                                <option value="0" <?php echo ($settings['auto_payout_enabled'] ?? '1') == '0' ? 'selected' : ''; ?>>Disabled</option>
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Whether auto payout feature is enabled</p>
                        </div>
                        
                        <!-- CAPTCHA Enabled -->
                        <div class="form-group">
                            <label for="setting_captcha_enabled" class="form-label">CAPTCHA Enabled</label>
                            <select id="setting_captcha_enabled" name="setting_captcha_enabled" class="form-control">
                                <option value="1" <?php echo ($settings['captcha_enabled'] ?? '1') == '1' ? 'selected' : ''; ?>>Enabled</option>
                                <option value="0" <?php echo ($settings['captcha_enabled'] ?? '1') == '0' ? 'selected' : ''; ?>>Disabled</option>
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Whether CAPTCHA is required on forms</p>
                        </div>
                        
                        <!-- Maintenance Mode -->
                        <div class="form-group">
                            <label for="setting_maintenance_mode" class="form-label">Maintenance Mode</label>
                            <select id="setting_maintenance_mode" name="setting_maintenance_mode" class="form-control">
                                <option value="0" <?php echo ($settings['maintenance_mode'] ?? '0') == '0' ? 'selected' : ''; ?>>Off</option>
                                <option value="1" <?php echo ($settings['maintenance_mode'] ?? '0') == '1' ? 'selected' : ''; ?>>On</option>
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Enable maintenance mode (limits access)</p>
                        </div>
                        
                        <!-- PayPal Enabled -->
                        <div class="form-group">
                            <label for="setting_paypal_enabled" class="form-label">PayPal Enabled</label>
                            <select id="setting_paypal_enabled" name="setting_paypal_enabled" class="form-control">
                                <option value="1" <?php echo ($settings['paypal_enabled'] ?? '1') == '1' ? 'selected' : ''; ?>>Enabled</option>
                                <option value="0" <?php echo ($settings['paypal_enabled'] ?? '1') == '0' ? 'selected' : ''; ?>>Disabled</option>
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Whether PayPal is available as a payment method</p>
                        </div>
                        
                        <!-- Bank Transfer Enabled -->
                        <div class="form-group">
                            <label for="setting_bank_transfer_enabled" class="form-label">Bank Transfer Enabled</label>
                            <select id="setting_bank_transfer_enabled" name="setting_bank_transfer_enabled" class="form-control">
                                <option value="1" <?php echo ($settings['bank_transfer_enabled'] ?? '1') == '1' ? 'selected' : ''; ?>>Enabled</option>
                                <option value="0" <?php echo ($settings['bank_transfer_enabled'] ?? '1') == '0' ? 'selected' : ''; ?>>Disabled</option>
                            </select>
                            <p class="text-sm text-gray-500 mt-1">Whether bank transfer is available as a payment method</p>
                        </div>
                    </div>
                    
                    <div class="form-group mt-8">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save mr-2"></i>Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>