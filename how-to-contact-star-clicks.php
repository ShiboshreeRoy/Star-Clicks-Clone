<?php
// Contact Page for Star-Clicks Clone
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Star-Clicks Clone</title>
    
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
                <h1 class="text-3xl font-bold text-white">Contact Us</h1>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Get In Touch</h2>
                        
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full">
                                    <i class="fas fa-phone text-blue-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Phone</h3>
                                    <p class="text-gray-600">+44 203 290 8015</p>
                                    <p class="text-sm text-gray-500 mt-1">Available Monday to Friday, 9am to 5pm, and Saturday 11am to 3pm</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full">
                                    <i class="fas fa-envelope text-blue-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Email</h3>
                                    <p class="text-gray-600">[email protected]</p>
                                    <p class="text-sm text-gray-500 mt-1">For general inquiries and support</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full">
                                    <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Address</h3>
                                    <p class="text-gray-600">147 Botanic Avenue</p>
                                    <p class="text-gray-600">Belfast, Northern Ireland</p>
                                    <p class="text-gray-600">BT7 1JJ</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full">
                                    <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Account Issues</h3>
                                    <p class="text-gray-600">For account-related issues</p>
                                    <p class="text-sm text-gray-500 mt-1">Must be addressed via email or support ticket</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Send Us a Message</h2>
                        
                        <form id="contactForm">
                            <div class="form-group">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email" class="form-label">Your Email</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" id="subject" name="subject" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="message" class="form-label">Message</label>
                                <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn-primary w-full">Send Message</button>
                            </div>
                        </form>
                        
                        <div id="messageSent" class="alert alert-success hidden mt-4">
                            <i class="fas fa-check-circle mr-2"></i>
                            Your message has been sent successfully!
                        </div>
                    </div>
                </div>
                
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Support Options</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="border border-gray-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-life-ring text-blue-600 text-2xl mr-3"></i>
                                <h3 class="text-xl font-semibold text-gray-900">Help & Support</h3>
                            </div>
                            <p class="text-gray-600 mb-4">Visit our support section for answers to frequently asked questions and common issues.</p>
                            <a href="help.php" class="text-blue-600 hover:underline font-medium">Visit Help Center</a>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-ticket-alt text-blue-600 text-2xl mr-3"></i>
                                <h3 class="text-xl font-semibold text-gray-900">Support Tickets</h3>
                            </div>
                            <p class="text-gray-600 mb-4">For personalized assistance, submit a support ticket and our team will respond as soon as possible.</p>
                            <a href="portal/support.php" class="text-blue-600 hover:underline font-medium">Submit Ticket</a>
                        </div>
                    </div>
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

    <script>
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // In a real application, you would send the form data to a server
            // For this demo, we'll just show a success message
            document.getElementById('messageSent').classList.remove('hidden');
            
            // Reset form
            this.reset();
            
            // Hide success message after 5 seconds
            setTimeout(function() {
                document.getElementById('messageSent').classList.add('hidden');
            }, 5000);
        });
    </script>
</body>
</html>