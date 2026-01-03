<?php
// Terms of Service Page for Star-Clicks Clone
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Star-Clicks Clone</title>
    
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
                <h1 class="text-3xl font-bold text-white">Terms of Service</h1>
            </div>
            
            <div class="p-8">
                <div class="prose max-w-none">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">General Terms</h2>
                    <p class="mb-4">You must read, agree with and accept all of the policies and terms below (the "Rules") to join Star-Clicks Clone program. "You" or "you" means the party listed on the account. You must have the authority to agree to these Rules for that party. "Star-Clicks Clone" means Star-Clicks Clone located in the system. By creating an account, you accept and acknowledge the following rules.</p>
                    
                    <p class="mb-6">Star-Clicks Clone may modify or terminate the following rules of use at any time for any reason. We periodically update our terms to ensure their consistency with our policies and allow for addition of new features and continued improvement of our advertising solution.</p>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Common Terms</h3>
                    
                    <div class="space-y-6 mb-8">
                        <div>
                            <h4 class="font-bold text-lg text-gray-800">Impression (Impr.)</h4>
                            <p>The number of impressions is the number of times an ad is displayed on Star-Clicks Clone or Star-Clicks Clone Network. Check your impressions to see how many people your ad is displayed to.</p>
                        </div>
                        
                        <div>
                            <h4 class="font-bold text-lg text-gray-800">Cost-per-click (CPC)</h4>
                            <p>Cost-Per-Clicks CPC is the price for each click. For Advertisers CPC means the money they pay for each click their ad receives and for Publishers CPC means the money they receive for each ad they click.</p>
                        </div>
                        
                        <div>
                            <h4 class="font-bold text-lg text-gray-800">Minimum cost-per-click (minimum CPC)</h4>
                            <p>The lowest amount that you are receive to click an ad.</p>
                        </div>
                        
                        <div>
                            <h4 class="font-bold text-lg text-gray-800">HTML code</h4>
                            <p>HTML code is a method that provides Publishers more ads. Advertisers ads are distributed through PPC section and HTML code of Publishers assuring the highest quality of ad management for Advertisers. HTML Code should be placed on an online website/blog with original contents. Only Gold and Platinum members have access to HTML Code.</p>
                        </div>
                        
                        <div>
                            <h4 class="font-bold text-lg text-gray-800">Pay-Per-Click (PPC)</h4>
                            <p>Pay-Per-Click PPC means the money Publishers receive for each click on the ads. The balance displayed in Publisher accounts shows the total amount of PPC of the Publisher.</p>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-4">User Account</h3>
                    <ul class="list-disc pl-6 mb-6 space-y-2">
                        <li>Only one account per member and per household is allowed. Any attempt to create or sign in more than one account will result in suspending all created accounts. Furthermore, no payout or refund will be made to members who create multiple accounts. This includes Silver, Gold and Platinum members. Also members with more than one account will be banned from further using Star-Clicks Clone</li>
                        <li>Member information is NOT shared with public or any other third party. Members can not transfer an account's balance to another account.</li>
                        <li>Providing false or wrong account information will result in immediate account closure.</li>
                        <li>Any attempt to misuse Star-Clicks Clone will result in immediate account closure. Payouts are only sent to verified accounts.</li>
                        <li>Members need to verify their email address and their mobile phone.</li>
                    </ul>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Referrals</h3>
                    <p class="mb-6">Referral ID is activated after the first payout sent to the member. Members do not get paid for referrals if the referral is not activated.</p>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Prohibited Clicks and Impressions</h3>
                    <p class="mb-4">Any method that artificially generates clicks or impressions is strictly prohibited. These prohibited methods include but are not limited to: repeated manual clicks or impressions, incentives to click or to generate impressions, using robots, iframes, automated click and impression generating tools, third-party services that generate clicks or impressions such as paid-to-click, paid-to-surf, paid-to-read, auto surf, and click-exchange programs, or any deceptive software.</p>
                    <p class="mb-6">Please note that clicking on your own HTML code ads for any reason is prohibited, to avoid potential inflation of advertiser costs.</p>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Publishers and Use of Ads</h3>
                    <p class="mb-4">Star-Clicks Clone will grant a percentage fee that will amount to 50% (and possibly adjusted at any time) of the amount collected by Star-Clicks Clone in connection with each valid Click to a paid Star-Clicks Clone Advertisements. Further, Star-Clicks Clone will only pay a percentage fee for valid clicks as soon as and provided that Star-Clicks Clone has received payment from its advertisers.</p>
                    <p class="mb-6">Star-Clicks Clone shall not be responsible for payments of the percentage fee that have not been performed if data has been lost due to hardware failure, data failure, hacker attack, fire, flood, or other reasons or events that are beyond Star-Clicks Clone's sphere of influence.</p>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Publishers Site Content</h3>
                    <p class="mb-4">Publisher may NOT use Star-Clicks Clone ads with the following contents:</p>
                    <ul class="list-disc pl-6 mb-6 space-y-2">
                        <li>Violent content, racial intolerance, sexual content, or advocacy against any individual, group, or organization.</li>
                        <li>Content regarding programs which compensate users for clicking on ads or offers, performing searches, surfing websites, or reading emails.</li>
                        <li>Illegal content.</li>
                        <li>Content protected by copyright, unless you have the necessary legal rights to display that content.</li>
                        <li>Poor content, or sites "made for advertising".</li>
                        <li>Main language of the site must be English language.</li>
                        <li>Publisher site must have some contents, clicks on the links that appear on a page without contents will not be accepted.</li>
                        <li>Publisher page must have contents at the time of payout review and at the time of payout release.</li>
                        <li>Publishers may be required to provide their traffic sources.</li>
                    </ul>
                    
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Withdraw and Payout Release</h3>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Manual payout requests are reviewed before release</li>
                        <li>No payout or remaining balance is paid if the Publisher account is closed or suspended</li>
                        <li>Auto Payouts are sent automatically and are not reviewed</li>
                        <li>Manual payouts need up to 15 working days to be reviewed</li>
                        <li>We aim to release payouts of Gold members in 15 working days</li>
                        <li>We do not guarantee payout release time for Silver members</li>
                        <li>The commission charged by PayPal, Bitcoin (miner's fees), and bank transfers should be paid by the members.</li>
                    </ul>
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