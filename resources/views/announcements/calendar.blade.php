<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Calendar - UTHM Digital Bulletin Board</title>
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
        :root {
            --sidebar-collapsed: 80px;
            --sidebar-expanded: 280px;
            --transition-speed: 0.3s;
        }

        .sidebar-collapsed { width: var(--sidebar-collapsed) !important; }
        .sidebar-expanded { width: var(--sidebar-expanded) !important; }
        .content-collapsed { margin-left: var(--sidebar-collapsed) !important; }
        .content-expanded { margin-left: var(--sidebar-expanded) !important; }
        .sidebar-transition { transition: width var(--transition-speed) ease; }
        .content-transition { transition: margin-left var(--transition-speed) ease; }
        
        .sidebar-text {
            transition: all var(--transition-speed) ease;
            overflow: hidden;
            white-space: nowrap;
        }
        
        .sidebar-collapsed .sidebar-text { opacity: 0; width: 0; margin-left: 0 !important; }
        .sidebar-expanded .sidebar-text { opacity: 1; width: auto; margin-left: 0.75rem !important; }
        
        @media (max-width: 768px) {
            .sidebar-collapsed, .sidebar-expanded {
                width: 280px !important;
                transform: translateX(-100%);
            }
            .sidebar-expanded.mobile-open { transform: translateX(0); }
            .content-collapsed, .content-expanded { margin-left: 0 !important; }
        }

        .calendar-day {
            min-height: 120px;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .calendar-day:hover { background-color: #f9fafb; }
        .calendar-day.today { background-color: #e6f0fa; border: 2px solid #0056a6; }
        .calendar-day.other-month { background-color: #f8f9fa; color: #6c757d; }
        
        .event-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 4px;
        }
        
        .event-lecture { background-color: #0056a6; }
        .event-deadline { background-color: #dc3545; }
        .event-exam { background-color: #6f42c1; }
        .event-social { background-color: #6ea342; }
        .event-workshop { background-color: #ffc107; }
        .event-other { background-color: #6c757d; }
        .event-important { background-color: #8b5cf6; }
        
        .synced-event { position: relative; }
        .synced-event::after {
            content: '\f1a0';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 2px;
            right: 2px;
            font-size: 10px;
            color: #4285f4;
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #0056a6;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 24px;
            border-radius: 8px;
            color: white;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }
        .toast-success { background-color: #10b981; }
        .toast-error { background-color: #ef4444; }
        .toast-info { background-color: #3b82f6; }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
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

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar-collapsed bg-white shadow-lg h-screen fixed left-0 top-0 overflow-y-auto z-40 sidebar-transition">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="bg-green-600 text-white p-2 rounded-lg shrink-0">
                        <i class="fas fa-user-graduate text-lg"></i>
                    </div>
                    <div class="sidebar-text">
                        <h2 class="font-bold text-gray-900">UTHM Bulletin</h2>
                        <p class="text-xs text-gray-500">Student Dashboard</p>
                    </div>
                </div>
                <button id="sidebar-toggle" class="hidden md:block text-gray-500 hover:text-uthm-blue shrink-0">
                    <svg id="toggle-icon" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                    </svg>
                </button>
            </div>
        </div>

        <a href="{{ route('profile') }}" class="block hover:bg-gray-50 transition-colors">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-uthm-blue-light rounded-full flex items-center justify-center shrink-0">
                        <span class="font-bold text-uthm-blue">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
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
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0"><i class="fas fa-home w-5 h-5"></i></div>
                        <span class="sidebar-text ml-3">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('announcements.index') }}" class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0"><i class="fas fa-bullhorn w-5 h-5"></i></div>
                        <span class="sidebar-text ml-3">Announcements</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-3 rounded-lg bg-uthm-blue-light text-uthm-blue transition-colors">
                        <div class="shrink-0"><i class="fas fa-calendar-alt w-5 h-5"></i></div>
                        <span class="sidebar-text ml-3">Calendar</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0"><i class="fas fa-calendar-check w-5 h-5"></i></div>
                        <span class="sidebar-text ml-3">Events</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('settings') }}" class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0"><i class="fas fa-cog w-5 h-5"></i></div>
                        <span class="sidebar-text ml-3">Settings</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center p-3 rounded-lg hover:bg-red-50 text-red-600 w-full transition-colors">
                    <div class="shrink-0"><i class="fas fa-sign-out-alt w-5 h-5"></i></div>
                    <span class="sidebar-text ml-3">Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="content-collapsed min-h-screen content-transition">
        <nav class="bg-white shadow">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">Student Calendar</h1>
                        <span class="mx-2 text-gray-400">/</span>
                        <span id="current-month-year" class="text-gray-600">Loading...</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div id="google-sync-status" class="hidden md:flex items-center space-x-2 px-3 py-1 bg-gray-100 rounded-full">
                            <span id="sync-status-dot" class="w-2 h-2 bg-gray-400 rounded-full"></span>
                            <span id="sync-status-text" class="text-sm text-gray-600">Checking sync status...</span>
                        </div>
                        <button onclick="openEventModal()" class="bg-uthm-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            <span class="hidden md:inline">Add Event</span>
                        </button>
                        <button class="relative p-2 text-gray-600 hover:text-uthm-blue">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
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

        <!-- Calendar Content -->
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
                            <div class="flex bg-gray-100 p-1 rounded-lg">
                                <button id="month-view" class="px-3 py-1 rounded text-sm font-medium bg-white shadow text-uthm-blue">Month</button>
                                <button id="week-view" class="px-3 py-1 rounded text-sm font-medium text-gray-600 hover:text-gray-900">Week</button>
                                <button id="day-view" class="px-3 py-1 rounded text-sm font-medium text-gray-600 hover:text-gray-900">Day</button>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button id="prev-month" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button id="today-btn" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Today</button>
                                <button id="next-month" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex flex-wrap gap-2">
                        <button class="event-filter active px-3 py-1 bg-uthm-blue text-white rounded-full text-sm hover:bg-blue-700 transition" data-type="all">All Events</button>
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
                    <div class="grid grid-cols-7 bg-gray-50 border-b">
                        <div class="p-4 text-center font-medium text-gray-600">Sunday</div>
                        <div class="p-4 text-center font-medium text-gray-600">Monday</div>
                        <div class="p-4 text-center font-medium text-gray-600">Tuesday</div>
                        <div class="p-4 text-center font-medium text-gray-600">Wednesday</div>
                        <div class="p-4 text-center font-medium text-gray-600">Thursday</div>
                        <div class="p-4 text-center font-medium text-gray-600">Friday</div>
                        <div class="p-4 text-center font-medium text-gray-600">Saturday</div>
                    </div>
                    <div id="calendar-grid" class="grid grid-cols-7">
                        <div class="col-span-7 p-8 text-center">
                            <div class="loading-spinner mx-auto mb-2"></div>
                            <p class="text-gray-500">Loading calendar...</p>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Events & Sidebar -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-gray-900">Upcoming Events This Week</h3>
                                <a href="#" class="text-uthm-blue hover:text-blue-700 text-sm font-medium">
                                    View All <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                            <div id="upcoming-events" class="space-y-4">
                                <div class="text-center py-8 text-gray-500">
                                    <div class="loading-spinner mx-auto mb-2"></div>
                                    <p>Loading events...</p>
                                </div>
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
                                        <p id="stat-lectures" class="font-bold text-gray-900">-</p>
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
                                        <p id="stat-deadlines" class="font-bold text-gray-900">-</p>
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
                                        <p id="stat-exams" class="font-bold text-gray-900">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions & Sync -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-xl shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <button onclick="openEventModal()" class="w-full bg-uthm-blue text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                                    <i class="fas fa-plus mr-2"></i> Add New Event
                                </button>
                                <button onclick="syncWithGoogle()" id="sync-all-btn" class="w-full bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition flex items-center justify-center">
                                    <i class="fas fa-sync-alt mr-2"></i> Sync All to Google
                                </button>
                                <button onclick="window.print()" class="w-full bg-gray-100 text-gray-700 px-4 py-3 rounded-lg hover:bg-gray-200 transition flex items-center justify-center">
                                    <i class="fas fa-print mr-2"></i> Print Schedule
                                </button>
                            </div>
                        </div>

                        <!-- Calendar Sync -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Sync Calendar</h3>
                            <p id="google-status-text" class="text-sm text-gray-600 mb-4">
                                <i class="fas fa-circle text-gray-400 mr-1"></i> Checking status...
                            </p>
                            <div class="space-y-3">
                                <button id="connect-google-btn" 
                                        class="w-full bg-gray-800 text-white px-4 py-3 rounded-lg hover:bg-gray-900 transition flex items-center justify-center">
                                    <i class="fab fa-google mr-2"></i> Connect Google Calendar
                                </button>
                                <button id="disconnect-google-btn" 
                                        class="w-full bg-red-50 text-red-600 px-4 py-3 rounded-lg hover:bg-red-100 transition flex items-center justify-center text-sm hidden">
                                    <i class="fas fa-unlink mr-2"></i> Disconnect Google Calendar
                                </button>
                            </div>
                        </div>

                        <!-- Event Categories -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Event Categories</h3>
                            <div class="space-y-3" id="event-categories">
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="event-dot event-lecture mr-3"></span>
                                        <span class="font-medium">Lectures</span>
                                    </div>
                                    <span id="cat-lecture" class="bg-white px-2 py-1 rounded text-sm">0</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="event-dot event-deadline mr-3"></span>
                                        <span class="font-medium">Deadlines</span>
                                    </div>
                                    <span id="cat-deadline" class="bg-white px-2 py-1 rounded text-sm">0</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="event-dot event-exam mr-3"></span>
                                        <span class="font-medium">Exams</span>
                                    </div>
                                    <span id="cat-exam" class="bg-white px-2 py-1 rounded text-sm">0</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="event-dot event-social mr-3"></span>
                                        <span class="font-medium">Social Events</span>
                                    </div>
                                    <span id="cat-social" class="bg-white px-2 py-1 rounded text-sm">0</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="event-dot event-workshop mr-3"></span>
                                        <span class="font-medium">Workshops</span>
                                    </div>
                                    <span id="cat-workshop" class="bg-white px-2 py-1 rounded text-sm">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Modal -->
    <div id="event-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 id="modal-title" class="text-lg font-bold text-gray-900">Add New Event</h3>
                    <button onclick="closeEventModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="event-form">
                    <input type="hidden" id="event-id">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Title *</label>
                            <input type="text" id="event-title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent" placeholder="Enter event title">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                                <input type="date" id="event-start-date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                <input type="date" id="event-end-date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                                <input type="time" id="event-start-time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                                <input type="time" id="event-end-time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Type *</label>
                            <select id="event-type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent">
                                <option value="lecture">Lecture</option>
                                <option value="deadline">Deadline</option>
                                <option value="exam">Exam</option>
                                <option value="social">Social Event</option>
                                <option value="workshop">Workshop</option>
                                <option value="other">Other</option>
                                @if(Auth::user()->role === 'admin')
                                <option value="important">Important</option>
                                @endif
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input type="text" id="event-location" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent" placeholder="Enter location">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="event-description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent" placeholder="Enter event description"></textarea>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="checkbox" id="event-all-day" class="rounded border-gray-300 text-uthm-blue focus:ring-uthm-blue">
                                <span class="ml-2 text-sm text-gray-700">All Day Event</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" id="event-reminder" class="rounded border-gray-300 text-uthm-blue focus:ring-uthm-blue">
                                <span class="ml-2 text-sm text-gray-700">Set Reminder</span>
                            </label>
                            <label class="flex items-center" id="google-sync-option">
                                <input type="checkbox" id="event-sync-google" class="rounded border-gray-300 text-uthm-blue focus:ring-uthm-blue" checked>
                                <span class="ml-2 text-sm text-gray-700">Sync to Google</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeEventModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" id="event-submit-btn" class="px-4 py-2 bg-uthm-blue text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i> Save Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Event Detail Modal -->
    <div id="event-detail-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 id="detail-title" class="text-lg font-bold text-gray-900"></h3>
                    <div class="flex space-x-2">
                        <button onclick="editEventFromDetail()" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteEventFromDetail()" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button onclick="closeEventDetailModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="space-y-3">
                    <p id="detail-date" class="text-gray-600"></p>
                    <p id="detail-time" class="text-gray-600"></p>
                    <p id="detail-location" class="text-gray-600"></p>
                    <p id="detail-type" class="text-gray-600"></p>
                    <p id="detail-description" class="text-gray-700 mt-4"></p>
                    <div id="detail-sync-status" class="mt-2"></div>
                </div>
                <div class="flex justify-end mt-6">
                    <button onclick="closeEventDetailModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let currentEvents = [];
        let currentDetailEvent = null;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initializeSidebar();
            checkGoogleStatus();
            loadEvents();
        });

        // Google Calendar Integration
        async function checkGoogleStatus() {
            try {
                const response = await fetch('/google-calendar/status');
                const data = await response.json();
                updateGoogleUI(data);
            } catch (error) {
                console.error('Status check error:', error);
            }
        }

        function updateGoogleUI(status) {
            const connectBtn = document.getElementById('connect-google-btn');
            const disconnectBtn = document.getElementById('disconnect-google-btn');
            const statusText = document.getElementById('google-status-text');
            const statusDot = document.getElementById('sync-status-dot');
            const syncStatusText = document.getElementById('sync-status-text');
            const syncOption = document.getElementById('google-sync-option');
            const syncAllBtn = document.getElementById('sync-all-btn');
            
            if (status.connected) {
                connectBtn.classList.add('hidden');
                disconnectBtn.classList.remove('hidden');
                syncAllBtn.classList.remove('hidden');
                
                if (statusText) {
                    statusText.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-1"></i> Connected to Google Calendar';
                }
                if (statusDot) {
                    statusDot.className = 'w-2 h-2 bg-green-500 rounded-full';
                }
                if (syncStatusText) {
                    syncStatusText.textContent = 'Synced with Google';
                }
                if (syncOption) {
                    syncOption.classList.remove('hidden');
                }
                
                disconnectBtn.onclick = disconnectGoogle;
                syncAllBtn.onclick = syncWithGoogle;
            } else {
                connectBtn.classList.remove('hidden');
                disconnectBtn.classList.add('hidden');
                syncAllBtn.classList.add('hidden');
                
                if (statusText) {
                    statusText.innerHTML = '<i class="fas fa-times-circle text-red-500 mr-1"></i> Not Connected';
                }
                if (statusDot) {
                    statusDot.className = 'w-2 h-2 bg-gray-400 rounded-full';
                }
                if (syncStatusText) {
                    syncStatusText.textContent = 'Google Calendar not connected';
                }
                if (syncOption) {
                    syncOption.classList.add('hidden');
                }
                
                connectBtn.onclick = connectGoogle;
            }
        }

        async function connectGoogle() {
            try {
                const response = await fetch('/google-calendar/connect');
                const data = await response.json();
                
                if (data.success && data.auth_url) {
                    const width = 600, height = 600;
                    const left = (screen.width - width) / 2;
                    const top = (screen.height - height) / 2;
                    
                    const popup = window.open(data.auth_url, 'GoogleAuth', 
                        `width=${width},height=${height},left=${left},top=${top}`);
                    
                    const checkPopup = setInterval(() => {
                        if (popup.closed) {
                            clearInterval(checkPopup);
                            showToast('Checking Google Calendar connection...', 'info');
                            setTimeout(() => {
                                checkGoogleStatus();
                                loadEvents();
                            }, 2000);
                        }
                    }, 1000);
                }
            } catch (error) {
                console.error('Connection error:', error);
                showToast('Failed to connect to Google Calendar', 'error');
            }
        }

        async function disconnectGoogle() {
            if (!confirm('Disconnect Google Calendar? Your events will remain in the system.')) return;
            
            try {
                const response = await fetch('/google-calendar/disconnect', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    }
                });
                const data = await response.json();
                if (data.success) {
                    showToast('Google Calendar disconnected', 'success');
                    checkGoogleStatus();
                }
            } catch (error) {
                console.error('Disconnect error:', error);
                showToast('Failed to disconnect', 'error');
            }
        }

        async function syncWithGoogle() {
            const btn = document.getElementById('sync-all-btn');
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<div class="loading-spinner mr-2"></div> Syncing...';
            
            try {
                const response = await fetch('/api/events/sync-all-google', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    }
                });
                const data = await response.json();
                if (data.success) {
                    showToast(data.message, 'success');
                    loadEvents();
                } else {
                    showToast(data.message, 'error');
                }
            } catch (error) {
                console.error('Sync error:', error);
                showToast('Sync failed', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        }

        // Calendar Functions
        async function loadEvents() {
            try {
                const response = await fetch('/api/events');
                const events = await response.json();
                currentEvents = events;
                renderCalendar();
                renderUpcomingEvents();
                updateStatistics();
                updateCategories();
            } catch (error) {
                console.error('Error loading events:', error);
                showToast('Failed to load events', 'error');
            }
        }

        function renderCalendar() {
            const now = new Date();
            const monthYear = document.getElementById('current-month-year');
            const calendarGrid = document.getElementById('calendar-grid');
            
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];
            monthYear.textContent = `${monthNames[now.getMonth()]} ${now.getFullYear()}`;
            
            calendarGrid.innerHTML = '';
            
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            const totalDays = lastDay.getDate();
            const startingDay = firstDay.getDay();
            
            const prevMonthLastDay = new Date(now.getFullYear(), now.getMonth(), 0).getDate();
            for (let i = 0; i < startingDay; i++) {
                const day = prevMonthLastDay - startingDay + i + 1;
                const cell = createDayCell(day, 'other-month');
                calendarGrid.appendChild(cell);
            }
            
            const today = new Date();
            for (let day = 1; day <= totalDays; day++) {
                const cellDate = new Date(now.getFullYear(), now.getMonth(), day);
                const cell = createDayCell(day, cellDate.toDateString() === today.toDateString() ? 'today' : '');
                
                const dayEvents = currentEvents.filter(event => {
                    const eventDate = new Date(event.start_date);
                    return eventDate.getDate() === day && 
                           eventDate.getMonth() === now.getMonth() &&
                           eventDate.getFullYear() === now.getFullYear();
                });
                
                if (dayEvents.length > 0) {
                    const eventsContainer = cell.querySelector('.day-events');
                    dayEvents.slice(0, 3).forEach(event => {
                        const eventEl = document.createElement('div');
                        eventEl.className = `text-xs p-1 rounded truncate cursor-pointer hover:opacity-90 ${getEventClass(event.type)} ${event.synced_with_google ? 'synced-event' : ''}`;
                        eventEl.textContent = event.title;
                        eventEl.onclick = (e) => {
                            e.stopPropagation();
                            showEventDetail(event);
                        };
                        eventsContainer.appendChild(eventEl);
                    });
                    
                    if (dayEvents.length > 3) {
                        const moreEl = document.createElement('div');
                        moreEl.className = 'text-xs text-gray-500 mt-1';
                        moreEl.textContent = `+${dayEvents.length - 3} more`;
                        eventsContainer.appendChild(moreEl);
                    }
                }
                
                calendarGrid.appendChild(cell);
            }
            
            const totalCells = 42;
            const remainingCells = totalCells - (startingDay + totalDays);
            for (let i = 1; i <= remainingCells; i++) {
                const cell = createDayCell(i, 'other-month');
                calendarGrid.appendChild(cell);
            }
        }

        function createDayCell(dayNumber, additionalClasses = '') {
            const cell = document.createElement('div');
            cell.className = `calendar-day p-4 border border-gray-100 ${additionalClasses}`;
            cell.innerHTML = `
                <div class="flex justify-between items-start">
                    <span class="font-medium text-gray-900">${dayNumber}</span>
                    <button class="text-gray-400 hover:text-uthm-blue text-sm" onclick="event.stopPropagation(); openEventModalForDate(${dayNumber})">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="day-events mt-2"></div>
            `;
            return cell;
        }

        function getEventClass(type) {
            const classes = {
                'lecture': 'bg-blue-100 text-blue-800',
                'deadline': 'bg-red-100 text-red-800',
                'exam': 'bg-purple-100 text-purple-800',
                'social': 'bg-green-100 text-green-800',
                'workshop': 'bg-yellow-100 text-yellow-800',
                'important': 'bg-pink-100 text-pink-800',
                'other': 'bg-gray-100 text-gray-800'
            };
            return classes[type] || 'bg-gray-100 text-gray-800';
        }

        function renderUpcomingEvents() {
            const container = document.getElementById('upcoming-events');
            container.innerHTML = '';
            
            const nextWeek = new Date();
            nextWeek.setDate(nextWeek.getDate() + 7);
            
            const upcoming = currentEvents
                .filter(event => new Date(event.start_date) >= new Date() && new Date(event.start_date) <= nextWeek)
                .sort((a, b) => new Date(a.start_date) - new Date(b.start_date))
                .slice(0, 5);
            
            if (upcoming.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-calendar-times text-3xl mb-2"></i>
                        <p>No upcoming events for this week</p>
                    </div>
                `;
                return;
            }
            
            upcoming.forEach(event => {
                const eventDate = new Date(event.start_date);
                const eventEl = document.createElement('div');
                eventEl.className = 'flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer';
                eventEl.onclick = () => showEventDetail(event);
                
                eventEl.innerHTML = `
                    <div class="mr-4">
                        <div class="text-center">
                            <div class="font-bold text-lg">${eventDate.getDate()}</div>
                            <div class="text-xs uppercase text-gray-500">${eventDate.toLocaleDateString('en-US', { month: 'short' })}</div>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">${event.title} ${event.synced_with_google ? '<i class="fab fa-google text-blue-500 text-xs ml-1" title="Synced with Google Calendar"></i>' : ''}</h4>
                        <p class="text-sm text-gray-600">
                            ${event.start_time ? `<i class="far fa-clock mr-1"></i> ${event.start_time}` : ''}
                            ${event.location ? ` • <i class="fas fa-map-marker-alt mr-1 ml-2"></i> ${event.location}` : ''}
                        </p>
                        <div class="flex items-center mt-2">
                            <span class="px-2 py-1 ${getEventClass(event.type)} text-xs rounded">${event.type}</span>
                            ${event.visibility === 'public' ? '<span class="ml-2 px-2 py-1 bg-uthm-blue-light text-uthm-blue text-xs rounded">Public</span>' : ''}
                        </div>
                    </div>
                `;
                container.appendChild(eventEl);
            });
        }

        function updateStatistics() {
            fetch('/api/events/statistics')
                .then(res => res.json())
                .then(data => {
                    document.getElementById('stat-lectures').textContent = data.lectures || 0;
                    document.getElementById('stat-deadlines').textContent = data.deadlines || 0;
                    document.getElementById('stat-exams').textContent = data.exams || 0;
                })
                .catch(err => console.error('Stats error:', err));
        }

        function updateCategories() {
            const categories = {};
            currentEvents.forEach(event => {
                categories[event.type] = (categories[event.type] || 0) + 1;
            });
            
            ['lecture', 'deadline', 'exam', 'social', 'workshop'].forEach(type => {
                const el = document.getElementById(`cat-${type}`);
                if (el) el.textContent = categories[type] || 0;
            });
        }

        // Event Modal Functions
        function openEventModal() {
            document.getElementById('modal-title').textContent = 'Add New Event';
            document.getElementById('event-id').value = '';
            document.getElementById('event-form').reset();
            document.getElementById('event-start-date').value = new Date().toISOString().split('T')[0];
            document.getElementById('event-modal').classList.remove('hidden');
        }

        function openEventModalForDate(day) {
            openEventModal();
            const now = new Date();
            document.getElementById('event-start-date').value = 
                `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        }

        function closeEventModal() {
            document.getElementById('event-modal').classList.add('hidden');
        }

        function showEventDetail(event) {
            currentDetailEvent = event;
            document.getElementById('detail-title').textContent = event.title;
            document.getElementById('detail-date').innerHTML = `<i class="far fa-calendar mr-2"></i> ${event.start_date}${event.end_date && event.end_date !== event.start_date ? ' - ' + event.end_date : ''}`;
            document.getElementById('detail-time').innerHTML = event.start_time ? `<i class="far fa-clock mr-2"></i> ${event.start_time}${event.end_time ? ' - ' + event.end_time : ''}` : '';
            document.getElementById('detail-location').innerHTML = event.location ? `<i class="fas fa-map-marker-alt mr-2"></i> ${event.location}` : '';
            document.getElementById('detail-type').innerHTML = `<span class="px-2 py-1 ${getEventClass(event.type)} text-xs rounded">${event.type}</span> ${event.visibility === 'public' ? '<span class="ml-2 px-2 py-1 bg-uthm-blue-light text-uthm-blue text-xs rounded">Public</span>' : ''}`;
            document.getElementById('detail-description').textContent = event.description || 'No description';
            
            const syncStatus = document.getElementById('detail-sync-status');
            if (event.synced_with_google) {
                syncStatus.innerHTML = '<span class="text-sm text-green-600"><i class="fab fa-google mr-1"></i> Synced with Google Calendar</span>';
            } else {
                syncStatus.innerHTML = '<span class="text-sm text-gray-500"><i class="fas fa-cloud-upload-alt mr-1"></i> Not synced</span>';
            }
            
            document.getElementById('event-detail-modal').classList.remove('hidden');
        }

        function closeEventDetailModal() {
            document.getElementById('event-detail-modal').classList.add('hidden');
            currentDetailEvent = null;
        }

        function editEventFromDetail() {
            if (!currentDetailEvent) return;
            
            // Check permissions
            @if(Auth::user()->role !== 'admin')
            if (currentDetailEvent.visibility === 'public') {
                showToast('Only administrators can edit public events', 'error');
                return;
            }
            if (currentDetailEvent.user_id !== {{ Auth::id() }}) {
                showToast('You can only edit your own events', 'error');
                return;
            }
            @endif
            
            document.getElementById('modal-title').textContent = 'Edit Event';
            document.getElementById('event-id').value = currentDetailEvent.id;
            document.getElementById('event-title').value = currentDetailEvent.title;
            document.getElementById('event-start-date').value = currentDetailEvent.start_date;
            document.getElementById('event-end-date').value = currentDetailEvent.end_date || '';
            document.getElementById('event-start-time').value = currentDetailEvent.start_time || '';
            document.getElementById('event-end-time').value = currentDetailEvent.end_time || '';
            document.getElementById('event-type').value = currentDetailEvent.type;
            document.getElementById('event-location').value = currentDetailEvent.location || '';
            document.getElementById('event-description').value = currentDetailEvent.description || '';
            document.getElementById('event-all-day').checked = currentDetailEvent.all_day || false;
            document.getElementById('event-reminder').checked = currentDetailEvent.set_reminder || false;
            document.getElementById('event-sync-google').checked = currentDetailEvent.synced_with_google;
            
            closeEventDetailModal();
            document.getElementById('event-modal').classList.remove('hidden');
        }

        function deleteEventFromDetail() {
            if (!currentDetailEvent) return;
            
            // Check permissions
            @if(Auth::user()->role !== 'admin')
            if (currentDetailEvent.visibility === 'public') {
                showToast('Only administrators can delete public events', 'error');
                return;
            }
            if (currentDetailEvent.user_id !== {{ Auth::id() }}) {
                showToast('You can only delete your own events', 'error');
                return;
            }
            @endif
            
            if (!confirm('Are you sure you want to delete this event?')) return;
            
            const eventId = currentDetailEvent.id;
            closeEventDetailModal();
            
            fetch(`/api/events/${eventId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    loadEvents();
                } else {
                    showToast(data.message || 'Failed to delete event', 'error');
                }
            })
            .catch(err => {
                console.error('Delete error:', err);
                showToast('Failed to delete event', 'error');
            });
        }

        // Event Form Submission
        document.getElementById('event-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const eventId = document.getElementById('event-id').value;
            const isEdit = eventId !== '';
            
            const eventData = {
                title: document.getElementById('event-title').value,
                start_date: document.getElementById('event-start-date').value,
                end_date: document.getElementById('event-end-date').value || document.getElementById('event-start-date').value,
                start_time: document.getElementById('event-start-time').value || null,
                end_time: document.getElementById('event-end-time').value || null,
                type: document.getElementById('event-type').value,
                location: document.getElementById('event-location').value,
                description: document.getElementById('event-description').value,
                all_day: document.getElementById('event-all-day').checked,
                set_reminder: document.getElementById('event-reminder').checked,
                sync_to_google: document.getElementById('event-sync-google').checked,
            };
            
            const url = isEdit ? `/api/events/${eventId}` : '/api/events';
            const method = isEdit ? 'PUT' : 'POST';
            
            const submitBtn = document.getElementById('event-submit-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<div class="loading-spinner mr-2"></div> Saving...';
            
            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(eventData)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    closeEventModal();
                    loadEvents();
                } else {
                    showToast(data.message || 'Failed to save event', 'error');
                }
            })
            .catch(err => {
                console.error('Save error:', err);
                showToast('Failed to save event', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i> Save Event';
            });
        });

        // Toast Notification
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Sidebar (same as existing)
        function initializeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const toggleIcon = document.getElementById('toggle-icon');
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            
            const isExpanded = localStorage.getItem('sidebarExpanded') === 'true';
            if (isExpanded) expandSidebar();
            else collapseSidebar();
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', () => {
                    sidebar.classList.contains('sidebar-expanded') ? collapseSidebar() : expandSidebar();
                });
            }
            
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('mobile-open');
                });
            }
            
            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });
                document.addEventListener('click', () => userMenu.classList.add('hidden'));
            }
            
            if (window.innerWidth < 768) {
                document.querySelectorAll('#sidebar a').forEach(link => {
                    link.addEventListener('click', () => sidebar.classList.remove('mobile-open'));
                });
            }
            
            function expandSidebar() {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                mainContent.classList.remove('content-collapsed');
                mainContent.classList.add('content-expanded');
                if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
                localStorage.setItem('sidebarExpanded', 'true');
            }
            
            function collapseSidebar() {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
                mainContent.classList.remove('content-expanded');
                mainContent.classList.add('content-collapsed');
                if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
                localStorage.setItem('sidebarExpanded', 'false');
            }
            
            window.addEventListener('resize', () => {
                if (window.innerWidth < 768) {
                    if (!sidebar.classList.contains('mobile-open')) {
                        sidebar.style.transform = 'translateX(-100%)';
                    }
                } else {
                    sidebar.style.transform = 'translateX(0)';
                }
            });
            
            if (window.innerWidth < 768) sidebar.style.transform = 'translateX(-100%)';
        }

        // Navigation
        document.getElementById('prev-month')?.addEventListener('click', () => {
            // Implement month navigation if needed
            loadEvents();
        });
        
        document.getElementById('next-month')?.addEventListener('click', () => {
            loadEvents();
        });
        
        document.getElementById('today-btn')?.addEventListener('click', () => {
            loadEvents();
        });

        // Event Filters
        document.querySelectorAll('.event-filter').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.event-filter').forEach(b => {
                    b.classList.remove('active', 'bg-uthm-blue', 'text-white');
                    b.classList.add('bg-gray-100', 'text-gray-700');
                });
                this.classList.add('active', 'bg-uthm-blue', 'text-white');
                this.classList.remove('bg-gray-100', 'text-gray-700');
                
                const type = this.dataset.type;
                if (type === 'all') {
                    loadEvents();
                } else {
                    currentEvents = currentEvents.filter(e => e.type === type);
                    renderCalendar();
                    renderUpcomingEvents();
                }
            });
        });
    </script>
</body>
</html>