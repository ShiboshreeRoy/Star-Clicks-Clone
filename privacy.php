<?php
// Privacy Policy Page for Star-Clicks Clone
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Star-Clicks Clone</title>
    
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
                <h1 class="text-3xl font-bold text-white">Privacy Policy</h1>
            </div>
            
            <div class="p-8">
                <div class="prose max-w-none">
                    <p class="mb-6">At Star-Clicks Clone, accessible from <?php echo SITE_URL; ?>, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by Star-Clicks Clone and how we use it.</p>
                    
                    <p class="mb-6">If you have additional questions or require more information about our Privacy Policy, do not hesitate to contact us.</p>
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Log Files</h2>
                    <p class="mb-6">Star-Clicks Clone follows a standard procedure of using log files. These files log visitors when they visit websites. All hosting companies do this and a part of hosting services' analytics. The information collected by log files include internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date and time stamp, referring/exit pages, and possibly the number of clicks. These are not linked to any information that is personally identifiable. The purpose of the information is for analyzing trends, administering the site, tracking users' movement on the website, and gathering demographic information.</p>
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Cookies and Web Beacons</h2>
                    <p class="mb-6">Like any other website, Star-Clicks Clone uses 'cookies'. These cookies are used to store information including visitors' preferences, and the pages on the website that the visitor accessed or visited. The information is used to optimize the users' experience by customizing our web page content based on visitors' browser type and/or other information.</p>
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Information We Collect</h2>
                    <p class="mb-4">We may collect the following information during your use of our service:</p>
                    <ul class="list-disc pl-6 mb-6 space-y-2">
                        <li>Basic contact and payment information during registration</li>
                        <li>Interaction details with customer support</li>
                        <li>Activity on client websites, including browser and IP address data</li>
                        <li>Personal information provided during account setup</li>
                        <li>Financial information for payment processing</li>
                    </ul>
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">How We Use Your Information</h2>
                    <p class="mb-4">We use the information we collect in various ways, including to:</p>
                    <ul class="list-disc pl-6 mb-6 space-y-2">
                        <li>Provide, operate, and maintain our service</li>
                        <li>Improve, personalize, and expand our service</li>
                        <li>Understand and analyze how you use our service</li>
                        <li>Develop new products, services, features, and functionality</li>
                        <li>Find and prevent fraud</li>
                        <li>Communicate with you, either directly or through one of our partners</li>
                        <li>Send you emails</li>
                        <li>Find and resolve technical issues</li>
                    </ul>
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Sharing Your Information</h2>
                    <p class="mb-6">We do not share customer information with third parties except in specific circumstances, such as legal requirements or safety concerns. We may share your information with trusted third parties who assist us in operating our website, conducting business, or serving our users, as long as those parties agree to maintain the confidentiality of your information.</p>
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Security</h2>
                    <p class="mb-6">We value your trust in providing us your Personal Information, thus we are striving to use commercially acceptable means of protecting it. But remember that no method of transmission over the internet, or method of electronic storage is 100% secure and reliable, and we cannot guarantee its absolute security.</p>
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Links to Other Sites</h2>
                    <p class="mb-6">Our Service may contain links to external sites that are not operated by us. If you click on a third party link, you will be directed to that third party's site. We strongly advise you to review the Privacy Policy of every site you visit. We have no control over and assume no responsibility for the content, privacy policies or practices of any third party sites or services.</p>
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Children's Privacy</h2>
                    <p class="mb-6">Our Services do not address anyone under the age of 13. We do not knowingly collect personally identifiable information from children under 13. In the case we discover that a child under 13 has provided us with personal information, we immediately delete this from our servers. If you are a parent or guardian and you are aware that your child has provided us with personal information, please contact us so that we will be able to do necessary actions.</p>
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Changes to This Privacy Policy</h2>
                    <p class="mb-6">We may update our Privacy Policy from time to time. Thus, we advise you to review this page periodically for any changes. We will notify you of any changes by posting the new Privacy Policy on this page. These changes are effective immediately after they are posted on this page.</p>
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Contact Us</h2>
                    <p class="mb-6">If you have any questions or suggestions about our Privacy Policy, do not hesitate to contact us at <?php echo SITE_EMAIL; ?>.</p>
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