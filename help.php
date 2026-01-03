<?php
// Help/FAQ Page for Star-Clicks Clone
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & FAQs - Star-Clicks Clone</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
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
                    <?php if (isLoggedIn()): ?>
                        <a href="portal/dashboard.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Dashboard</a>
                        <a href="portal/logout.php" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Logout</a>
                    <?php else: ?>
                        <a href="portal/signin.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Sign In</a>
                        <a href="portal/signup.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-blue-600 py-6 px-8">
                <h1 class="text-3xl font-bold text-white">Help & Frequently Asked Questions</h1>
            </div>
            
            <div class="p-8">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Browse Our Help Topics</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="#general" class="border border-gray-200 rounded-lg p-4 hover:bg-blue-50 hover:border-blue-300 transition duration-300">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-600 text-xl mr-3"></i>
                                <h3 class="font-semibold text-lg text-gray-900">General Information</h3>
                            </div>
                        </a>
                        
                        <a href="#publishers" class="border border-gray-200 rounded-lg p-4 hover:bg-blue-50 hover:border-blue-300 transition duration-300">
                            <div class="flex items-center">
                                <i class="fas fa-user-circle text-blue-600 text-xl mr-3"></i>
                                <h3 class="font-semibold text-lg text-gray-900">For Publishers</h3>
                            </div>
                        </a>
                        
                        <a href="#advertisers" class="border border-gray-200 rounded-lg p-4 hover:bg-blue-50 hover:border-blue-300 transition duration-300">
                            <div class="flex items-center">
                                <i class="fas fa-bullhorn text-blue-600 text-xl mr-3"></i>
                                <h3 class="font-semibold text-lg text-gray-900">For Advertisers</h3>
                            </div>
                        </a>
                        
                        <a href="#payments" class="border border-gray-200 rounded-lg p-4 hover:bg-blue-50 hover:border-blue-300 transition duration-300">
                            <div class="flex items-center">
                                <i class="fas fa-dollar-sign text-blue-600 text-xl mr-3"></i>
                                <h3 class="font-semibold text-lg text-gray-900">Payments & Payouts</h3>
                            </div>
                        </a>
                    </div>
                </div>
                
                <div id="general" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">General Information</h2>
                    
                    <div class="space-y-6">
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">What is Star-Clicks Clone?</h3>
                            <p class="text-gray-600">Star-Clicks Clone is an online advertising network that connects advertisers with publishers, offering advertising services and monetization opportunities. It allows publishers to earn money by clicking ads and advertisers to promote their websites to a large audience.</p>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">How do I sign up?</h3>
                            <p class="text-gray-600">You can sign up as either a publisher or advertiser. Publishers earn money by clicking ads, while advertisers pay to promote their websites. Simply click on the "Sign Up" button in the top navigation and choose your account type.</p>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Is it free to join?</h3>
                            <p class="text-gray-600">Yes, joining as a publisher is completely free. Advertisers need to make a minimum deposit of $5.00 to start advertising.</p>
                        </div>
                    </div>
                </div>
                
                <div id="publishers" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">For Publishers</h2>
                    
                    <div class="space-y-6">
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">How do I earn money as a publisher?</h3>
                            <p class="text-gray-600">As a publisher, you earn money by clicking on ads displayed on the platform. You get paid per valid click based on the cost-per-click (CPC) rate set by the advertiser. You can withdraw your earnings when you reach the minimum payout threshold.</p>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">What is the minimum payout amount?</h3>
                            <p class="text-gray-600">The minimum payout amount is $5.00. Once your account balance reaches this amount, you can request a withdrawal.</p>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">What payment methods are available?</h3>
                            <p class="text-gray-600">We offer multiple payment methods including PayPal, bank transfer, and Bitcoin. You can select your preferred method when requesting a payout.</p>
                        </div>
                    </div>
                </div>
                
                <div id="advertisers" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">For Advertisers</h2>
                    
                    <div class="space-y-6">
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">How do I start advertising?</h3>
                            <p class="text-gray-600">To start advertising, sign up as an advertiser, deposit funds into your account, and create your first advertisement. You'll need to set a daily budget, cost-per-click, and the URL you want to promote.</p>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">How are clicks charged?</h3>
                            <p class="text-gray-600">You only pay for valid clicks on your ads. We use advanced fraud detection to ensure you're only charged for legitimate clicks from real users. Invalid clicks are filtered out and not charged to your account.</p>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Can I track my ad performance?</h3>
                            <p class="text-gray-600">Yes, our dashboard provides detailed statistics about your ads including clicks, impressions, click-through rates, and total spent. You can monitor your campaigns in real-time and adjust your strategy accordingly.</p>
                        </div>
                    </div>
                </div>
                
                <div id="payments" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Payments & Payouts</h2>
                    
                    <div class="space-y-6">
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">How long does it take to process payouts?</h3>
                            <p class="text-gray-600">Manual payout requests typically take up to 15 working days to be reviewed and processed. Auto payouts are sent automatically and do not require review. Gold members receive priority processing for their payouts.</p>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Are there any fees for withdrawals?</h3>
                            <p class="text-gray-600">We do not charge any fees for withdrawals, but you may be subject to fees from your payment provider (like PayPal or bank transfer fees). These fees are typically paid by the member.</p>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">What documents are required for payouts?</h3>
                            <p class="text-gray-600">For larger payouts, we may require verification documents including a copy of your ID (passport or national ID) and proof of address. The specific requirements may vary depending on your location and the payout amount.</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Still need help?</h3>
                    <p class="text-gray-600 mb-4">If you couldn't find the answer to your question, our support team is ready to help you.</p>
                    <a href="portal/support.php" class="btn-primary inline-block">
                        <i class="fas fa-ticket-alt mr-2"></i>Submit a Support Ticket
                    </a>
                    <a href="how-to-contact-star-clicks.php" class="btn-secondary inline-block ml-2">
                        <i class="fas fa-envelope mr-2"></i>Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-lg font-semibold mb-4">Site Links</h4>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-300 hover:text-white">Home</a></li>
                        <li><a href="portal/signup.php?action=p" class="text-gray-300 hover:text-white">Publishers</a></li>
                        <li><a href="portal/signup.php?action=a" class="text-gray-300 hover:text-white">Advertisers</a></li>
                        <li><a href="portal/signin.php" class="text-gray-300 hover:text-white">Sign In</a></li>
                        <li><a href="portal/signup.php" class="text-gray-300 hover:text-white">Sign Up</a></li>
                        <li><a href="how-to-contact-star-clicks.php" class="text-gray-300 hover:text-white">Contact us</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="terms.php" class="text-gray-300 hover:text-white">Terms</a></li>
                        <li><a href="privacy.php" class="text-gray-300 hover:text-white">Privacy Policy</a></li>
                        <li><a href="cookies.php" class="text-gray-300 hover:text-white">Cookies Policy</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Help & Support</h4>
                    <ul class="space-y-2">
                        <li><a href="help.php" class="text-gray-300 hover:text-white">Help & FAQs</a></li>
                        <li><a href="portal/support.php" class="text-gray-300 hover:text-white">Support</a></li>
                        <li><a href="how-to-contact-star-clicks.php" class="text-gray-300 hover:text-white">Contact Us</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Us</h4>
                    <p class="text-gray-300">147 Botanic Avenue</p>
                    <p class="text-gray-300">Belfast, Northern Ireland</p>
                    <p class="text-gray-300">BT7 1JJ</p>
                    <p class="text-gray-300 mt-2">Phone: +44 203 290 8015</p>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-400">2008-2026 © All Rights Reserved. Privacy Policy | Terms of Service</p>
            </div>
        </div>
    </footer>
</body>
</html>