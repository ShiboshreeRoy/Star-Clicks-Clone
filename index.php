<?php
// Star-Clicks Clone - Main Index Page
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Main landing page content
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Earn & Make Money Online - Get Paid By PayPal Bank Transfer</title>
    <meta name="description" content="Join Star-Clicks to earn money online as a publisher or advertise your website to reach millions of potential customers.">
    
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
                    <a href="#publishers" class="text-gray-700 hover:text-blue-600 px-3 py-2">Publishers</a>
                    <a href="#advertisers" class="text-gray-700 hover:text-blue-600 px-3 py-2">Advertisers</a>
                    <a href="portal/signin.php" class="text-gray-700 hover:text-blue-600 px-3 py-2">Sign In</a>
                    <a href="portal/signup.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Sign Up</a>
                </div>
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" class="text-gray-700 hover:text-blue-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="#publishers" class="text-gray-700 hover:text-blue-600 block px-3 py-2">Publishers</a>
                <a href="#advertisers" class="text-gray-700 hover:text-blue-600 block px-3 py-2">Advertisers</a>
                <a href="portal/signin.php" class="text-gray-700 hover:text-blue-600 block px-3 py-2">Sign In</a>
                <a href="portal/signup.php" class="bg-blue-600 text-white block px-3 py-2 rounded-md">Sign Up</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-500 to-purple-600 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-extrabold mb-6">Earn & Make Money Online</h1>
                <h2 class="text-2xl md:text-3xl font-bold mb-8">Get Paid By PayPal Bank Transfer</h2>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="portal/signup.php?action=p" class="bg-white text-blue-600 font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition duration-300">
                        START NOW - PUBLISHER
                    </a>
                    <a href="portal/signup.php?action=a" class="bg-yellow-500 text-gray-900 font-bold py-3 px-8 rounded-lg hover:bg-yellow-400 transition duration-300">
                        START NOW - ADVERTISER
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Publishers Section -->
    <section id="publishers" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">PUBLISHERS - Earn Money Online</h2>
                <h3 class="text-2xl font-semibold text-gray-700">Start Working Now, Earn Money & Get Paid Online</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h4 class="text-xl font-bold text-blue-600 mb-4">Publish Ads - Get. Paid. Online.</h4>
                    <p class="text-gray-600 mb-6">Sign in to see how much you can earn :-)</p>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                    <i class="fas fa-bolt"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h5 class="text-lg font-semibold text-gray-900">Income Paid Instantly</h5>
                                <p class="mt-2 text-gray-600">Account's balance is updated immediately</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-500 text-white">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h5 class="text-lg font-semibold text-gray-900">Withdraw Your Balance</h5>
                                <p class="mt-2 text-gray-600">Withdraw your balance to Paypal, Bank Transfer or many others</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-yellow-500 text-white">
                                    <i class="fas fa-rocket"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h5 class="text-lg font-semibold text-gray-900">Quick & Free Start</h5>
                                <p class="mt-2 text-gray-600">100% Free to join and start</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-100 p-8 rounded-xl">
                    <img src="images/publisher-icon.png" alt="Publisher Icon" class="mx-auto mb-6" style="max-height: 200px;">
                    <a href="portal/signup.php?action=p" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-300 text-center block font-bold">
                        PUBLISHER SIGN UP
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Advertisers Section -->
    <section id="advertisers" class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">ADVERTISERS - Advertise Online</h2>
                <h3 class="text-2xl font-semibold text-gray-700">Transform Your Business By Advertising Your Web Site</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="bg-gray-100 p-8 rounded-xl">
                    <img src="images/advertiser-icon-l.png" alt="Advertiser Icon" class="mx-auto mb-6" style="max-height: 200px;">
                    <a href="portal/signup.php?action=a" class="w-full bg-yellow-500 text-gray-900 py-3 px-6 rounded-lg hover:bg-yellow-400 transition duration-300 text-center block font-bold">
                        ADVERTISER SIGN UP
                    </a>
                </div>
                
                <div>
                    <h4 class="text-xl font-bold text-blue-600 mb-4">Advertise Your Website</h4>
                    <p class="text-gray-600 mb-6">Advertise your website to millions of potential customers and visitors</p>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                    <i class="fas fa-search"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h5 class="text-lg font-semibold text-gray-900">Improve Your Site SEO</h5>
                                <p class="mt-2 text-gray-600">One of the greatest ways to improve your site SEO and page ranking</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-500 text-white">
                                    <i class="fas fa-percentage"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h5 class="text-lg font-semibold text-gray-900">Pay Only For Clicks</h5>
                                <p class="mt-2 text-gray-600">Do not pay for impressions, only for real clicks</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-yellow-500 text-white">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h5 class="text-lg font-semibold text-gray-900">Minimum Deposit US$5.00</h5>
                                <p class="mt-2 text-gray-600">Start with a small investment and see the amazing results</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-lg font-semibold mb-4">Site Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#publishers" class="text-gray-300 hover:text-white">Publishers</a></li>
                        <li><a href="#advertisers" class="text-gray-300 hover:text-white">Advertisers</a></li>
                        <li><a href="portal/signin.php" class="text-gray-300 hover:text-white">Sign In</a></li>
                        <li><a href="portal/signup.php" class="text-gray-300 hover:text-white">Sign Up</a></li>
                        <li><a href="how-to-contact-star-clicks.php" class="text-gray-300 hover:text-white">Contact us</a></li>
                        <li><a href="blog.php" class="text-gray-300 hover:text-white">Blog</a></li>
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

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>