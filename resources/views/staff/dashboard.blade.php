<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - UTHM Bulletin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'uthm-blue': '#0056a6',
                        'uthm-blue-light': '#e6f0fa',
                        'uthm-green': '#6ea342',
                        'uthm-yellow': '#ffc107',
                        'uthm-red': '#dc3545',
                        'uthm-purple': '#6f42c1',
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom sidebar styles */
        :root {
            --sidebar-collapsed: 80px;
            --sidebar-expanded: 280px;
            --transition-speed: 0.3s;
        }

        .sidebar-collapsed {
            width: var(--sidebar-collapsed) !important;
        }
        
        .sidebar-expanded {
            width: var(--sidebar-expanded) !important;
        }
        
        .content-collapsed {
            margin-left: var(--sidebar-collapsed) !important;
        }
        
        .content-expanded {
            margin-left: var(--sidebar-expanded) !important;
        }
        
        /* Smooth transitions */
        .sidebar-transition {
            transition: width var(--transition-speed) ease;
        }
        
        .content-transition {
            transition: margin-left var(--transition-speed) ease;
        }
        
        /* Text visibility control */
        .sidebar-text {
            transition: all var(--transition-speed) ease;
            overflow: hidden;
            white-space: nowrap;
        }
        
        .sidebar-collapsed .sidebar-text {
            opacity: 0;
            width: 0;
            margin-left: 0 !important;
        }
        
        .sidebar-expanded .sidebar-text {
            opacity: 1;
            width: auto;
            margin-left: 0.75rem !important;
        }
        
        /* Mobile styles */
        @media (max-width: 768px) {
            .sidebar-collapsed,
            .sidebar-expanded {
                width: 280px !important;
                transform: translateX(-100%);
            }
            
            .sidebar-expanded.mobile-open {
                transform: translateX(0);
            }
            
            .content-collapsed,
            .content-expanded {
                margin-left: 0 !important;
            }
        }

        /* Dashboard custom styles */
        .stats-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .announcement-card {
            border-left: 4px solid;
        }
        
        .urgent {
            border-left-color: #dc3545;
        }
        
        .important {
            border-left-color: #ffc107;
        }
        
        .normal {
            border-left-color: #0056a6;
        }
        
        .event-card {
            transition: all 0.3s ease;
        }
        
        .event-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Mobile Menu Button -->
    <div class="md:hidden fixed top-4 left-4 z-50">
        <button id="mobile-menu-toggle" class="bg-uthm-blue text-white p-2 rounded-lg shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <!-- Sidebar Dashboard Navigation - MATCHING MAIN DASHBOARD STRUCTURE -->
    <div id="sidebar" class="sidebar-collapsed bg-white shadow-lg h-screen fixed left-0 top-0 overflow-y-auto z-40 sidebar-transition">
        <!-- Sidebar Header -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="bg-green-600 text-white p-2 rounded-lg shrink-0">
                        <i class="fas fa-user-graduate text-lg"></i>
                    </div>
                    <div class="sidebar-text">
                        <h2 class="font-bold text-gray-900">UTHM Bulletin</h2>
                        <p class="text-xs text-gray-500">Student Dashboard</p>
                    </div>
                </div>
                
                <!-- Toggle Button -->
                <button id="sidebar-toggle" class="hidden md:block text-gray-500 hover:text-uthm-blue shrink-0">
                    <svg id="toggle-icon" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                    </svg>
                </button>
            </div>
        </div>

                           <!-- User Profile - Now Clickable -->
<a href="{{ route('profile') }}" class="block hover:bg-gray-50 transition-colors">
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-uthm-blue-light rounded-full flex items-center justify-center shrink-0">
                <span class="font-bold uthm-blue">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            </div>
            <div class="sidebar-text">
                <h3 class="font-medium text-gray-900">{{ $user->name }}</h3>
                <p class="text-xs text-gray-500">{{ $user->uthm_id ?? 'UTHM Member' }}</p>
            </div>
        </div>
    </div>
