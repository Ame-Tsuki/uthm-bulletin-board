<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - UTHM Bulletin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
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

    <!-- Sidebar Dashboard Navigation -->
    <div id="sidebar" class="sidebar-collapsed bg-white shadow-lg h-screen fixed left-0 top-0 overflow-y-auto z-40 sidebar-transition">
        <!-- Sidebar Header -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="bg-uthm-blue text-white p-2 rounded-lg shrink-0">
                        <i class="fas fa-bullhorn text-lg"></i>
                    </div>
                    <div class="sidebar-text">
                        <h2 class="font-bold uthm-blue">UTHM Bulletin</h2>
                        <p class="text-xs text-gray-500">Dashboard</p>
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

        <!-- User Profile -->
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

        <!-- Dashboard Navigation -->
        <nav class="p-4">
            <ul class="space-y-2">
                <!-- Dashboard (Active) -->
                <li>
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center p-3 rounded-lg bg-uthm-blue-light text-uthm-blue">
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

                <!-- Role-Based Navigation -->
                @if($user->role === 'admin')
                    <li class="pt-4">
                        <p class="sidebar-text text-xs text-gray-500 uppercase tracking-wider px-3">Admin</p>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center p-3 rounded-lg hover:bg-red-50 text-red-600 transition-colors mt-2">
                            <div class="shrink-0">
                                <i class="fas fa-cogs w-5 h-5"></i>
                            </div>
                            <span class="sidebar-text ml-3">Admin Panel</span>
                        </a>
                    </li>
                @elseif($user->role === 'staff')
                    <li>
                        <a href="{{ route('staff.dashboard') }}" 
                           class="flex items-center p-3 rounded-lg hover:bg-yellow-50 text-yellow-600 transition-colors">
                            <div class="shrink-0">
                                <i class="fas fa-user-tie w-5 h-5"></i>
                            </div>
                            <span class="sidebar-text ml-3">Staff Dashboard</span>
                        </a>
                    </li>
                @elseif($user->role === 'student')
                    <li>
                        <a href="{{ route('student.dashboard') }}" 
                           class="flex items-center p-3 rounded-lg hover:bg-green-50 text-green-600 transition-colors">
                            <div class="shrink-0">
                                <i class="fas fa-user-graduate w-5 h-5"></i>
                            </div>
                            <span class="sidebar-text ml-3">Student Dashboard</span>
                        </a>
                    </li>
                @endif


                <!-- Settings -->
                <li>
                    <a href="{{ route('settings') }}"
                       class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0">
                            <i class="fas fa-cog w-5 h-5"></i>
                        </div>
                        <span class="sidebar-text ml-3">Settings</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Logout -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
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
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="content-collapsed min-h-screen content-transition">
        <!-- Top Navigation Bar -->
        <nav class="bg-white shadow">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Breadcrumb -->
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">Dashboard</h1>
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-600">Overview</span>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Quick Create Dropdown -->
                        @if(in_array($user->role, ['admin', 'staff']))
                        <div class="relative hidden md:block">
                            <button id="quick-create-button" class="bg-uthm-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                                <i class="fas fa-plus mr-2"></i>
                                Quick Create
                            </button>
                            <div id="quick-create-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-bullhorn mr-2"></i> New Announcement
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-calendar-check mr-2"></i> New Event
                                </a>
                                @if($user->role === 'admin')
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-plus mr-2"></i> Add User
                                </a>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        <!-- Notification Bell -->
                        <button class="relative p-2 text-gray-600 hover:text-uthm-blue">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        
                        <!-- User Menu -->
                        <div class="relative">
                            <button id="user-menu-button" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100">
                                <div class="w-8 h-8 bg-uthm-blue-light rounded-full flex items-center justify-center">
                                    <span class="font-bold text-uthm-blue">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->uthm_id }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> My Profile
                                </a>
                                <a href="{{ route('settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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
                <!-- Welcome Message -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Welcome back, {{ $user->name }}!</h2>
                    <p class="text-gray-600 mt-1">Here's what's happening with UTHM Bulletin today.</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Announcements -->
                    <div class="stats-card bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Announcements</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">1,248</p>
                                <p class="text-sm text-green-600 mt-1">
                                    <i class="fas fa-arrow-up mr-1"></i> 12% from last month
                                </p>
                            </div>
                            <div class="bg-uthm-blue-light p-3 rounded-lg">
                                <i class="fas fa-bullhorn text-2xl text-uthm-blue"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Unread Announcements -->
                    <div class="stats-card bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Unread Announcements</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">12</p>
                                <p class="text-sm text-red-600 mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i> 3 urgent
                                </p>
                            </div>
                            <div class="bg-red-50 p-3 rounded-lg">
                                <i class="fas fa-envelope text-2xl text-red-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Events -->
                    <div class="stats-card bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Upcoming Events</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">8</p>
                                <p class="text-sm text-uthm-blue mt-1">
                                    <i class="fas fa-calendar-alt mr-1"></i> Next: Tomorrow
                                </p>
                            </div>
                            <div class="bg-green-50 p-3 rounded-lg">
                                <i class="fas fa-calendar-check text-2xl text-uthm-green"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Active Clubs -->
                    <div class="stats-card bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Active Clubs</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">24</p>
                                <p class="text-sm text-uthm-yellow mt-1">
                                    <i class="fas fa-users mr-1"></i> 5 new this month
                                </p>
                            </div>
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <i class="fas fa-users text-2xl text-uthm-yellow"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts and Announcements Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Recent Announcements -->
                    <div class="lg:col-span-2">
                        <!-- Section Header -->
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-900">Recent Announcements</h3>
                            <a href="{{ route('announcements.index') }}" class="text-uthm-blue hover:text-blue-700 text-sm font-medium">
                                View All <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>

                        <!-- Announcements List -->
                        <div class="space-y-4">
                            @for($i = 1; $i <= 5; $i++)
                            <div class="announcement-card bg-white rounded-lg shadow p-5 {{ $i == 1 ? 'urgent' : ($i == 2 ? 'important' : 'normal') }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h4 class="font-semibold text-gray-900">Semester {{ $i }} Registration Opens</h4>
                                            @if($i == 1)
                                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Urgent</span>
                                            @elseif($i == 2)
                                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Important</span>
                                            @endif
                                        </div>
                                        <p class="text-gray-600 text-sm mb-3">Registration for Semester {{ $i }} will begin next Monday. All students must complete registration by the deadline.</p>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded mr-3">Academic</span>
                                            <span class="mr-3"><i class="far fa-clock mr-1"></i> 2 hours ago</span>
                                            <span><i class="far fa-eye mr-1"></i> 245 views</span>
                                        </div>
                                    </div>
                                    <button class="text-gray-400 hover:text-gray-600 ml-2">
                                        <i class="far fa-bookmark"></i>
                                    </button>
                                </div>
                            </div>
                            @endfor
                        </div>

                        <!-- Quick Stats -->
                        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-center">
                                    <div class="bg-uthm-blue-light p-2 rounded-lg mr-3">
                                        <i class="fas fa-fire text-uthm-blue"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Trending</p>
                                        <p class="font-bold text-gray-900">"Campus Fest 2024"</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-center">
                                    <div class="bg-green-50 p-2 rounded-lg mr-3">
                                        <i class="fas fa-chart-line text-uthm-green"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Most Viewed</p>
                                        <p class="font-bold text-gray-900">"Scholarship Opportunities"</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-center">
                                    <div class="bg-yellow-50 p-2 rounded-lg mr-3">
                                        <i class="fas fa-comments text-uthm-yellow"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Most Comments</p>
                                        <p class="font-bold text-gray-900">"Library Renovation"</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Charts and Quick Links -->
                    <div class="space-y-8">
                        <!-- Announcement Categories Chart -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Announcement Categories</h3>
                            <div class="h-64">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>

                        <!-- Upcoming Events -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Upcoming Events</h3>
                                <a href="#" class="text-uthm-blue hover:text-blue-700 text-sm">View Calendar</a>
                            </div>
                            <div class="space-y-4">
                                @for($i = 1; $i <= 3; $i++)
                                <div class="flex items-center p-3 hover:bg-gray-50 rounded-lg">
                                    <div class="bg-uthm-blue-light p-2 rounded-lg mr-3">
                                        <i class="fas fa-calendar-day text-uthm-blue"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">Career Fair {{ date('Y') + $i }}</p>
                                        <p class="text-sm text-gray-600">Tomorrow, 9:00 AM - Main Hall</p>
                                    </div>
                                </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Quick Links -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Links</h3>
                            <div class="grid grid-cols-2 gap-3">
                                <a href="#" class="bg-uthm-blue-light hover:bg-blue-100 rounded-lg p-3 text-center transition-colors">
                                    <i class="fas fa-file-alt text-uthm-blue text-lg mb-2"></i>
                                    <p class="text-sm font-medium text-gray-900">Academic Calendar</p>
                                </a>
                                <a href="#" class="bg-green-50 hover:bg-green-100 rounded-lg p-3 text-center transition-colors">
                                    <i class="fas fa-graduation-cap text-uthm-green text-lg mb-2"></i>
                                    <p class="text-sm font-medium text-gray-900">Student Portal</p>
                                </a>
                                <a href="#" class="bg-yellow-50 hover:bg-yellow-100 rounded-lg p-3 text-center transition-colors">
                                    <i class="fas fa-book text-uthm-yellow text-lg mb-2"></i>
                                    <p class="text-sm font-medium text-gray-900">E-Library</p>
                                </a>
                                <a href="#" class="bg-red-50 hover:bg-red-100 rounded-lg p-3 text-center transition-colors">
                                    <i class="fas fa-heartbeat text-uthm-red text-lg mb-2"></i>
                                    <p class="text-sm font-medium text-gray-900">Health Center</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role-Specific Content -->
                @if($user->role === 'admin')
                <!-- Admin Specific Dashboard -->
                <div class="mt-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Admin Tools</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white rounded-xl shadow p-6">
                            <div class="flex items-center mb-4">
                                <div class="bg-red-50 p-3 rounded-lg mr-3">
                                    <i class="fas fa-user-shield text-red-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">User Management</p>
                                    <p class="text-sm text-gray-600">Manage system users</p>
                                </div>
                            </div>
                            <button class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition-colors">
                                Manage Users
                            </button>
                        </div>
                        <div class="bg-white rounded-xl shadow p-6">
                            <div class="flex items-center mb-4">
                                <div class="bg-blue-50 p-3 rounded-lg mr-3">
                                    <i class="fas fa-chart-bar text-uthm-blue text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">Analytics</p>
                                    <p class="text-sm text-gray-600">View detailed reports</p>
                                </div>
                            </div>
                            <button class="w-full bg-uthm-blue text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                View Analytics
                            </button>
                        </div>
                        <div class="bg-white rounded-xl shadow p-6">
                            <div class="flex items-center mb-4">
                                <div class="bg-green-50 p-3 rounded-lg mr-3">
                                    <i class="fas fa-cogs text-uthm-green text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">System Settings</p>
                                    <p class="text-sm text-gray-600">Configure system</p>
                                </div>
                            </div>
                            <button class="w-full bg-uthm-green text-white py-2 rounded-lg hover:bg-green-700 transition-colors">
                                Settings
                            </button>
                        </div>
                    </div>
                </div>
                @endif
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
            const quickCreateButton = document.getElementById('quick-create-button');
            const quickCreateMenu = document.getElementById('quick-create-menu');
            
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
            }
            
            // Quick create menu toggle
            if (quickCreateButton && quickCreateMenu) {
                quickCreateButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    quickCreateMenu.classList.toggle('hidden');
                });
            }
            
            // Close menus when clicking outside
            document.addEventListener('click', function() {
                if (userMenu) userMenu.classList.add('hidden');
                if (quickCreateMenu) quickCreateMenu.classList.add('hidden');
            });
            
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
            
            // Initialize Chart.js
            const categoryCtx = document.getElementById('categoryChart');
            if (categoryCtx) {
                new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Academic', 'Events', 'General', 'Urgent', 'Clubs'],
                        datasets: [{
                            data: [35, 25, 20, 10, 10],
                            backgroundColor: [
                                '#0056a6',
                                '#6ea342',
                                '#6c757d',
                                '#dc3545',
                                '#ffc107'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
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