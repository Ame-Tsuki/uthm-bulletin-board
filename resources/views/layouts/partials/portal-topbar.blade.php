@php
    $portalUser = $user ?? Auth::user();
@endphp

<nav class="portal-topbar sticky top-0 z-30">
    <div class="portal-topbar-wrap max-w-full mx-auto">
        <div class="portal-topbar-inner">
            <div class="flex items-center min-w-0">
                <h1 class="text-lg font-bold text-gray-900 truncate">{{ $pageTitle ?? 'Dashboard' }}</h1>
                @if(!empty($breadcrumbId))
                    <span class="mx-2 text-gray-300 hidden sm:inline">/</span>
                    <span id="{{ $breadcrumbId }}" class="text-gray-500 text-sm hidden sm:inline truncate">Loading...</span>
                @elseif(!empty($breadcrumb))
                    <span class="mx-2 text-gray-300 hidden sm:inline">/</span>
                    <span class="text-gray-500 text-sm hidden sm:inline truncate">{{ $breadcrumb }}</span>
                @endif
            </div>

            <div class="flex items-center space-x-2 sm:space-x-4">
                @if(!empty($headerActionsHtml))
                    {!! $headerActionsHtml !!}
                @elseif(isset($headerActions))
                    {{ $headerActions }}
                @endif

                @include('layouts.partials.notification-bell')

                <div class="relative">
                    <button id="user-menu-button" type="button" class="flex items-center space-x-2 p-1.5 pr-3 rounded-xl hover:bg-gray-100 transition-colors">
                        <div class="w-9 h-9 bg-gradient-to-br from-green-100 to-emerald-50 rounded-full flex items-center justify-center ring-2 ring-white shadow-sm">
                            <span class="font-bold text-green-700 text-sm">{{ strtoupper(substr($portalUser->name, 0, 1)) }}</span>
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-semibold text-gray-900 leading-tight">{{ $portalUser->name }}</p>
                            <p class="text-xs text-gray-500">{{ $portalUser->uthm_id }}</p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs hidden md:inline"></i>
                    </button>

                    <div id="user-menu" class="portal-dropdown absolute right-0 mt-2 w-52 py-2 hidden z-50">
                        <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-user w-5 text-gray-400 mr-2"></i> My Profile
                        </a>
                        <a href="{{ route('settings') }}" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-cog w-5 text-gray-400 mr-2"></i> Settings
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <i class="fas fa-sign-out-alt w-5 mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
