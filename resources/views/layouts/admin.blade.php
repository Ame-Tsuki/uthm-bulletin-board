<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - UTHM Bulletin Board System</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        .sidebar {
            transition: all 0.3s ease;
        }
        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar-link {
            transition: all 0.2s ease;
            position: relative;
        }
        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
            padding-left: 1.5rem;
        }
        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.15);
            border-left: 4px solid #fff;
            padding-left: 1.25rem;
        }
        .sidebar-link.active i {
            color: #fff;
        }
        .sidebar-link.active span {
            font-weight: 600;
        }
        .table-row-hover:hover {
            background-color: #f9fafb;
        }
        .badge {
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
        }
    </style>
    @yield('styles')
</head>
<body class="bg-gray-50 antialiased">
    <div class="flex h-screen overflow-hidden">
        
        <aside class="sidebar w-64 bg-gray-900 text-white hidden md:block flex-shrink-0">
            <div class="p-6 h-full flex flex-col justify-between">
                <div>
                    <div class="flex items-center space-x-3 mb-8">
                        <div class="gradient-bg p-3 rounded-xl">
                            <i class="fas fa-shield-alt text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold">Admin Panel</h2>
                            <p class="text-gray-400 text-sm">UTHM Bulletin System</p>
                        </div>
                    </div>
                    
                    <nav class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center sidebar-link p-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt mr-3 text-gray-300 w-5"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center sidebar-link p-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fas fa-users mr-3 text-gray-300 w-5"></i>
                            <span>User Management</span>
                        </a>
                        <a href="{{ route('admin.announcements.index') }}" class="flex items-center sidebar-link p-3 rounded-lg {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-list mr-3 text-gray-300 w-5"></i>
                            <span>Posts & Content</span>
                        </a>
                        <a href="{{ route('admin.moderation') }}" class="flex items-center sidebar-link p-3 rounded-lg {{ request()->routeIs('admin.moderation') ? 'active' : '' }}">
                            <i class="fas fa-flag mr-3 text-gray-300 w-5"></i>
                            <span>Moderation</span>
                        </a>
                        <a href="{{ route('admin.calendar') }}" class="flex items-center sidebar-link p-3 rounded-lg {{ request()->routeIs('admin.calendar') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt mr-3 text-gray-300 w-5"></i>
                            <span>Calendar</span>
                        </a>
                        <a href="{{ route('admin.featured-posts') }}" class="flex items-center sidebar-link p-3 rounded-lg {{ request()->routeIs('admin.featured-posts') ? 'active' : '' }}">
                            <i class="fas fa-star mr-3 text-gray-300 w-5"></i>
                            <span>Featured Posts</span>
                        </a>
                        <a href="{{ route('admin.analytics') }}" class="flex items-center sidebar-link p-3 rounded-lg {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar mr-3 text-gray-300 w-5"></i>
                            <span>Analytics</span>
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center sidebar-link p-3 rounded-lg {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <i class="fas fa-cog mr-3 text-gray-300 w-5"></i>
                            <span>System Settings</span>
                        </a>
                    </nav>
                </div>
                
                <div class="mt-auto p-4 bg-gray-800 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="ml-3 overflow-hidden">
                            <p class="font-medium truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                            <p class="text-sm text-gray-400">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col h-full overflow-y-auto">
            
            <header class="bg-white shadow-sm border-b sticky top-0 z-30 flex-shrink-0">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <button id="menuToggle" class="md:hidden mr-4 text-gray-600 focus:outline-none">
                                <i class="fas fa-bars text-xl"></i>
                            </button>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-800">@yield('page_title', 'Dashboard')</h1>
                                <p class="text-gray-600 text-sm">@yield('page_subtitle', 'Welcome back, ' . (Auth::user()->name ?? 'Administrator'))</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <button class="relative text-gray-600 hover:text-gray-800 focus:outline-none">
                                <i class="fas fa-bell text-xl"></i>
                            </button>
                            <div class="relative">
                                <button id="userMenu" class="flex items-center space-x-2 focus:outline-none">
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                    </div>
                                    <span class="font-medium hidden md:inline text-gray-700">{{ Auth::user()->name ?? 'Administrator' }}</span>
                                    <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                                </button>
                                
                                <div id="dropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50 border border-gray-100">
                                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2 w-4"></i>Profile
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2 w-4"></i>Settings
                                    </a>
                                    <hr class="my-2 border-gray-100">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100 font-medium">
                                            <i class="fas fa-sign-out-alt mr-2 w-4"></i>Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div id="mobileSidebar" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 md:hidden hidden">
                <aside class="absolute left-0 top-0 h-full w-64 bg-gray-900 text-white p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-8">
                            <div class="flex items-center space-x-3">
                                <div class="gradient-bg p-3 rounded-xl">
                                    <i class="fas fa-shield-alt text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold">Admin Panel</h2>
                                    <p class="text-gray-400 text-sm">UTHM Bulletin System</p>
                                </div>
                            </div>
                            <button id="closeMenu" class="text-white focus:outline-none">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        
                        <nav class="space-y-1">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center sidebar-link p-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt mr-3 w-5"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="flex items-center sidebar-link p-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <i class="fas fa-users mr-3 w-5"></i>
                                <span>User Management</span>
                            </a>
                            <a href="{{ route('admin.announcements.index') }}" class="flex items-center sidebar-link p-3 rounded-lg {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-list mr-3 w-5"></i>
                                <span>Posts & Content</span>
                            </a>
                            <a href="{{ route('admin.moderation') }}" class="flex items-center sidebar-link p-3 rounded-lg {{ request()->routeIs('admin.moderation') ? 'active' : '' }}">
                                <i class="fas fa-flag mr-3 w-5"></i>
                                <span>Moderation</span>
                            </a>
                            <a href="{{ route('admin.calendar') }}" class="flex items-center sidebar-link p-3 rounded-lg {{ request()->routeIs('admin.calendar') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt mr-3 w-5"></i>
                                <span>Calendar</span>
                            </a>
                            <a href="{{ route('admin.featured-posts') }}" class="flex items-center sidebar-link p-3 rounded-lg {{ request()->routeIs('admin.featured-posts') ? 'active' : '' }}">
                                <i class="fas fa-star mr-3 w-5"></i>
                                <span>Featured Posts</span>
                            </a>
                            <a href="{{ route('admin.analytics') }}" class="flex items-center sidebar-link p-3 rounded-lg {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                                <i class="fas fa-chart-bar mr-3 w-5"></i>
                                <span>Analytics</span>
                            </a>
                            <a href="{{ route('admin.settings.index') }}" class="flex items-center sidebar-link p-3 rounded-lg {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                <i class="fas fa-cog mr-3 w-5"></i>
                                <span>System Settings</span>
                            </a>
                        </nav>
                    </div>
                    
                    <div class="mt-auto p-4 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="ml-3 overflow-hidden">
                                <p class="text-sm font-medium truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                                <p class="text-xs text-gray-400">Administrator</p>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            <main class="p-6 flex-grow">
                @yield('content')
            </main>

            <footer class="bg-white border-t px-6 py-4 mt-auto flex-shrink-0">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-2 md:space-y-0">
                    <div class="text-gray-600 text-sm">
                        <p>&copy; {{ date('Y') }} UTHM Bulletin Board System. All rights reserved.</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500">v1.2.1</span>
                        <span class="text-sm text-gray-500">System Rendered</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Menu Selectors
            const menuToggle = document.getElementById('menuToggle');
            const closeMenu = document.getElementById('closeMenu');
            const mobileSidebar = document.getElementById('mobileSidebar');
            
            if (menuToggle && mobileSidebar) {
                menuToggle.addEventListener('click', () => {
                    mobileSidebar.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                });
            }
            
            if (closeMenu && mobileSidebar) {
                closeMenu.addEventListener('click', () => {
                    mobileSidebar.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                });
                
                mobileSidebar.addEventListener('click', (e) => {
                    if (e.target === mobileSidebar) {
                        mobileSidebar.classList.add('hidden');
                        document.body.classList.remove('overflow-hidden');
                    }
                });
            }

            // User Dropdown Profile Menu
            const userMenu = document.getElementById('userMenu');
            const dropdownMenu = document.getElementById('dropdownMenu');
            
            if (userMenu && dropdownMenu) {
                userMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('hidden');
                });

                document.addEventListener('click', function(event) {
                    if (!userMenu.contains(event.target) && !dropdownMenu.contains(event.target)) {
                        dropdownMenu.classList.add('hidden');
                    }
                });
            }

            // Global Flash Alert Transient Actions
            setTimeout(function() {
                document.querySelectorAll('.alert-auto-hide').forEach(alert => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });
    </script>
    @yield('scripts')
</body>
</html>