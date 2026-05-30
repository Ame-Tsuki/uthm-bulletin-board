@php
    $portalUser = $user ?? Auth::user();
    $portalRole = $portalRole ?? ($portalUser->role ?? 'student');
    $portalLabel = $portalLabel ?? (ucfirst($portalRole) . ' Portal');
    $portalIcon = $portalRole === 'staff' ? 'fa-chalkboard-teacher' : 'fa-user-graduate';
    $communityHubRoute = $portalRole === 'staff' ? 'staff.community-hub' : 'student.community-hub';

    $navItems = [
        ['route' => 'dashboard', 'icon' => 'fa-home', 'label' => 'Dashboard', 'active' => request()->routeIs('dashboard', 'student.dashboard', 'staff.dashboard')],
        ['route' => 'announcements.index', 'icon' => 'fa-bullhorn', 'label' => 'Announcements', 'active' => request()->routeIs('announcements.index', 'announcements.show')],
        ['route' => 'announcements.my-announcements', 'icon' => 'fa-file-alt', 'label' => 'My Announcements', 'active' => request()->routeIs('announcements.my-announcements', 'announcements.create', 'announcements.edit')],
        ['route' => 'calendar', 'icon' => 'fa-calendar-alt', 'label' => 'Calendar', 'active' => request()->routeIs('calendar', 'student.calendar', 'staff.calendar')],
        ['route' => $communityHubRoute, 'icon' => 'fa-users', 'label' => 'Community Hub', 'active' => request()->routeIs('student.community-hub*', 'staff.community-hub*')],
        ['route' => 'settings', 'icon' => 'fa-cog', 'label' => 'Settings', 'active' => request()->routeIs('settings')],
    ];
@endphp

<div class="md:hidden fixed top-4 left-4 z-50">
    <button id="mobile-menu-toggle" type="button" aria-label="Open menu" class="bg-uthm-blue text-white p-2.5 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
</div>

<div id="sidebar" class="sidebar-collapsed portal-sidebar h-screen fixed left-0 top-0 overflow-y-auto z-40 sidebar-transition">
    <div class="p-3 border-b border-gray-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3 overflow-hidden">
                <div class="portal-sidebar-logo text-white p-2.5 rounded-xl shrink-0">
                    <i class="fas {{ $portalIcon }} text-lg"></i>
                </div>
                <div class="sidebar-text">
                    <h2 class="font-bold text-gray-900 text-sm leading-tight">UTHM Bulletin</h2>
                    <p class="text-xs text-gray-500">{{ $portalLabel }}</p>
                </div>
            </div>
            <button id="sidebar-toggle" type="button" aria-label="Toggle sidebar" class="hidden md:flex items-center justify-center w-8 h-8 text-gray-400 hover:text-uthm-blue hover:bg-uthm-blue-light rounded-lg shrink-0 transition-colors">
                <svg id="toggle-icon" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                </svg>
            </button>
        </div>
    </div>

    <a href="{{ route('profile.show') }}" class="block hover:bg-uthm-blue-light/50 transition-colors">
        <div class="p-3 border-b border-gray-100">
            <div class="flex items-center space-x-2.5">
                <div class="w-9 h-9 bg-gradient-to-br from-uthm-blue-light to-blue-100 rounded-full flex items-center justify-center shrink-0 ring-2 ring-white shadow-sm">
                    <span class="font-bold text-uthm-blue text-sm">{{ strtoupper(substr($portalUser->name ?? 'G', 0, 1)) }}</span>
                </div>
                <div class="sidebar-text min-w-0">
                    <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $portalUser->name ?? 'Guest User' }}</h3>
                    <p class="text-xs text-gray-500 truncate">{{ $portalUser->uthm_id ?? 'UTHM Member' }}</p>
                    @if($portalUser->role ?? null)
                        <span class="mt-1 inline-block px-2 py-0.5 text-xs rounded-full badge-{{ $portalUser->role }}">
                            {{ ucfirst(str_replace('_', ' ', $portalUser->role)) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </a>

    <nav class="p-2 pb-20">
        <ul class="space-y-0.5">
            @foreach($navItems as $item)
                <li>
                    <a href="{{ route($item['route']) }}"
                       class="portal-nav-link {{ $item['active'] ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fas {{ $item['icon'] }}"></i></span>
                        <span class="sidebar-text ml-3">{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    <div class="absolute bottom-0 left-0 right-0 p-3 border-t border-gray-100 bg-gradient-to-t from-white to-transparent">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="portal-nav-link w-full text-red-500 hover:bg-red-50 hover:text-red-600">
                <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>
                <span class="sidebar-text ml-3">Logout</span>
            </button>
        </form>
    </div>
</div>
