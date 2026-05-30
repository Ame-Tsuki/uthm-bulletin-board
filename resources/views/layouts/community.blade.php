<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Community Hub') - UTHM Bulletin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.partials.portal-head')
    <style>
        .post-card {
            transition: all 0.25s ease;
        }
        .post-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(0, 86, 166, 0.08);
        }
        .modal { transition: all 0.3s ease; }
        .grid > a { display: flex; }
        .grid > a > div { width: 100%; }
    </style>
    @stack('styles')
</head>
<body class="portal-body">
    @include('layouts.partials.portal-sidebar', ['user' => $user ?? Auth::user()])

    <div id="main-content" class="content-collapsed min-h-screen content-transition">
        <nav class="portal-topbar sticky top-0 z-30">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center min-w-0">
                        <h1 class="text-lg font-bold text-gray-900 truncate">@yield('page-title', 'Community Hub')</h1>
                        @hasSection('breadcrumb')
                            <span class="mx-2 text-gray-300 hidden sm:inline">/</span>
                            <span class="text-gray-500 text-sm hidden sm:inline truncate">@yield('breadcrumb')</span>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        <div class="relative">
                            <button id="notification-menu-button" type="button" class="relative p-2.5 text-gray-500 hover:text-uthm-blue hover:bg-uthm-blue-light rounded-xl transition-colors focus:outline-none">
                                <i class="fas fa-bell text-lg"></i>
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                    <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-red-500 rounded-full text-[10px] text-white flex items-center justify-center font-bold font-sans">
                                        {{ Auth::user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            </button>
                            
                            <div id="notification-menu" class="portal-dropdown absolute right-0 mt-2 w-80 py-2 hidden z-50 max-h-[450px] overflow-y-auto">
                                <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                                    <span class="font-bold text-sm text-gray-800">Notifications</span>
                                    @if(Auth::user()->unreadNotifications->count() > 0)
                                        <form action="{{ route('notifications.markAllRead') }}" method="POST" class="inline m-0">
                                            @csrf
                                            <button type="submit" class="text-xs text-uthm-blue hover:underline font-medium focus:outline-none">
                                                Mark all as read
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                
                                <div class="divide-y divide-gray-50">
                                    @forelse(Auth::user()->unreadNotifications as $notification)
                                        <a href="{{ route('notifications.read', $notification->id) }}" class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                                            <p class="text-sm font-semibold text-gray-900 mb-0.5">
                                                {{ $notification->data['title'] ?? 'System Update' }}
                                            </p>
                                            <p class="text-xs text-gray-600 line-clamp-2">
                                                {{ $notification->data['message'] ?? '' }}
                                            </p>
                                            <span class="text-[10px] text-gray-400 mt-1 block">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                        </a>
                                    @empty
                                        <div class="px-4 py-8 text-center text-gray-400">
                                            <i class="fas fa-bell-slash text-2xl mb-2 block"></i>
                                            <p class="text-xs">All caught up! No unread notifications.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        
                        <div class="relative">
                            <button id="user-menu-button" type="button" class="flex items-center space-x-2 p-1.5 pr-3 rounded-xl hover:bg-gray-100 transition-colors">
                                <div class="w-9 h-9 bg-gradient-to-br from-green-100 to-emerald-50 rounded-full flex items-center justify-center ring-2 ring-white shadow-sm">
                                    <span class="font-bold text-green-700 text-sm">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-semibold text-gray-900 leading-tight">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->uthm_id }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs hidden md:inline"></i>
                            </button>
                            
                            <div id="user-menu" class="portal-dropdown absolute right-0 mt-2 w-52 py-2 hidden z-50">
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

                        @yield('header-actions')
                    </div>
                </div>
            </div>
        </nav>

        @include('layouts.partials.portal-content-open')
                @if(session('success'))
                    <div class="portal-card bg-green-50 border-green-200 text-green-800 px-4 py-3 mb-4 flex items-center text-sm">
                        <i class="fas fa-check-circle mr-2 text-green-600"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="portal-card bg-red-50 border-red-200 text-red-800 px-4 py-3 mb-4 flex items-center text-sm">
                        <i class="fas fa-exclamation-circle mr-2 text-red-600"></i>
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('info'))
                    <div class="portal-card bg-blue-50 border-blue-200 text-blue-800 px-4 py-3 mb-4 flex items-center text-sm">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                        {{ session('info') }}
                    </div>
                @endif

                @yield('community-content')
        @include('layouts.partials.portal-content-close')
    </div>

    @stack('modals')

    @include('layouts.partials.portal-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            const notificationMenuButton = document.getElementById('notification-menu-button');
            const notificationMenu = document.getElementById('notification-menu');

            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (notificationMenu) notificationMenu.classList.add('hidden');
                    userMenu.classList.toggle('hidden');
                });
            }

            if (notificationMenuButton && notificationMenu) {
                notificationMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (userMenu) userMenu.classList.add('hidden');
                    notificationMenu.classList.toggle('hidden');
                });
            }

            document.addEventListener('click', function() {
                if (userMenu) userMenu.classList.add('hidden');
                if (notificationMenu) notificationMenu.classList.add('hidden');
            });
        });
    </script>

    @stack('scripts')
</body>
</html>