</a>

        <!-- Dashboard Navigation - EXACT SAME ORDER AS MAIN DASHBOARD -->
        <nav class="p-4">
            <ul class="space-y-2">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0">
                            <i class="fas fa-home w-5 h-5"></i>
                        </div>
                        <span class="sidebar-text ml-3">Dashboard</span>
                    </a>
                </li>

                <!-- Announcements -->
                <li>
                    <a href="{{ route('announcements.index') }}" 
                       class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0">
                            <i class="fas fa-bullhorn w-5 h-5"></i>
                        </div>
                        <span class="sidebar-text ml-3">Announcements</span>
                    </a>
                </li>

                <!-- My Announcements -->
                <li>
                    <a href="{{ route('announcements.my-announcements') }}" 
                       class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0">
                            <i class="fas fa-file-alt w-5 h-5"></i>
                        </div>
                        <span class="sidebar-text ml-3">My Announcements</span>
                    </a>
                </li>

                <!-- Calendar -->
                <li>
                    <a href="#" 
                       class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0">
                            <i class="fas fa-calendar-alt w-5 h-5"></i>
                        </div>
                        <span class="sidebar-text ml-3">Calendar</span>
                    </a>
                </li>

                <!-- Events -->
                <li>
                    <a href="#" 
                       class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0">
                            <i class="fas fa-calendar-check w-5 h-5"></i>
                        </div>
                        <span class="sidebar-text ml-3">Events</span>
                    </a>
                </li>

                

                <!-- Clubs -->
                <li>
                    <a href="#" 
                       class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0">
                            <i class="fas fa-users w-5 h-5"></i>
                        </div>
                        <span class="sidebar-text ml-3">Clubs</span>
                    </a>
                </li>

                <!-- Settings -->
                <li>
                    <a href="#" 
                       class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0">
                            <i class="fas fa-cog w-5 h-5"></i>
                        </div>
                        <span class="sidebar-text ml-3">Settings</span>
                    </a>
                </li>

                <!-- Logout - MOVED HERE TO MATCH MAIN DASHBOARD -->
                <li class="pt-4 border-t border-gray-200">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="flex items-center p-3 rounded-lg hover:bg-red-50 text-red-600 w-full transition-colors">
                            <div class="shrink-0">
                                <i class="fas fa-sign-out-alt w-5 h-5"></i>
                            </div>
                            <span class="sidebar-text ml-3">Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <!-- REMOVED: Separate Logout section at bottom since it's now in the main nav -->
    </div>

    <!-- Main Content -->
    <div id="main-content" class="content-collapsed min-h-screen content-transition">
        <!-- Top Navigation Bar -->
        <nav class="bg-white shadow">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Breadcrumb -->
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">Student Dashboard</h1>
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-600">Overview</span>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Notification Bell -->
                        <button class="relative p-2 text-gray-600 hover:text-uthm-blue">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        
                        <!-- User Menu -->
                        <div class="relative">
                            <button id="user-menu-button" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="font-bold text-green-700">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->uthm_id }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> My Profile
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i> Settings
                                </a>
                                <div class="border-t border-gray-200 my-2"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Dashboard Content -->
        <div class="py-8">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Welcome Section -->
                <div class="bg-white rounded-xl shadow p-6 mb-8">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome back, {{ Auth::user()->name }}! 👋</h2>
                            <p class="text-gray-600">Here's your student dashboard overview for today.</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <div class="flex items-center space-x-4 text-sm">
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full">
                                    <i class="fas fa-graduation-cap mr-1"></i> Active Student
                                </span>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                                    <i class="fas fa-calendar mr-1"></i> Semester 1, 2024
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Staff Info Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                        <div class="stats-card bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-3 rounded-lg mr-3">
                                    <i class="fas fa-id-card text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">UTHM ID</p>
                                    <p class="font-bold text-gray-900">{{ Auth::user()->uthm_id }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stats-card bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-3 rounded-lg mr-3">
                                    <i class="fas fa-university text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Faculty</p>
                                    <p class="font-bold text-gray-900">{{ Auth::user()->faculty ?? 'Not specified' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stats-card bg-purple-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-3 rounded-lg mr-3">
                                    <i class="fas fa-envelope text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Email</p>
                                    <p class="font-bold text-gray-900">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stats-card bg-yellow-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="bg-yellow-100 p-3 rounded-lg mr-3">
                                    <i class="fas fa-book text-yellow-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Enrolled Courses</p>
                                    <p class="font-bold text-gray-900">6 Courses</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Today's Announcements -->
                    <div class="lg:col-span-2">
                        <!-- Section Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-2 sm:mb-0">Today's Announcements</h3>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 bg-uthm-blue text-white rounded text-sm hover:bg-blue-700 transition">
                                    All
                                </button>
                                <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200 transition">
                                    My Faculty
                                </button>
                                <button class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200 transition">
                                    University
                                </button>
                            </div>
                        </div>

                        <!-- Announcements List -->
                        <div class="space-y-4">
                            <div class="announcement-card bg-white rounded-lg shadow p-6 important">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h4 class="font-semibold text-gray-900 text-lg">FYP Submission Deadline Extended</h4>
                                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Important</span>
                                        </div>
                                        <p class="text-gray-600 text-sm mb-3">Final Year Project submission deadline extended to December 20, 2024. Please ensure all submissions are complete.</p>
                                        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500">
                                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded">
                                                <i class="fas fa-university mr-1"></i> Faculty of Computer Science
                                            </span>
                                            <span><i class="far fa-clock mr-1"></i> 2 hours ago</span>
                                            <span><i class="far fa-eye mr-1"></i> 342 views</span>
                                            <span><i class="far fa-comment mr-1"></i> 24 comments</span>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800 p-2">
                                            <i class="far fa-calendar-plus"></i>
                                        </button>
                                        <button class="text-gray-400 hover:text-gray-600 p-2">
                                            <i class="far fa-bookmark"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="announcement-card bg-white rounded-lg shadow p-6 normal">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h4 class="font-semibold text-gray-900 text-lg">Sports Day Registration Open</h4>
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Event</span>
                                        </div>
                                        <p class="text-gray-600 text-sm mb-3">Annual UTHM Sports Day registration is now open. Click here to register your team for various sports competitions.</p>
                                        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500">
                                            <span class="px-2 py-1 bg-green-50 text-green-700 rounded">
                                                <i class="fas fa-running mr-1"></i> Student Affairs Department
                                            </span>
                                            <span><i class="far fa-clock mr-1"></i> 1 day ago</span>
                                            <span><i class="far fa-eye mr-1"></i> 521 views</span>
                                            <span><i class="far fa-comment mr-1"></i> 18 comments</span>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex space-x-2">
                                        <button class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition">
                                            <i class="fas fa-external-link-alt mr-1"></i> Register
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- More announcements can be added here -->
                            <div class="text-center mt-4">
                                <a href="{{ route('announcements.index') }}" class="text-uthm-blue hover:text-blue-700 font-medium">
                                    <i class="fas fa-arrow-right mr-1"></i> View All Announcements
                                </a>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-center">
                                    <div class="bg-uthm-blue-light p-3 rounded-lg mr-3">
                                        <i class="fas fa-fire text-uthm-blue"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Trending Announcement</p>
                                        <p class="font-bold text-gray-900">"Campus Fest 2024"</p>
                                        <p class="text-xs text-gray-500">862 views</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-center">
                                    <div class="bg-green-50 p-3 rounded-lg mr-3">
                                        <i class="fas fa-chart-line text-uthm-green"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Your Faculty Updates</p>
                                        <p class="font-bold text-gray-900">12 New Posts</p>
                                        <p class="text-xs text-gray-500">This week</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-center">
                                    <div class="bg-yellow-50 p-3 rounded-lg mr-3">
                                        <i class="fas fa-bell text-uthm-yellow"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Unread Announcements</p>
                                        <p class="font-bold text-gray-900">5 New</p>
                                        <p class="text-xs text-gray-500">2 Urgent</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Upcoming Events & Quick Links -->
                    <div class="space-y-8">
                        <!-- Upcoming Events -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-gray-900">Upcoming Events</h3>
                                <a href="#" class="text-uthm-blue hover:text-blue-700 text-sm font-medium">
                                    View Calendar <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                            <div class="space-y-4">
                                <div class="event-card flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition">
                                    <div class="bg-yellow-100 p-3 rounded-lg mr-4">
                                        <div class="text-center">
                                            <div class="font-bold text-lg">15</div>
                                            <div class="text-xs uppercase">DEC</div>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-900">Career Workshop: Resume Building</h4>
                                        <p class="text-sm text-gray-600">
                                            <i class="far fa-clock mr-1"></i> 2:00 PM • 
                                            <i class="fas fa-map-marker-alt mr-1 ml-2"></i> Main Hall
                                        </p>
                                        <div class="flex items-center mt-2">
                                            <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded">Career</span>
                                        </div>
                                    </div>
                                    <button class="bg-uthm-blue text-white px-3 py-2 rounded text-sm hover:bg-blue-700 transition">
                                        Attend
                                    </button>
                                </div>
                                
                                <div class="event-card flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                                    <div class="bg-purple-100 p-3 rounded-lg mr-4">
                                        <div class="text-center">
                                            <div class="font-bold text-lg">20</div>
                                            <div class="text-xs uppercase">DEC</div>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-900">Final Year Project Submission</h4>
                                        <p class="text-sm text-gray-600">
                                            <i class="far fa-clock mr-1"></i> All Day • 
                                            <i class="fas fa-map-marker-alt mr-1 ml-2"></i> Faculty Offices
                                        </p>
                                        <div class="flex items-center mt-2">
                                            <span class="px-2 py-1 bg-red-50 text-red-700 text-xs rounded">Deadline</span>
                                        </div>
                                    </div>
                                    <button class="bg-uthm-blue text-white px-3 py-2 rounded text-sm hover:bg-blue-700 transition">
                                        <i class="far fa-calendar-plus mr-1"></i> Add
                                    </button>
                                </div>
                                
                                <div class="event-card flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                                    <div class="bg-green-100 p-3 rounded-lg mr-4">
                                        <div class="text-center">
                                            <div class="font-bold text-lg">25</div>
                                            <div class="text-xs uppercase">DEC</div>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-900">Christmas Celebration</h4>
                                        <p class="text-sm text-gray-600">
                                            <i class="far fa-clock mr-1"></i> 6:00 PM • 
                                            <i class="fas fa-map-marker-alt mr-1 ml-2"></i> Student Center
                                        </p>
                                        <div class="flex items-center mt-2">
                                            <span class="px-2 py-1 bg-green-50 text-green-700 text-xs rounded">Social</span>
                                        </div>
                                    </div>
                                    <button class="bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700 transition">
                                        Join
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Links -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Links</h3>
                            <div class="grid grid-cols-2 gap-3">
                                <a href="#" class="bg-blue-50 hover:bg-blue-100 rounded-lg p-4 text-center transition-colors">
                                    <div class="bg-blue-100 p-3 rounded-lg mb-2 mx-auto w-fit">
                                        <i class="fas fa-graduation-cap text-blue-600"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">Academic Portal</p>
                                </a>
                                <a href="#" class="bg-green-50 hover:bg-green-100 rounded-lg p-4 text-center transition-colors">
                                    <div class="bg-green-100 p-3 rounded-lg mb-2 mx-auto w-fit">
                                        <i class="fas fa-book text-green-600"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">E-Library</p>
                                </a>
                                <a href="#" class="bg-yellow-50 hover:bg-yellow-100 rounded-lg p-4 text-center transition-colors">
                                    <div class="bg-yellow-100 p-3 rounded-lg mb-2 mx-auto w-fit">
                                        <i class="fas fa-file-alt text-yellow-600"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">Assignments</p>
                                </a>
                                <a href="#" class="bg-purple-50 hover:bg-purple-100 rounded-lg p-4 text-center transition-colors">
                                    <div class="bg-purple-100 p-3 rounded-lg mb-2 mx-auto w-fit">
                                        <i class="fas fa-users text-purple-600"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">Student Clubs</p>
                                </a>
                            </div>
                        </div>

                        <!-- Academic Summary -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Academic Summary</h3>
                            <div class="space-y-3">
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Current GPA</span>
                                        <span class="font-bold text-green-600">3.75</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: 88%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Attendance Rate</span>
                                        <span class="font-bold text-blue-600">94%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: 94%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Assignment Completion</span>
                                        <span class="font-bold text-purple-600">87%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: 87%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const toggleIcon = document.getElementById('toggle-icon');
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            
            // Load sidebar state from localStorage
            const isSidebarExpanded = localStorage.getItem('sidebarExpanded') === 'true';
            if (isSidebarExpanded) {
                expandSidebar();
            } else {
                collapseSidebar();
            }
            
            // Desktop sidebar toggle
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (sidebar.classList.contains('sidebar-expanded')) {
                        collapseSidebar();
                    } else {
                        expandSidebar();
                    }
                });
            }
            
            // Mobile menu toggle
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    if (sidebar.classList.contains('mobile-open')) {
                        sidebar.classList.remove('mobile-open');
                    } else {
                        sidebar.classList.add('mobile-open');
                    }
                });
            }
            
            // User menu toggle
            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });
                
                // Close user menu when clicking outside
                document.addEventListener('click', function() {
                    userMenu.classList.add('hidden');
                });
            }
            
            // Close mobile sidebar when clicking on links
            if (window.innerWidth < 768) {
                document.querySelectorAll('#sidebar a').forEach(link => {
                    link.addEventListener('click', function() {
                        sidebar.classList.remove('mobile-open');
                    });
                });
            }
            
            function expandSidebar() {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                mainContent.classList.remove('content-collapsed');
                mainContent.classList.add('content-expanded');
                
                if (toggleIcon) {
                    toggleIcon.style.transform = 'rotate(180deg)';
                }
                
                localStorage.setItem('sidebarExpanded', 'true');
            }
            
            function collapseSidebar() {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
                mainContent.classList.remove('content-expanded');
                mainContent.classList.add('content-collapsed');
                
                if (toggleIcon) {
                    toggleIcon.style.transform = 'rotate(0deg)';
                }
                
                localStorage.setItem('sidebarExpanded', 'false');
            }
            
            // Responsive behavior
            window.addEventListener('resize', function() {
                if (window.innerWidth < 768) {
                    if (!sidebar.classList.contains('mobile-open')) {
                        sidebar.style.transform = 'translateX(-100%)';
                    }
                } else {
                    sidebar.style.transform = 'translateX(0)';
                }
            });
            
            // Initialize mobile state
            if (window.innerWidth < 768) {
                sidebar.style.transform = 'translateX(-100%)';
            }
        });
    </script>
</body>
</html>