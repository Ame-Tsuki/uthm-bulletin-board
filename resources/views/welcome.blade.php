<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UTHM Digital Bulletin Board | Campus Communication Hub</title>
    <meta name="description" content="The official digital hub for all UTHM announcements, events, and campus news.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <script src="{{ asset('js/welcome.js') }}" defer></script>
    <style>
        :root {
            --uthm-blue: #0056a6;
            --uthm-blue-light: #e6f0fa;
            --uthm-blue-dark: #003d75;
            --uthm-green: #6ea342;
            --uthm-green-light: #f0f7e9;
            --uthm-yellow: #ffc72c;
        }

        * {
            font-family: 'Inter', sans-serif;
        }
        
        .bg-uthm-blue { background-color: var(--uthm-blue); }
        .text-uthm-blue { color: var(--uthm-blue); }
        .border-uthm-blue { border-color: var(--uthm-blue); }
        .hover\:bg-uthm-blue-dark:hover { background-color: var(--uthm-blue-dark); }
        .focus\:ring-uthm-blue:focus { --tw-ring-color: var(--uthm-blue); }
        
        .bg-uthm-green-light { background-color: var(--uthm-green-light); }
        .bg-uthm-blue-light { background-color: var(--uthm-blue-light); }
        .text-uthm-green { color: var(--uthm-green); }
        .bg-uthm-green { background-color: var(--uthm-green); }
        .text-uthm-yellow { color: var(--uthm-yellow); }
        
        /* Enhanced nav link with smooth underline animation */
        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: var(--uthm-blue);
            transition: width 0.3s ease, left 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
            left: 0;
        }
        
        .announcement-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .announcement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, var(--uthm-blue) 0%, var(--uthm-blue-dark) 100%);
        }
        
        .stat-card {
            background: linear-gradient(145deg, #ffffff, #f5f7fa);
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.05);
        }

        /* Improved mobile menu with slide animation */
        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, opacity 0.3s ease, padding 0.3s ease;
            opacity: 0;
        }

        .mobile-menu.open {
            max-height: 400px;
            opacity: 1;
            padding-bottom: 1rem;
        }

        /* Animated hamburger icon */
        .hamburger-line {
            display: block;
            width: 24px;
            height: 2px;
            background-color: currentColor;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .hamburger.active .hamburger-line:nth-child(1) {
            transform: translateY(8px) rotate(45deg);
        }

        .hamburger.active .hamburger-line:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active .hamburger-line:nth-child(3) {
            transform: translateY(-8px) rotate(-45deg);
        }

        /* Hero section fade-in animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease forwards;
        }

        .fade-in-up-delay-1 {
            animation: fadeInUp 0.6s ease 0.1s forwards;
            opacity: 0;
        }

        .fade-in-up-delay-2 {
            animation: fadeInUp 0.6s ease 0.2s forwards;
            opacity: 0;
        }

        .fade-in-up-delay-3 {
            animation: fadeInUp 0.6s ease 0.3s forwards;
            opacity: 0;
        }

        /* Announcement preview card floating animation */
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .float-animation {
            animation: float 4s ease-in-out infinite;
        }

        /* Button hover effects */
        .btn-primary {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        /* Scroll reveal animation */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Active nav state indicator */
        .nav-link.active {
            color: var(--uthm-blue);
            font-weight: 600;
        }

        .nav-link.active::after {
            width: 100%;
            left: 0;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <!-- Logo -->
              
<div class="flex items-center">
    <a href="/" class="flex items-center space-x-3 group">
        <div class="transition-transform group-hover:scale-105">
            <img src="{{ asset('images/logo_uthm.jpg') }}" 
                 alt="UTHM Logo" 
                 class="h-16 w-auto object-contain">
        </div>
        <div>
            <h1 class="text-xl font-bold text-uthm-blue">UTHM Digital Bulletin</h1>
            <p class="text-xs text-gray-500">Official Communication Platform</p>
        </div>
    </a>
</div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#" class="nav-link active text-gray-700 hover:text-uthm-blue font-medium">Home</a>
                    <a href="#" class="nav-link text-gray-700 hover:text-uthm-blue font-medium">Announcements</a>
                    <a href="#" class="nav-link text-gray-700 hover:text-uthm-blue font-medium">Events</a>
                    <a href="#" class="nav-link text-gray-700 hover:text-uthm-blue font-medium">Calendar</a>
                    <a href="#" class="nav-link text-gray-700 hover:text-uthm-blue font-medium">Clubs</a>
                    
                    <!-- Auth Buttons -->
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('login') }}" class="px-5 py-2.5 text-uthm-blue border-2 border-uthm-blue rounded-lg hover:bg-uthm-blue hover:text-white transition-all duration-300 font-medium">Login</a>
                        <a href="{{ route('register') }}" class="btn-primary px-5 py-2.5 bg-uthm-blue text-white rounded-lg hover:bg-uthm-blue-dark transition-all duration-300 font-medium shadow-md hover:shadow-lg">Register</a>
                    </div>
                </div>
                
                <!-- Enhanced mobile menu button with animated hamburger -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" class="hamburger text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-uthm-blue" aria-label="Toggle menu" aria-expanded="false">
                        <span class="hamburger-line mb-1.5"></span>
                        <span class="hamburger-line mb-1.5"></span>
                        <span class="hamburger-line"></span>
                    </button>
                </div>
            </div>
            
            <!-- Enhanced Mobile Menu with slide animation -->
            <div id="mobile-menu" class="mobile-menu md:hidden">
                <div class="flex flex-col space-y-1 pt-2 border-t border-gray-100">
                    <a href="#" class="text-gray-700 hover:text-uthm-blue hover:bg-uthm-blue-light font-medium py-3 px-4 rounded-lg transition-colors">Home</a>
                    <a href="#" class="text-gray-700 hover:text-uthm-blue hover:bg-uthm-blue-light font-medium py-3 px-4 rounded-lg transition-colors">Announcements</a>
                    <a href="#" class="text-gray-700 hover:text-uthm-blue hover:bg-uthm-blue-light font-medium py-3 px-4 rounded-lg transition-colors">Events</a>
                    <a href="#" class="text-gray-700 hover:text-uthm-blue hover:bg-uthm-blue-light font-medium py-3 px-4 rounded-lg transition-colors">Calendar</a>
                    <a href="#" class="text-gray-700 hover:text-uthm-blue hover:bg-uthm-blue-light font-medium py-3 px-4 rounded-lg transition-colors">Clubs</a>
                    <div class="pt-4 mt-2 border-t border-gray-100 space-y-3">
                        <a href="{{ route('login') }}" class="block px-4 py-3 text-center text-uthm-blue border-2 border-uthm-blue rounded-lg hover:bg-uthm-blue hover:text-white transition-all font-medium">Login</a>
                        <a href="{{ route('register') }}" class="block px-4 py-3 text-center bg-uthm-blue text-white rounded-lg hover:bg-uthm-blue-dark transition-all font-medium">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient text-white py-16 md:py-24 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-12">
                <div class="lg:w-1/2">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 fade-in-up leading-tight">Stay Connected.<br>Stay Informed.</h1>
                    <p class="text-xl mb-8 opacity-90 fade-in-up-delay-1 leading-relaxed">The official digital hub for all UTHM announcements, events, and campus news. Never miss an important update again.</p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 fade-in-up-delay-2">
                        <a href="{{ route('register') }}" class="btn-primary px-8 py-4 bg-white text-uthm-blue font-bold rounded-xl hover:bg-gray-100 text-center transition shadow-lg hover:shadow-xl">
                            <i class="fas fa-user-plus mr-2"></i>Join Now
                        </a>
                        <a href="#features" class="px-8 py-4 border-2 border-white text-white font-bold rounded-xl hover:bg-white hover:text-uthm-blue text-center transition">
                            <i class="fas fa-info-circle mr-2"></i>Learn More
                        </a>
                    </div>
                </div>
                <div class="lg:w-1/2 flex justify-center fade-in-up-delay-3">
                    <div class="relative float-animation">
                        <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-md">
                            <div class="flex items-center mb-4">
                                <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div>
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Latest Announcements</h3>
                            
                            <!-- Sample Announcements -->
                            <div class="space-y-4">
                                <div class="p-4 bg-uthm-blue-light rounded-xl hover:shadow-md transition-shadow cursor-pointer">
                                    <div class="flex items-center mb-2">
                                        <span class="bg-uthm-blue text-white text-xs px-2.5 py-1 rounded-full mr-2 font-medium">University</span>
                                        <span class="text-xs text-gray-500">Today</span>
                                    </div>
                                    <p class="font-semibold text-gray-800">Mid-Semester Break Schedule Released</p>
                                </div>
                                
                                <div class="p-4 bg-uthm-green-light rounded-xl hover:shadow-md transition-shadow cursor-pointer">
                                    <div class="flex items-center mb-2">
                                        <span class="bg-uthm-green text-white text-xs px-2.5 py-1 rounded-full mr-2 font-medium">FCSIT</span>
                                        <span class="text-xs text-gray-500">2 days ago</span>
                                    </div>
                                    <p class="font-semibold text-gray-800">Final Year Project Submission Deadline</p>
                                </div>
                                
                                <div class="p-4 bg-yellow-50 rounded-xl hover:shadow-md transition-shadow cursor-pointer">
                                    <div class="flex items-center mb-2">
                                        <span class="bg-yellow-500 text-white text-xs px-2.5 py-1 rounded-full mr-2 font-medium">Event</span>
                                        <span class="text-xs text-gray-500">3 days ago</span>
                                    </div>
                                    <p class="font-semibold text-gray-800">Career Fair 2024 - Registration Open</p>
                                </div>
                            </div>
                            
                            <div class="mt-6 text-center">
                                <a href="#" class="inline-flex items-center text-uthm-blue font-semibold hover:underline group">
                                    View All Announcements 
                                    <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="stat-card rounded-2xl p-6 text-center reveal">
                    <div class="text-4xl font-extrabold text-uthm-blue mb-2">
                        <span class="counter" data-target="1250">0</span>+
                    </div>
                    <div class="text-gray-600 font-medium">Active Users</div>
                </div>
                <div class="stat-card rounded-2xl p-6 text-center reveal">
                    <div class="text-4xl font-extrabold text-uthm-green mb-2">
                        <span class="counter" data-target="324">0</span>
                    </div>
                    <div class="text-gray-600 font-medium">Announcements This Month</div>
                </div>
                <div class="stat-card rounded-2xl p-6 text-center reveal">
                    <div class="text-4xl font-extrabold text-purple-600 mb-2">
                        <span class="counter" data-target="45">0</span>
                    </div>
                    <div class="text-gray-600 font-medium">Upcoming Events</div>
                </div>
                <div class="stat-card rounded-2xl p-6 text-center reveal">
                    <div class="text-4xl font-extrabold text-uthm-yellow mb-2">
                        <span class="counter" data-target="28">0</span>
                    </div>
                    <div class="text-gray-600 font-medium">Active Clubs</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-4">Why Use UTHM Digital Bulletin?</h2>
                <p class="text-gray-600 max-w-2xl mx-auto text-lg">Designed specifically for the UTHM community to streamline communication and enhance campus engagement.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-md announcement-card reveal">
                    <div class="w-16 h-16 bg-uthm-blue-light rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-bullhorn text-2xl text-uthm-blue"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Centralized Announcements</h3>
                    <p class="text-gray-600 leading-relaxed">All official university communications in one place. Filter by faculty, club, or interest.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-md announcement-card reveal">
                    <div class="w-16 h-16 bg-uthm-green-light rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-calendar-check text-2xl text-uthm-green"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Personal Calendar Sync</h3>
                    <p class="text-gray-600 leading-relaxed">Add important dates directly to your Google Calendar or Outlook with one click.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-md announcement-card reveal">
                    <div class="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Verified Access Only</h3>
                    <p class="text-gray-600 leading-relaxed">Only verified UTHM students and staff can post, ensuring reliable information.</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="bg-white p-8 rounded-2xl shadow-md announcement-card reveal">
                    <div class="w-16 h-16 bg-yellow-50 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-mobile-alt text-2xl text-yellow-600"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Mobile Responsive</h3>
                    <p class="text-gray-600 leading-relaxed">Access announcements on any device - desktop, tablet, or smartphone.</p>
                </div>
                
                <!-- Feature 5 -->
                <div class="bg-white p-8 rounded-2xl shadow-md announcement-card reveal">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-filter text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Smart Filtering</h3>
                    <p class="text-gray-600 leading-relaxed">Find exactly what you need with advanced search and categorization tools.</p>
                </div>
                
                <!-- Feature 6 -->
                <div class="bg-white p-8 rounded-2xl shadow-md announcement-card reveal">
                    <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-comments text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-gray-800">Community Engagement</h3>
                    <p class="text-gray-600 leading-relaxed">Comment, provide feedback, and interact with announcements and events.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-uthm-blue-light">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-6">Ready to Transform Campus Communication?</h2>
            <p class="text-gray-600 mb-10 text-lg leading-relaxed">Join thousands of UTHM students and staff who are already staying connected through our Digital Bulletin Board.</p>
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="{{ route('register') }}" class="btn-primary px-10 py-4 bg-uthm-blue text-white font-bold rounded-xl hover:bg-uthm-blue-dark transition shadow-lg hover:shadow-xl">
                    <i class="fas fa-rocket mr-2"></i>Create Your Account
                </a>
                <a href="{{ route('login') }}" class="px-10 py-4 bg-white text-uthm-blue font-bold rounded-xl hover:bg-gray-100 transition shadow-lg border-2 border-gray-200">
                    <i class="fas fa-sign-in-alt mr-2"></i>Existing User Login
                </a>
            </div>
            <p class="mt-8 text-gray-500">
                <i class="fas fa-id-card mr-1"></i> UTHM ID required for registration
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                <!-- Column 1 -->
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="bg-uthm-blue p-3 rounded-xl">
                            <i class="fas fa-bullhorn text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">UTHM Digital Bulletin</h3>
                            <p class="text-sm text-gray-400">Official Platform</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-6 leading-relaxed">The centralized communication hub for Universiti Tun Hussein Onn Malaysia.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 hover:bg-uthm-blue hover:text-white transition-colors" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 hover:bg-uthm-blue hover:text-white transition-colors" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 hover:bg-uthm-blue hover:text-white transition-colors" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center text-gray-400 hover:bg-uthm-blue hover:text-white transition-colors" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Column 2 -->
                <div>
                    <h4 class="font-bold text-lg mb-6">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-uthm-blue"></i>Announcements</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-uthm-blue"></i>University Calendar</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-uthm-blue"></i>Clubs & Societies</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-uthm-blue"></i>Staff Portal</a></li>
                    </ul>
                </div>
                
                <!-- Column 3 -->
                <div>
                    <h4 class="font-bold text-lg mb-6">Resources</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-uthm-blue"></i>User Guide</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-uthm-blue"></i>FAQs</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-uthm-blue"></i>Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-uthm-blue"></i>Terms of Service</a></li>
                    </ul>
                </div>
                
                <!-- Column 4 -->
                <div>
                    <h4 class="font-bold text-lg mb-6">Contact</h4>
                    <ul class="space-y-4 text-gray-400">
                        <li class="flex items-start">
                            <div class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-uthm-blue text-sm"></i>
                            </div>
                            <span class="leading-relaxed">Universiti Tun Hussein Onn Malaysia, 86400 Parit Raja, Johor</span>
                        </li>
                        <li class="flex items-center">
                            <div class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-envelope text-uthm-blue text-sm"></i>
                            </div>
                            <a href="mailto:bulletin@uthm.edu.my" class="hover:text-white transition-colors">bulletin@uthm.edu.my</a>
                        </li>
                        <li class="flex items-center">
                            <div class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-phone text-uthm-blue text-sm"></i>
                            </div>
                            <a href="tel:+60745370000" class="hover:text-white transition-colors">+60 7-453 7000</a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-500">
                <p>&copy; 2025 UTHM Digital Bulletin Board. All rights reserved.</p>
                <p class="mt-2 text-sm">Final Year Project by Muhammad Amir Arif (AI230102) | Supervised by Dr. Norfadilla Binti Wahid | FCSIT</p>
            </div>
        </div>
    </footer>

    <!-- Enhanced JavaScript with animated counters and scroll reveal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            // Toggle mobile menu with animation
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('open');
                mobileMenuButton.classList.toggle('active');
                
                // Update aria-expanded
                const isExpanded = mobileMenu.classList.contains('open');
                mobileMenuButton.setAttribute('aria-expanded', isExpanded);
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                    mobileMenu.classList.remove('open');
                    mobileMenuButton.classList.remove('active');
                    mobileMenuButton.setAttribute('aria-expanded', 'false');
                }
            });

            // Close menu on escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && mobileMenu.classList.contains('open')) {
                    mobileMenu.classList.remove('open');
                    mobileMenuButton.classList.remove('active');
                    mobileMenuButton.setAttribute('aria-expanded', 'false');
                }
            });
            
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    if(targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if(targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80,
                            behavior: 'smooth'
                        });
                        
                        // Close mobile menu if open
                        mobileMenu.classList.remove('open');
                        mobileMenuButton.classList.remove('active');
                    }
                });
            });

            // Animated counters
            const counters = document.querySelectorAll('.counter');
            const speed = 200;

            const animateCounter = (counter) => {
                const target = +counter.getAttribute('data-target');
                const increment = target / speed;
                
                const updateCount = () => {
                    const count = +counter.innerText;
                    if (count < target) {
                        counter.innerText = Math.ceil(count + increment);
                        requestAnimationFrame(updateCount);
                    } else {
                        counter.innerText = target.toLocaleString();
                    }
                };
                
                updateCount();
            };

            // Scroll reveal animation
            const revealElements = document.querySelectorAll('.reveal');
            let scrollTimeout;
            const revealOnScroll = () => {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    revealElements.forEach(el => {
                        const elementTop = el.getBoundingClientRect().top;
                        const windowHeight = window.innerHeight;
                        
                        if(elementTop < windowHeight - 100) {
                            el.classList.add('visible');
                            
                            // Trigger counter animation if element contains a counter
                            const counter = el.querySelector('.counter');
                            if(counter && !counter.classList.contains('counted')) {
                                counter.classList.add('counted');
                                animateCounter(counter);
                            }
                        }
                    });
                }, 16); // ~60fps
            };

            // Initial check and scroll listener
            revealOnScroll();
            window.addEventListener('scroll', revealOnScroll);
        });
    </script>
</body>
</html>
