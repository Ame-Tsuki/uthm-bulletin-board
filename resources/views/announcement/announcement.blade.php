<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - UTHM Bulletin Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        /* Announcement styles */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .badge-urgent {
            background-color: #fee2e2;
            color: #dc2626;
        }
        .badge-important {
            background-color: #fef3c7;
            color: #d97706;
        }
        .badge-academic {
            background-color: #dbeafe;
            color: #1d4ed8;
        }
        .badge-events {
            background-color: #f3e8ff;
            color: #7c3aed;
        }
        .badge-general {
            background-color: #f0f9ff;
            color: #0369a1;
        }
        
        /* Custom colors */
        .uthm-blue { color: #0056a6; }
        .bg-uthm-blue { background-color: #0056a6; }
        .bg-uthm-blue-light { background-color: #e6f0fa; }
        .uthm-green { color: #6ea342; }
        .uthm-yellow { color: #ffc107; }
        .uthm-red { color: #dc3545; }

        /* Floating button */
        .floating-add-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 86, 166, 0.3);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 86, 166, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(0, 86, 166, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0, 86, 166, 0);
            }
        }
        
        /* Mobile floating button */
        @media (max-width: 768px) {
            .floating-add-btn {
                bottom: 5rem;
                right: 1.5rem;
            }
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
                        <p class="text-xs text-gray-500">Announcements</p>
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

        <!-- Dashboard Navigation -->
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

                <!-- Announcements (Active) -->
                <li>
                    <a href="{{ route('announcements.index') }}" 
                       class="flex items-center p-3 rounded-lg bg-uthm-blue-light text-uthm-blue">
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
                        <h1 class="text-xl font-bold text-gray-900">Announcements</h1>
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-600">All Announcements</span>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Quick Create Dropdown -->
                        @if(in_array($user->role, ['admin', 'staff']))
                        <div class="relative hidden md:block">
                            <button id="quick-create-button" class="bg-uthm-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                                <i class="fas fa-plus mr-2"></i>
                                Create Announcement
                            </button>
                            <div id="quick-create-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden">
                                <!-- ADD THIS: Direct link to create announcement -->
                                <a href="{{ route('announcements.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-bullhorn mr-2"></i> New Announcement
                                </a>
                                <!-- End of addition -->
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
                                    <span class="font-bold uthm-blue">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->uthm_id ?? 'UTHM Member' }}</p>
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

        <!-- Main Content -->
        <div class="py-8">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Announcements</h2>
                            <p class="mt-2 text-gray-600">Stay updated with the latest news and announcements from UTHM</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                    <!-- Add Post Button -->
                        <a href="{{ route('announcements.create') }}" 
                        class="inline-flex items-center px-5 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors shadow-md">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Add New Post
                        </a>
                </div>
                    </div>
                </div>

                <!-- ADD THIS: Create Announcement Card (for admin/staff only) -->
                @if(in_array($user->role, ['admin']))
                <div class="mb-8 bg-gradient-to-r from-blue-50 to-uthm-blue-light border border-blue-200 rounded-xl shadow-sm p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-uthm-blue text-white p-3 rounded-lg mr-4">
                                <i class="fas fa-bullhorn text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">Ready to share an announcement?</h3>
                                <p class="text-gray-600 text-sm mt-1">Create a new announcement to inform students and staff about important updates.</p>
                            </div>
                        </div>
                        <a href="{{ route('announcements.create') }}" 
                           class="inline-flex items-center px-5 py-3 bg-uthm-blue text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow">
                            <i class="fas fa-plus mr-2"></i>
                            Create Announcement
                        </a>
                    </div>
                </div>
                @endif

                <!-- Filters -->
                <div class="mb-8 bg-white rounded-xl shadow p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex flex-wrap gap-2">
                            <button onclick="filterAnnouncements('all')" 
                                    class="px-4 py-2 bg-uthm-blue text-white rounded-lg text-sm font-medium">
                                All Announcements
                            </button>
                            <button onclick="filterAnnouncements('urgent')" 
                                    class="px-4 py-2 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100">
                                <i class="fas fa-exclamation-circle mr-2"></i>Urgent
                            </button>
                            <button onclick="filterAnnouncements('academic')" 
                                    class="px-4 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100">
                                <i class="fas fa-graduation-cap mr-2"></i>Academic
                            </button>
                            <button onclick="filterAnnouncements('events')" 
                                    class="px-4 py-2 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-100">
                                <i class="fas fa-calendar-alt mr-2"></i>Events
                            </button>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" 
                                       placeholder="Search announcements..." 
                                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-64">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Demo Notice -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-blue-900">Demo Mode</h4>
                            <p class="text-blue-700 text-sm mt-1">This is a demonstration. When you create announcements in the admin panel, they will appear here.</p>
                        </div>
                    </div>
                </div>

                <!-- Announcements Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Announcement Card 1 -->
                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium badge-urgent">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Urgent
                                    </span>
                                    <span class="ml-2 px-3 py-1 rounded-full text-xs font-medium badge-general">
                                        General
                                    </span>
                                </div>
                                <span class="text-sm text-gray-500">
                                    <i class="far fa-clock mr-1"></i> Dec 19, 2023
                                </span>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-3">System Maintenance This Weekend</h3>
                            
                            <p class="text-gray-600 mb-4 line-clamp-2">
                                There will be a scheduled system maintenance on Saturday, December 23rd from 2:00 AM to 6:00 AM. All UTHM digital services will be temporarily unavailable during this period.
                            </p>
                            
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-user-tie mr-2"></i>
                                    <span>IT Department</span>
                                </div>
                                <a href="/announcement/1" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 font-medium rounded-lg hover:bg-blue-100 transition-colors">
                                    View Details
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Announcement Card 2 -->
                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium badge-important">
                                        <i class="fas fa-star mr-1"></i> Important
                                    </span>
                                    <span class="ml-2 px-3 py-1 rounded-full text-xs font-medium badge-academic">
                                        Academic
                                    </span>
                                </div>
                                <span class="text-sm text-gray-500">
                                    <i class="far fa-clock mr-1"></i> Dec 18, 2023
                                </span>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Final Exam Schedule Release</h3>
                            
                            <p class="text-gray-600 mb-4 line-clamp-2">
                                The final examination schedule for Semester 1, 2023/2024 has been published. Students can access their exam timetable through the Student Portal.
                            </p>
                            
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-user-tie mr-2"></i>
                                    <span>Academic Affairs Office</span>
                                </div>
                                <a href="/announcement/2" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 font-medium rounded-lg hover:bg-blue-100 transition-colors">
                                    View Details
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Announcement Card 3 -->
                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium badge-events">
                                        <i class="fas fa-calendar-alt mr-1"></i> Events
                                    </span>
                                </div>
                                <span class="text-sm text-gray-500">
                                    <i class="far fa-clock mr-1"></i> Dec 17, 2023
                                </span>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Career Fair 2024</h3>
                            
                            <p class="text-gray-600 mb-4 line-clamp-2">
                                UTHM Annual Career Fair will be held on January 15-17, 2024 at Dewan Sultan Ibrahim. Over 100 companies from various industries will participate.
                            </p>
                            
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-user-tie mr-2"></i>
                                    <span>Career Center</span>
                                </div>
                                <a href="/announcement/3" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 font-medium rounded-lg hover:bg-blue-100 transition-colors">
                                    View Details
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Announcement Card 4 -->
                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium badge-academic">
                                        Academic
                                    </span>
                                </div>
                                <span class="text-sm text-gray-500">
                                    <i class="far fa-clock mr-1"></i> Dec 16, 2023
                                </span>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Semester Break Activities</h3>
                            
                            <p class="text-gray-600 mb-4 line-clamp-2">
                                Various activities and workshops are planned for the semester break. Registration opens next week for all interested students.
                            </p>
                            
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-user-tie mr-2"></i>
                                    <span>Student Affairs</span>
                                </div>
                                <a href="/announcement/4" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 font-medium rounded-lg hover:bg-blue-100 transition-colors">
                                    View Details
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Database Announcements -->
                    @forelse($announcements as $announcement)
                        <div class="bg-white rounded-xl shadow hover:shadow-lg transition-shadow overflow-hidden border border-gray-200">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        @if($announcement->priority === 'urgent')
                                            <span class="px-3 py-1 rounded-full text-xs font-medium badge-urgent">
                                                <i class="fas fa-exclamation-circle mr-1"></i> Urgent
                                            </span>
                                        @elseif($announcement->priority === 'important')
                                            <span class="px-3 py-1 rounded-full text-xs font-medium badge-important">
                                                <i class="fas fa-star mr-1"></i> Important
                                            </span>
                                        @endif
                                        
                                        <span class="ml-2 px-3 py-1 rounded-full text-xs font-medium badge-{{ $announcement->category }}">
                                            {{ ucfirst($announcement->category) }}
                                        </span>
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        <i class="far fa-clock mr-1"></i> 
                                        {{ $announcement->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                                
                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $announcement->title }}</h3>
                                
                                <p class="text-gray-600 mb-4 line-clamp-2">
                                    {{ Str::limit($announcement->content, 150) }}
                                </p>
                                
                                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-user-tie mr-2"></i>
                                        <span>{{ $announcement->author->name ?? 'Admin' }}</span>
                                    </div>
                                    <a href="/announcement/{{ $announcement->id }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 font-medium rounded-lg hover:bg-blue-100 transition-colors">
                                        View Details
                                        <i class="fas fa-arrow-right ml-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <!-- Empty state will be shown only if no database announcements -->
                    @endforelse
                </div>

                <!-- No Announcements Message -->
                @if($announcements->count() == 0)
                    <div class="text-center py-12">
                        <div class="inline-block p-6 bg-gray-100 rounded-full mb-4">
                            <i class="fas fa-bullhorn text-gray-400 text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">No announcements yet</h3>
                        <p class="text-gray-600 mb-6">When announcements are created, they will appear here.</p>
                        <p class="text-sm text-gray-500">Demo announcements are shown above for reference.</p>
                        <!-- ADD THIS: Add button to create first announcement -->
                        @if(in_array($user->role, ['admin', 'staff']))
                        <div class="mt-8">
                            <a href="{{ route('announcements.create') }}" 
                               class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors shadow-lg">
                                <i class="fas fa-plus-circle mr-2 text-lg"></i>
                                Create Your First Announcement
                            </a>
                        </div>
                        @endif
                    </div>
                @endif

                <!-- Pagination -->
                @if($announcements->hasPages())
                    <div class="mt-8">
                        <div class="bg-white rounded-xl shadow p-4">
                            {{ $announcements->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-6">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-500 text-sm">
                    <p>UTHM Digital Bulletin Board &copy; {{ date('Y') }}. All rights reserved.</p>
                    <p class="mt-1">For issues or inquiries, contact: <a href="mailto:support@uthm.edu.my" class="text-blue-600 hover:text-blue-800">support@uthm.edu.my</a></p>
                </div>
            </div>
        </footer>

        <!-- ADD THIS: Floating "Add Post" Button -->
        @if(in_array($user->role, ['admin', 'staff']))
        <a href="{{ route('announcements.create') }}" 
           class="floating-add-btn bg-green-600 text-white p-4 rounded-full shadow-lg hover:bg-green-700 transition-colors hover:shadow-xl">
            <i class="fas fa-plus text-2xl"></i>
        </a>
        @endif
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
                
                // Fix the create announcement link in dropdown
                const createAnnouncementLink = quickCreateMenu.querySelector('a[href*="announcements.create"]');
                if (createAnnouncementLink) {
                    createAnnouncementLink.addEventListener('click', function(e) {
                        e.stopPropagation();
                        window.location.href = this.href;
                    });
                }
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
            
            // Filter announcements
            window.filterAnnouncements = function(category) {
                alert('Filter by: ' + category + '\n\nThis feature will be implemented with real data.');
            }

            // Search functionality
            document.querySelector('input[type="text"]').addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    const searchTerm = this.value.toLowerCase();
                    alert('Search for: ' + searchTerm + '\n\nSearch functionality will be implemented with real data.');
                }
            });
            
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