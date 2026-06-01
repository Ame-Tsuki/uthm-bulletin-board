<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - UTHM Bulletin Board System</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        .sidebar {
            transition: all 0.3s ease;
            width: 280px;
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
            font-size: 0.95rem;
            font-weight: 500;
            padding: 0.875rem 1rem;
            margin-bottom: 0.25rem;
        }
        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
            padding-left: 1.75rem;
        }
        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.15);
            border-left: 4px solid #fff;
            padding-left: 1.5rem;
        }
        .sidebar-link.active i {
            color: #fff;
        }
        .sidebar-link.active span {
            font-weight: 700;
        }
        .sidebar-link i {
            font-size: 1.25rem;
            width: 1.75rem;
        }
        .sidebar-link span {
            font-size: 0.95rem;
            letter-spacing: 0.01em;
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
        
        /* Sidebar header text styles */
        .sidebar-header h2 {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.01em;
        }
        .sidebar-header p {
            font-size: 0.75rem;
            letter-spacing: 0.02em;
        }
        
        /* User profile in sidebar */
        .user-profile-name {
            font-size: 0.9rem;
            font-weight: 600;
        }
        .user-profile-role {
            font-size: 0.7rem;
        }
        
        /* Main header styles */
        .main-header h1 {
            font-size: 1.6rem;
            font-weight: 700;
        }
        .main-header p {
            font-size: 0.85rem;
        }
    </style>
    @yield('styles')
</head>
<body class="bg-gray-50 antialiased">
    <div class="flex h-screen overflow-hidden">
        
        <aside class="sidebar w-72 bg-gray-900 text-white hidden md:block flex-shrink-0 shadow-xl">
            <div class="p-6 h-full flex flex-col justify-between">
                <div>
                    <div class="flex items-center space-x-3 mb-10 sidebar-header">
                        <div class="gradient-bg p-3 rounded-xl shadow-lg">
                            <i class="fas fa-shield-alt text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-extrabold tracking-tight">Admin Panel</h2>
                            <p class="text-gray-400 text-xs uppercase tracking-wide">UTHM Bulletin System</p>
                        </div>
                    </div>
                    
                    <nav class="space-y-1 mt-2">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center sidebar-link rounded-xl {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt mr-3 text-gray-300 w-5"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center sidebar-link rounded-xl {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fas fa-users mr-3 text-gray-300 w-5"></i>
                            <span>User Management</span>
                        </a>
                        <a href="{{ route('admin.announcements.index') }}" class="flex items-center sidebar-link rounded-xl {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-list mr-3 text-gray-300 w-5"></i>
                            <span>Posts & Content</span>
                        </a>
                        <a href="{{ route('admin.moderation') }}" class="flex items-center sidebar-link rounded-xl {{ request()->routeIs('admin.moderation') ? 'active' : '' }}">
                            <i class="fas fa-flag mr-3 text-gray-300 w-5"></i>
                            <span>Moderation</span>
                        </a>
                        <a href="{{ route('admin.calendar') }}" class="flex items-center sidebar-link rounded-xl {{ request()->routeIs('admin.calendar') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt mr-3 text-gray-300 w-5"></i>
                            <span>Calendar</span>
                        </a>
                        <a href="{{ route('admin.featured-posts') }}" class="flex items-center sidebar-link rounded-xl {{ request()->routeIs('admin.featured-posts') ? 'active' : '' }}">
                            <i class="fas fa-star mr-3 text-gray-300 w-5"></i>
                            <span>Featured Posts</span>
                        </a>
                        <a href="{{ route('admin.analytics') }}" class="flex items-center sidebar-link rounded-xl {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar mr-3 text-gray-300 w-5"></i>
                            <span>Analytics</span>
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center sidebar-link rounded-xl {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <i class="fas fa-cog mr-3 text-gray-300 w-5"></i>
                            <span>System Settings</span>
                        </a>
                    </nav>
                </div>
                
                <div class="mt-auto p-4 bg-gray-800 rounded-xl mt-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="ml-3 overflow-hidden">
                            <p class="font-semibold text-base truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col h-full overflow-y-auto">
            
            <header class="bg-white shadow-sm border-b sticky top-0 z-30 flex-shrink-0">
                <div class="px-8 py-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <button id="menuToggle" class="md:hidden mr-4 text-gray-600 focus:outline-none">
                                <i class="fas fa-bars text-2xl"></i>
                            </button>
                            <div class="main-header">
                                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 tracking-tight">@yield('page_title', 'Dashboard')</h1>
                                <p class="text-gray-500 text-sm mt-0.5">@yield('page_subtitle', 'Welcome back, ' . (Auth::user()->name ?? 'Administrator'))</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-5">
                            
                            <div class="relative" id="notificationDropdown">
                                <button id="notificationToggle" class="relative text-gray-600 hover:text-gray-800 focus:outline-none transition mt-1">
                                    <i class="fas fa-bell text-xl"></i>
                                    @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 border-2 border-white rounded-full">
                                            {{ Auth::user()->unreadNotifications->count() }}
                                        </span>
                                    @endif
                                </button>

                                <div id="notificationMenu" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-xl z-50 border border-gray-100 overflow-hidden">
                                    <div class="py-3 px-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                                        <span class="text-sm font-bold text-gray-700">Notifications</span>
                                        @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                                            <form action="{{ route('notifications.markAllRead') }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-xs font-semibold text-blue-600 hover:text-blue-800 focus:outline-none">Mark all read</button>
                                            </form>
                                        @endif
                                    </div>
                                    
                                    <div class="max-h-72 overflow-y-auto">
                                        @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                                            @foreach(Auth::user()->unreadNotifications as $notification)
                                                <a href="{{ $notification->data['url'] ?? '#' }}" class="block px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition {{ $loop->index % 2 == 0 ? 'bg-blue-50/30' : 'bg-white' }}">
                                                    <p class="text-sm text-gray-800 font-medium leading-tight">
                                                        <i class="fas fa-exclamation-circle text-orange-500 mr-1.5"></i> 
                                                        {{ $notification->data['message'] ?? 'New notification' }}
                                                    </p>
                                                    @if(isset($notification->data['reason']))
                                                        <p class="text-xs text-gray-500 mt-1 truncate pl-6">Reason: {{ $notification->data['reason'] }}</p>
                                                    @endif
                                                    <p class="text-[11px] text-gray-400 mt-1 pl-6">{{ $notification->created_at->diffForHumans() }}</p>
                                                </a>
                                            @endforeach
                                        @else
                                            <div class="px-4 py-8 text-center text-gray-500 flex flex-col items-center">
                                                <i class="far fa-bell-slash text-3xl mb-2 text-gray-300"></i>
                                                <p class="text-sm">You have no new notifications.</p>
                                            </div>
                                        @endif
                                    </div>
                                    @if(Auth::check() && Auth::user()->notifications->count() > 0)
                                    <div class="py-2 px-4 bg-gray-50 border-t border-gray-100 text-center">
                                        <a href="{{ route('admin.moderation') }}" class="text-xs font-semibold text-gray-600 hover:text-gray-800">View all notifications</a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="relative">
                                <button id="userMenu" class="flex items-center space-x-3 focus:outline-none hover:bg-gray-50 px-3 py-2 rounded-lg transition">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-base shadow-md">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                    </div>
                                    <div class="hidden md:block text-left">
                                        <span class="font-semibold text-gray-800 block">{{ Auth::user()->name ?? 'Administrator' }}</span>
                                        <span class="text-xs text-gray-500">Administrator</span>
                                    </div>
                                    <i class="fas fa-chevron-down text-gray-400 text-xs hidden md:block"></i>
                                </button>
                                
                                <div id="dropdownMenu" class="hidden absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl py-2 z-50 border border-gray-100">
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name ?? 'Administrator' }}</p>
                                        <p class="text-xs text-gray-500">{{ Auth::user()->email ?? 'admin@uthm.edu.my' }}</p>
                                    </div>
                                    <a href="#" class="block px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition">
                                        <i class="fas fa-user mr-3 w-4 text-gray-400"></i>Profile
                                    </a>
                                    <a href="#" class="block px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition">
                                        <i class="fas fa-cog mr-3 w-4 text-gray-400"></i>Settings
                                    </a>
                                    <hr class="my-2 border-gray-100">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2.5 text-red-600 hover:bg-gray-50 font-medium transition">
                                            <i class="fas fa-sign-out-alt mr-3 w-4 text-red-400"></i>Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div id="mobileSidebar" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 md:hidden hidden">
                <aside class="absolute left-0 top-0 h-full w-72 bg-gray-900 text-white p-6 flex flex-col justify-between shadow-2xl">
                    <div>
                        <div class="flex justify-between items-center mb-8">
                            <div class="flex items-center space-x-3">
                                <div class="gradient-bg p-3 rounded-xl shadow-lg">
                                    <i class="fas fa-shield-alt text-2xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-extrabold tracking-tight">Admin Panel</h2>
                                    <p class="text-gray-400 text-xs uppercase tracking-wide">UTHM Bulletin System</p>
                                </div>
                            </div>
                            <button id="closeMenu" class="text-white focus:outline-none bg-gray-800 p-2 rounded-lg">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        
                        <nav class="space-y-1 mt-2">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center sidebar-link rounded-xl py-3 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt mr-3 w-5"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="flex items-center sidebar-link rounded-xl py-3 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <i class="fas fa-users mr-3 w-5"></i>
                                <span>User Management</span>
                            </a>
                            <a href="{{ route('admin.announcements.index') }}" class="flex items-center sidebar-link rounded-xl py-3 {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-list mr-3 w-5"></i>
                                <span>Posts & Content</span>
                            </a>
                            <a href="{{ route('admin.moderation') }}" class="flex items-center sidebar-link rounded-xl py-3 {{ request()->routeIs('admin.moderation') ? 'active' : '' }}">
                                <i class="fas fa-flag mr-3 w-5"></i>
                                <span>Moderation</span>
                            </a>
                            <a href="{{ route('admin.calendar') }}" class="flex items-center sidebar-link rounded-xl py-3 {{ request()->routeIs('admin.calendar') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt mr-3 w-5"></i>
                                <span>Calendar</span>
                            </a>
                            <a href="{{ route('admin.featured-posts') }}" class="flex items-center sidebar-link rounded-xl py-3 {{ request()->routeIs('admin.featured-posts') ? 'active' : '' }}">
                                <i class="fas fa-star mr-3 w-5"></i>
                                <span>Featured Posts</span>
                            </a>
                            <a href="{{ route('admin.analytics') }}" class="flex items-center sidebar-link rounded-xl py-3 {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                                <i class="fas fa-chart-bar mr-3 w-5"></i>
                                <span>Analytics</span>
                            </a>
                            <a href="{{ route('admin.settings.index') }}" class="flex items-center sidebar-link rounded-xl py-3 {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                <i class="fas fa-cog mr-3 w-5"></i>
                                <span>System Settings</span>
                            </a>
                        </nav>
                    </div>
                    
                    <div class="mt-auto p-4 bg-gray-800 rounded-xl mt-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="ml-3 overflow-hidden">
                                <p class="font-semibold text-base truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                                <p class="text-xs text-gray-400 uppercase tracking-wide">Administrator</p>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            <main class="p-6 md:p-8 flex-grow">
                @yield('content')
            </main>

            <footer class="bg-white border-t px-6 md:px-8 py-5 mt-auto flex-shrink-0">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-3 md:space-y-0">
                    <div class="text-gray-500 text-sm">
                        <p>&copy; {{ date('Y') }} UTHM Bulletin Board System. All rights reserved.</p>
                    </div>
                    <div class="flex items-center space-x-5">
                        <span class="text-sm text-gray-400">v1.2.1</span>
                        <span class="text-sm text-gray-400">System Rendered</span>
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
            
            // Notification Dropdown Menu
            const notificationToggle = document.getElementById('notificationToggle');
            const notificationMenu = document.getElementById('notificationMenu');
            
            if (userMenu && dropdownMenu) {
                userMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('hidden');
                    // Hide notifications if open
                    if(notificationMenu && !notificationMenu.classList.contains('hidden')) {
                        notificationMenu.classList.add('hidden');
                    }
                });
            }

            if (notificationToggle && notificationMenu) {
                notificationToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationMenu.classList.toggle('hidden');
                    // Hide user menu if open
                    if(dropdownMenu && !dropdownMenu.classList.contains('hidden')) {
                        dropdownMenu.classList.add('hidden');
                    }
                });
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                if (dropdownMenu && !userMenu.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.add('hidden');
                }
                if (notificationMenu && !notificationToggle.contains(event.target) && !notificationMenu.contains(event.target)) {
                    notificationMenu.classList.add('hidden');
                }
            });

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