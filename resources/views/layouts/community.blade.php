<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Community Hub') - UTHM Bulletin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        
        .sidebar-transition {
            transition: width var(--transition-speed) ease;
        }
        
        .content-transition {
            transition: margin-left var(--transition-speed) ease;
        }
        
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

        .badge-admin {
            background-color: #dc2626;
            color: white;
        }
        .badge-staff {
            background-color: #2563eb;
            color: white;
        }
        .badge-student {
            background-color: #059669;
            color: white;
        }
        .badge-guest {
            background-color: #6b7280;
            color: white;
        }
        
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

        .post-card { 
            transition: all 0.3s ease; 
        }
        .post-card:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 10px 20px rgba(0,0,0,0.05); 
        }
        .modal { 
            transition: all 0.3s ease; 
        }
        /* Line clamp for consistent description height */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Ensure grid items are same height */
.grid > a {
    display: flex;
}

.grid > a > div {
    width: 100%;
}
    </style>
    @stack('styles')
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
                        <span class="font-bold text-uthm-blue">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                    </div>
                    <div class="sidebar-text">
                        <h3 class="font-medium text-gray-900">{{ Auth::user()->name ?? 'User' }}</h3>
                        <p class="text-xs text-gray-500">{{ Auth::user()->uthm_id ?? 'Student' }}</p>
                        @if(Auth::user()->role)
                            <span class="mt-1 inline-block px-2 py-1 text-xs rounded-full badge-{{ Auth::user()->role }}">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                        @endif
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
                    <a href="{{ route('announcements.my-announcements') }}" class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0"><i class="fas fa-file-alt w-5 h-5"></i></div>
                        <span class="sidebar-text ml-3">My Announcements</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('calendar') }}" class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0"><i class="fas fa-calendar-alt w-5 h-5"></i></div>
                        <span class="sidebar-text ml-3">Calendar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.community-hub') }}" class="flex items-center p-3 rounded-lg {{ request()->routeIs('student.community-hub*') ? 'bg-uthm-blue-light text-uthm-blue' : 'hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue' }} transition-colors">
                        <div class="shrink-0"><i class="fas fa-users w-5 h-5"></i></div>
                        <span class="sidebar-text ml-3">Community Hub</span>
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
        <!-- Top Navigation Bar -->
        <nav class="bg-white shadow">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">@yield('page-title', 'Community Hub')</h1>
                        @hasSection('breadcrumb')
                            <span class="mx-2 text-gray-400">/</span>
                            <span class="text-gray-600">@yield('breadcrumb')</span>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Notification Bell -->
                        <button class="relative p-2 text-gray-600 hover:text-uthm-blue transition-colors">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        
                        <!-- User Menu Dropdown -->
                        <div class="relative">
                            <button id="user-menu-button" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="font-bold text-green-700">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->uthm_id }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500"></i>
                            </button>
                            
                            <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden z-50">
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

                        <!-- Header Actions (Custom buttons) -->
                        @yield('header-actions')
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('info'))
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        {{ session('info') }}
                    </div>
                @endif

                @yield('community-content')
            </div>
        </div>
    </div>

    @stack('modals')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const toggleIcon = document.getElementById('toggle-icon');
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            
            // Initialize sidebar state from localStorage
            const isSidebarExpanded = localStorage.getItem('sidebarExpanded') === 'true';
            if (isSidebarExpanded) {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                mainContent.classList.remove('content-collapsed');
                mainContent.classList.add('content-expanded');
                if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
            }
            
            // Sidebar toggle button
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (sidebar.classList.contains('sidebar-expanded')) {
                        // Collapse sidebar
                        sidebar.classList.remove('sidebar-expanded');
                        sidebar.classList.add('sidebar-collapsed');
                        mainContent.classList.remove('content-expanded');
                        mainContent.classList.add('content-collapsed');
                        if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
                        localStorage.setItem('sidebarExpanded', 'false');
                    } else {
                        // Expand sidebar
                        sidebar.classList.remove('sidebar-collapsed');
                        sidebar.classList.add('sidebar-expanded');
                        mainContent.classList.remove('content-collapsed');
                        mainContent.classList.add('content-expanded');
                        if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
                        localStorage.setItem('sidebarExpanded', 'true');
                    }
                });
            }
            
            // Mobile menu toggle
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('mobile-open');
                });
            }
            
            // User menu dropdown
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

            // Close sidebar on mobile when clicking outside
            if (window.innerWidth <= 768) {
                document.addEventListener('click', function(e) {
                    if (!sidebar.contains(e.target) && 
                        !mobileMenuToggle.contains(e.target) && 
                        sidebar.classList.contains('mobile-open')) {
                        sidebar.classList.remove('mobile-open');
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>