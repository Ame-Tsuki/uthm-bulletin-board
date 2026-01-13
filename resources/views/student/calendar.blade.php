<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - UTHM Bulletin</title>
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
        /* Custom sidebar styles - Same as dashboard */
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

        /* Calendar custom styles */
        .calendar-day {
            min-height: 120px;
            transition: all 0.2s ease;
        }
        
        .calendar-day:hover {
            background-color: #f9fafb;
        }
        
        .calendar-day.today {
            background-color: #e6f0fa;
            border: 2px solid #0056a6;
        }
        
        .calendar-day.other-month {
            background-color: #f8f9fa;
            color: #6c757d;
        }
        
        .event-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 4px;
        }
        
        .event-lecture {
            background-color: #0056a6;
        }
        
        .event-deadline {
            background-color: #dc3545;
        }
        
        .event-exam {
            background-color: #6f42c1;
        }
        
        .event-social {
            background-color: #6ea342;
        }
        
        .event-workshop {
            background-color: #ffc107;
        }
        
        /* Smooth calendar transitions */
        .calendar-transition {
            transition: all 0.3s ease;
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

    <!-- Sidebar Dashboard Navigation - EXACT SAME STRUCTURE -->
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

        <!-- User Profile -->
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

                <!-- Calendar (Active) -->
                <li>
                    <a href="#" 
                       class="flex items-center p-3 rounded-lg bg-uthm-blue-light text-uthm-blue transition-colors">
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
                        <h1 class="text-xl font-bold text-gray-900">Student Calendar</h1>
                        <span class="mx-2 text-gray-400">/</span>
                        <span id="current-month-year" class="text-gray-600">December 2024</span>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Add Event Button -->
                        <button onclick="openEventModal()" class="bg-uthm-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            <span class="hidden md:inline">Add Event</span>
                        </button>
                        
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
                            
                            <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden">
                                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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

        <!-- Main Calendar Content -->
        <div class="py-8">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Calendar Controls -->
                <div class="bg-white rounded-xl shadow p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div class="mb-4 md:mb-0">
                            <h2 class="text-2xl font-bold text-gray-900">Academic Calendar</h2>
                            <p class="text-gray-600">Track your lectures, deadlines, and events</p>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <!-- View Toggle -->
                            <div class="flex bg-gray-100 p-1 rounded-lg">
                                <button id="month-view" class="px-3 py-1 rounded text-sm font-medium bg-white shadow text-uthm-blue">
                                    Month
                                </button>
                                <button id="week-view" class="px-3 py-1 rounded text-sm font-medium text-gray-600 hover:text-gray-900">
                                    Week
                                </button>
                                <button id="day-view" class="px-3 py-1 rounded text-sm font-medium text-gray-600 hover:text-gray-900">
                                    Day
                                </button>
                            </div>
                            
                            <!-- Navigation -->
                            <div class="flex items-center space-x-2">
                                <button id="prev-month" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button id="today-btn" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">
                                    Today
                                </button>
                                <button id="next-month" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Event Type Filters -->
                    <div class="mt-6 flex flex-wrap gap-2">
                        <button class="event-filter active px-3 py-1 bg-uthm-blue text-white rounded-full text-sm hover:bg-blue-700 transition" data-type="all">
                            All Events
                        </button>
                        <button class="event-filter px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition" data-type="lecture">
                            <span class="event-dot event-lecture"></span> Lectures
                        </button>
                        <button class="event-filter px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition" data-type="deadline">
                            <span class="event-dot event-deadline"></span> Deadlines
                        </button>
                        <button class="event-filter px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition" data-type="exam">
                            <span class="event-dot event-exam"></span> Exams
                        </button>
                        <button class="event-filter px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition" data-type="social">
                            <span class="event-dot event-social"></span> Social Events
                        </button>
                        <button class="event-filter px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition" data-type="workshop">
                            <span class="event-dot event-workshop"></span> Workshops
                        </button>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <!-- Weekday Headers -->
                    <div class="grid grid-cols-7 bg-gray-50 border-b">
                        <div class="p-4 text-center font-medium text-gray-600">Sunday</div>
                        <div class="p-4 text-center font-medium text-gray-600">Monday</div>
                        <div class="p-4 text-center font-medium text-gray-600">Tuesday</div>
                        <div class="p-4 text-center font-medium text-gray-600">Wednesday</div>
                        <div class="p-4 text-center font-medium text-gray-600">Thursday</div>
                        <div class="p-4 text-center font-medium text-gray-600">Friday</div>
                        <div class="p-4 text-center font-medium text-gray-600">Saturday</div>
                    </div>

                    <!-- Calendar Days Grid -->
                    <div id="calendar-grid" class="grid grid-cols-7">
                        <!-- Days will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Upcoming Events Sidebar -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
                    <!-- Upcoming Events -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-gray-900">Upcoming Events This Week</h3>
                                <a href="#" class="text-uthm-blue hover:text-blue-700 text-sm font-medium">
                                    View All <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                            
                            <div id="upcoming-events" class="space-y-4">
                                <!-- Events will be populated by JavaScript -->
                            </div>
                        </div>

                        <!-- Calendar Statistics -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-3 rounded-lg mr-3">
                                        <i class="fas fa-chalkboard-teacher text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Lectures This Month</p>
                                        <p class="font-bold text-gray-900">18</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-center">
                                    <div class="bg-red-100 p-3 rounded-lg mr-3">
                                        <i class="fas fa-exclamation-circle text-red-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Upcoming Deadlines</p>
                                        <p class="font-bold text-gray-900">3</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-center">
                                    <div class="bg-purple-100 p-3 rounded-lg mr-3">
                                        <i class="fas fa-file-alt text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Exams Scheduled</p>
                                        <p class="font-bold text-gray-900">2</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions & Event Categories -->
                    <div class="space-y-6">
                        <!-- Quick Actions -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <button onclick="openEventModal()" class="w-full bg-uthm-blue text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                                    <i class="fas fa-plus mr-2"></i> Add New Event
                                </button>
                                <button onclick="exportCalendar()" class="w-full bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition flex items-center justify-center">
                                    <i class="fas fa-download mr-2"></i> Export Calendar
                                </button>
                                <button onclick="printCalendar()" class="w-full bg-gray-100 text-gray-700 px-4 py-3 rounded-lg hover:bg-gray-200 transition flex items-center justify-center">
                                    <i class="fas fa-print mr-2"></i> Print Schedule
                                </button>
                            </div>
                        </div>

                        <!-- Event Categories -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Event Categories</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="event-dot event-lecture mr-3"></span>
                                        <span class="font-medium">Lectures</span>
                                    </div>
                                    <span class="bg-white px-2 py-1 rounded text-sm">12</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="event-dot event-deadline mr-3"></span>
                                        <span class="font-medium">Deadlines</span>
                                    </div>
                                    <span class="bg-white px-2 py-1 rounded text-sm">5</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="event-dot event-exam mr-3"></span>
                                        <span class="font-medium">Exams</span>
                                    </div>
                                    <span class="bg-white px-2 py-1 rounded text-sm">3</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="event-dot event-social mr-3"></span>
                                        <span class="font-medium">Social Events</span>
                                    </div>
                                    <span class="bg-white px-2 py-1 rounded text-sm">8</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="event-dot event-workshop mr-3"></span>
                                        <span class="font-medium">Workshops</span>
                                    </div>
                                    <span class="bg-white px-2 py-1 rounded text-sm">4</span>
                                </div>
                            </div>
                        </div>

                        <!-- Calendar Sync -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Sync Calendar</h3>
                            <div class="space-y-3">
                                <button class="w-full bg-gray-800 text-white px-4 py-3 rounded-lg hover:bg-gray-900 transition flex items-center justify-center">
                                    <i class="fab fa-google mr-2"></i> Sync with Google Calendar
                                </button>
                                <button class="w-full bg-blue-500 text-white px-4 py-3 rounded-lg hover:bg-blue-600 transition flex items-center justify-center">
                                    <i class="fab fa-windows mr-2"></i> Sync with Outlook
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Modal -->
    <div id="event-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Add New Event</h3>
                    <button onclick="closeEventModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="event-form">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Title</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent" placeholder="Enter event title">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                                <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                                <input type="time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                                <input type="time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Type</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent">
                                <option value="lecture">Lecture</option>
                                <option value="deadline">Deadline</option>
                                <option value="exam">Exam</option>
                                <option value="social">Social Event</option>
                                <option value="workshop">Workshop</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent" placeholder="Enter location">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent" placeholder="Enter event description"></textarea>
                        </div>
                        
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-uthm-blue focus:ring-uthm-blue">
                                <span class="ml-2 text-sm text-gray-700">Set reminder notification</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeEventModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-uthm-blue text-white rounded-lg hover:bg-blue-700 transition">
                            Save Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize calendar
            const calendar = new Calendar();
            calendar.init();
            
            // Initialize sidebar (same as dashboard)
            initializeSidebar();
        });

        // Calendar Class
        class Calendar {
            constructor() {
                this.currentDate = new Date();
                this.events = [
                    { id: 1, title: 'Database Systems Lecture', date: new Date(2024, 11, 15), type: 'lecture', time: '10:00 AM', location: 'Room 101' },
                    { id: 2, title: 'FYP Submission Deadline', date: new Date(2024, 11, 20), type: 'deadline', time: '11:59 PM', location: 'Faculty Office' },
                    { id: 3, title: 'Career Workshop', date: new Date(2024, 11, 15), type: 'workshop', time: '2:00 PM', location: 'Main Hall' },
                    { id: 4, title: 'Software Engineering Exam', date: new Date(2024, 11, 18), type: 'exam', time: '9:00 AM', location: 'Exam Hall' },
                    { id: 5, title: 'Christmas Celebration', date: new Date(2024, 11, 25), type: 'social', time: '6:00 PM', location: 'Student Center' },
                    { id: 6, title: 'Web Development Lecture', date: new Date(2024, 11, 16), type: 'lecture', time: '2:00 PM', location: 'Room 203' },
                    { id: 7, title: 'AI Assignment Due', date: new Date(2024, 11, 22), type: 'deadline', time: '11:59 PM', location: 'Online' },
                ];
            }

            init() {
                this.renderCalendar();
                this.setupEventListeners();
                this.renderUpcomingEvents();
            }

            renderCalendar() {
                const monthYear = document.getElementById('current-month-year');
                const calendarGrid = document.getElementById('calendar-grid');
                
                // Update month/year display
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];
                monthYear.textContent = `${monthNames[this.currentDate.getMonth()]} ${this.currentDate.getFullYear()}`;
                
                // Clear calendar grid
                calendarGrid.innerHTML = '';
                
                // Get first day of month and total days
                const firstDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1);
                const lastDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0);
                const totalDays = lastDay.getDate();
                const startingDay = firstDay.getDay();
                
                // Add empty cells for previous month
                const prevMonthLastDay = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 0).getDate();
                for (let i = 0; i < startingDay; i++) {
                    const day = prevMonthLastDay - startingDay + i + 1;
                    const cell = this.createDayCell(day, 'other-month');
                    calendarGrid.appendChild(cell);
                }
                
                // Add current month days
                const today = new Date();
                for (let day = 1; day <= totalDays; day++) {
                    const cellDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), day);
                    const cell = this.createDayCell(day, cellDate.toDateString() === today.toDateString() ? 'today' : '');
                    
                    // Add events for this day
                    const dayEvents = this.events.filter(event => 
                        event.date.getDate() === day && 
                        event.date.getMonth() === this.currentDate.getMonth() &&
                        event.date.getFullYear() === this.currentDate.getFullYear()
                    );
                    
                    if (dayEvents.length > 0) {
                        const eventsContainer = document.createElement('div');
                        eventsContainer.className = 'mt-2 space-y-1';
                        
                        dayEvents.forEach(event => {
                            const eventEl = document.createElement('div');
                            eventEl.className = `text-xs p-1 rounded truncate cursor-pointer hover:opacity-90 ${this.getEventClass(event.type)}`;
                            eventEl.textContent = event.title;
                            eventEl.title = `${event.title}\n${event.time} - ${event.location}`;
                            eventsContainer.appendChild(eventEl);
                        });
                        
                        cell.querySelector('.day-events').appendChild(eventsContainer);
                    }
                    
                    calendarGrid.appendChild(cell);
                }
                
                // Add empty cells for next month
                const totalCells = 42; // 6 weeks * 7 days
                const remainingCells = totalCells - (startingDay + totalDays);
                for (let i = 1; i <= remainingCells; i++) {
                    const cell = this.createDayCell(i, 'other-month');
                    calendarGrid.appendChild(cell);
                }
            }

            createDayCell(dayNumber, additionalClasses = '') {
                const cell = document.createElement('div');
                cell.className = `calendar-day p-4 border border-gray-100 ${additionalClasses}`;
                
                cell.innerHTML = `
                    <div class="flex justify-between items-start">
                        <span class="font-medium text-gray-900">${dayNumber}</span>
                        <button class="text-gray-400 hover:text-uthm-blue text-sm">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="day-events mt-2"></div>
                `;
                
                return cell;
            }

            getEventClass(type) {
                const classes = {
                    'lecture': 'bg-blue-100 text-blue-800',
                    'deadline': 'bg-red-100 text-red-800',
                    'exam': 'bg-purple-100 text-purple-800',
                    'social': 'bg-green-100 text-green-800',
                    'workshop': 'bg-yellow-100 text-yellow-800'
                };
                return classes[type] || 'bg-gray-100 text-gray-800';
            }

            setupEventListeners() {
                // Month navigation
                document.getElementById('prev-month').addEventListener('click', () => {
                    this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                    this.renderCalendar();
                });
                
                document.getElementById('next-month').addEventListener('click', () => {
                    this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                    this.renderCalendar();
                });
                
                document.getElementById('today-btn').addEventListener('click', () => {
                    this.currentDate = new Date();
                    this.renderCalendar();
                });
                
                // View toggles
                document.getElementById('month-view').addEventListener('click', (e) => {
                    this.toggleView(e.target, 'month');
                });
                
                document.getElementById('week-view').addEventListener('click', (e) => {
                    this.toggleView(e.target, 'week');
                });
                
                document.getElementById('day-view').addEventListener('click', (e) => {
                    this.toggleView(e.target, 'day');
                });
                
                // Event filters
                document.querySelectorAll('.event-filter').forEach(button => {
                    button.addEventListener('click', (e) => {
                        document.querySelectorAll('.event-filter').forEach(btn => {
                            btn.classList.remove('active', 'bg-uthm-blue', 'text-white');
                            btn.classList.add('bg-gray-100', 'text-gray-700');
                        });
                        
                        e.target.classList.add('active', 'bg-uthm-blue', 'text-white');
                        e.target.classList.remove('bg-gray-100', 'text-gray-700');
                        
                        const type = e.target.dataset.type;
                        this.filterEvents(type);
                    });
                });
            }

            toggleView(button, view) {
                // Update button states
                document.querySelectorAll('#month-view, #week-view, #day-view').forEach(btn => {
                    btn.classList.remove('bg-white', 'shadow', 'text-uthm-blue');
                    btn.classList.add('text-gray-600');
                });
                
                button.classList.add('bg-white', 'shadow', 'text-uthm-blue');
                button.classList.remove('text-gray-600');
                
                // TODO: Implement different view renderings
                console.log(`Switched to ${view} view`);
            }

            filterEvents(type) {
                // TODO: Implement event filtering logic
                console.log(`Filtering events by type: ${type}`);
            }

            renderUpcomingEvents() {
                const upcomingEvents = document.getElementById('upcoming-events');
                upcomingEvents.innerHTML = '';
                
                // Get events for the next 7 days
                const nextWeek = new Date();
                nextWeek.setDate(nextWeek.getDate() + 7);
                
                const upcoming = this.events
                    .filter(event => event.date > new Date() && event.date <= nextWeek)
                    .sort((a, b) => a.date - b.date)
                    .slice(0, 5);
                
                if (upcoming.length === 0) {
                    upcomingEvents.innerHTML = `
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-calendar-times text-3xl mb-2"></i>
                            <p>No upcoming events for this week</p>
                        </div>
                    `;
                    return;
                }
                
                upcoming.forEach(event => {
                    const eventEl = document.createElement('div');
                    eventEl.className = 'flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition';
                    
                    const dateStr = event.date.toLocaleDateString('en-US', { 
                        weekday: 'short', 
                        month: 'short', 
                        day: 'numeric' 
                    });
                    
                    eventEl.innerHTML = `
                        <div class="mr-4">
                            <div class="text-center">
                                <div class="font-bold text-lg">${event.date.getDate()}</div>
                                <div class="text-xs uppercase text-gray-500">${event.date.toLocaleDateString('en-US', { month: 'short' })}</div>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900">${event.title}</h4>
                            <p class="text-sm text-gray-600">
                                <i class="far fa-clock mr-1"></i> ${event.time} • 
                                <i class="fas fa-map-marker-alt mr-1 ml-2"></i> ${event.location}
                            </p>
                            <div class="flex items-center mt-2">
                                <span class="px-2 py-1 ${this.getEventClass(event.type)} text-xs rounded">${event.type.charAt(0).toUpperCase() + event.type.slice(1)}</span>
                            </div>
                        </div>
                        <button class="ml-4 text-gray-400 hover:text-uthm-blue">
                            <i class="far fa-calendar-plus"></i>
                        </button>
                    `;
                    
                    upcomingEvents.appendChild(eventEl);
                });
            }
        }

        // Modal Functions
        function openEventModal() {
            document.getElementById('event-modal').classList.remove('hidden');
        }

        function closeEventModal() {
            document.getElementById('event-modal').classList.add('hidden');
        }

        // Export/Print Functions
        function exportCalendar() {
            alert('Calendar export feature coming soon!');
        }

        function printCalendar() {
            window.print();
        }

        // Sidebar Initialization (same as dashboard)
        function initializeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const toggleIcon = document.getElementById('toggle-icon');
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            
            // Load sidebar state
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
        }

        // Handle form submission
        document.getElementById('event-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Event added successfully!');
            closeEventModal();
            // Here you would typically send the data to your backend
        });
    </script>
</body>
</html>