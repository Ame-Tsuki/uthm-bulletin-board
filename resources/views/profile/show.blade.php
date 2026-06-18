<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - UTHM Bulletin Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('layouts.partials.portal-head')
</head>
<body class="portal-body">
    <div class="min-h-screen">
        <!-- Top Navigation Bar -->
        <nav class="portal-topbar">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Left: Back Button -->
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-600 hover:text-uthm-blue hover:bg-uthm-blue-light rounded-xl transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                        </a>
                    </div>
                    
                    <!-- Center: Page Title -->
                    <div class="flex items-center">
                        <h1 class="text-lg font-bold text-gray-900">My Profile</h1>
                    </div>
                    
                    <!-- Right: Action Buttons -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-uthm-blue text-white text-sm font-semibold rounded-xl hover:bg-uthm-blue-dark transition-colors">
                            <i class="fas fa-edit mr-2"></i> Edit Profile
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 text-sm font-semibold rounded-xl hover:bg-red-100 transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <div class="portal-main">
            <div class="portal-container max-w-4xl mx-auto">
                <!-- Profile Header Card -->
                <div class="portal-card overflow-hidden">
                    <div class="portal-welcome p-8 relative">
                        <div class="flex items-center relative z-10">
                            <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center mr-6 shadow-lg">
                                <span class="font-bold text-2xl text-uthm-blue">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                                <p class="text-blue-100/90 mt-0.5">{{ $user->uthm_id ?? 'UTHM Member' }}</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="inline-flex items-center px-3 py-1 portal-badge text-xs">
                                        <i class="fas fa-user-tag mr-1.5"></i>
                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                    @if($user->hasVerifiedEmail())
                                        <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                            <i class="fas fa-check-circle mr-1.5"></i> Verified
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8">
                        <!-- Stats Cards with Real Data -->
                        <div class="grid grid-cols-3 gap-4 mb-8">
                            <a href="{{ route('announcements.my-announcements') }}" class="text-center p-4 bg-gray-50 rounded-xl hover:bg-blue-50 transition-colors group cursor-pointer">
                                <i class="fas fa-bullhorn text-blue-600 text-xl mb-2 group-hover:scale-110 transition-transform"></i>
                                <p class="text-2xl font-bold text-gray-900">{{ $announcementsCount }}</p>
                                <p class="text-xs text-gray-500 font-medium mt-1">Announcements</p>
                            </a>
                            
                            <a href="{{ route('calendar') }}" class="text-center p-4 bg-gray-50 rounded-xl hover:bg-green-50 transition-colors group cursor-pointer">
                                <i class="fas fa-calendar-check text-green-600 text-xl mb-2 group-hover:scale-110 transition-transform"></i>
                                <p class="text-2xl font-bold text-gray-900">{{ $eventsCount }}</p>
                                <p class="text-xs text-gray-500 font-medium mt-1">Events Created</p>
                            </a>
                            
                            <a href="{{ route('community-hub') }}" class="text-center p-4 bg-gray-50 rounded-xl hover:bg-purple-50 transition-colors group cursor-pointer">
                                <i class="fas fa-users text-purple-600 text-xl mb-2 group-hover:scale-110 transition-transform"></i>
                                <p class="text-2xl font-bold text-gray-900">{{ $groupsCount }}</p>
                                <p class="text-xs text-gray-500 font-medium mt-1">Groups Joined</p>
                            </a>
                        </div>

                        <!-- Detailed Information Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Personal Information -->
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="portal-section-title flex items-center gap-2 text-base">
                                        <i class="fas fa-user-circle text-uthm-blue"></i> Personal Information
                                    </h3>
                                    <a href="{{ route('profile.edit') }}" class="text-xs text-uthm-blue hover:text-uthm-blue-dark font-medium">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                </div>
                                <div class="space-y-3">
                                    <div class="p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Full Name</p>
                                        <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                    </div>
                                    <div class="p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">UTHM ID</p>
                                        <p class="font-semibold text-gray-900">{{ $user->uthm_id ?? 'Not provided' }}</p>
                                    </div>
                                    <div class="p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Email Address</p>
                                        <div class="flex items-center justify-between">
                                            <p class="font-semibold text-gray-900">{{ $user->email ?? 'Not provided' }}</p>
                                            @if(!$user->hasVerifiedEmail())
                                                <span class="text-xs text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full">Unverified</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($user->faculty)
                                    <div class="p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Faculty</p>
                                        <p class="font-semibold text-gray-900">{{ $user->faculty }}</p>
                                    </div>
                                    @endif
                                    @if($user->phone)
                                    <div class="p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Phone</p>
                                        <p class="font-semibold text-gray-900">{{ $user->phone }}</p>
                                    </div>
                                    @endif
                                    <div class="p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1.5">My Interests</p>
                                        @if(is_array($user->interests) && count($user->interests) > 0)
                                            <div class="flex flex-wrap gap-1.5 mt-1">
                                                @foreach($user->interests as $interest)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-uthm-blue-light text-uthm-blue border border-uthm-blue/10 hover:scale-105 transition-transform duration-150">
                                                        <i class="fas fa-hashtag mr-1 opacity-70"></i>{{ $interest }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-500 italic">No interests added yet. Edit profile to add some!</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Account Information -->
                            <div>
                                <h3 class="portal-section-title mb-4 flex items-center gap-2 text-base">
                                    <i class="fas fa-shield-alt text-uthm-blue"></i> Account Information
                                </h3>
                                <div class="space-y-3">
                                    <div class="p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Role</p>
                                        <span class="inline-block px-3 py-1 badge-{{ $user->role }} rounded-full text-xs font-semibold">
                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </div>
                                    <div class="p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Account Created</p>
                                        <p class="font-semibold text-gray-900">{{ $user->created_at->format('F d, Y') }}</p>
                                    </div>
                                    <div class="p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Last Updated</p>
                                        <p class="font-semibold text-gray-900">{{ $user->updated_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                
                                <!-- Activity Summary -->
                                <div class="mt-6 p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Activity Summary</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-600">Total Announcements</span>
                                            <span class="text-sm font-semibold text-blue-600">{{ $announcementsCount }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-600">Events Created</span>
                                            <span class="text-sm font-semibold text-green-600">{{ $eventsCount }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-600">Groups Joined</span>
                                            <span class="text-sm font-semibold text-purple-600">{{ $groupsCount }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <div class="flex flex-wrap gap-3 justify-between items-center">
                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ route('profile.edit') }}" class="portal-btn-primary inline-flex items-center text-sm">
                                        <i class="fas fa-edit mr-2"></i> Edit Profile
                                    </a>
                                    <a href="{{ route('profile.edit') }}#security" class="inline-flex items-center px-5 py-2 bg-white border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors text-sm">
                                        <i class="fas fa-lock mr-2"></i> Change Password
                                    </a>
                                    <a href="{{ route('settings') }}" class="inline-flex items-center px-5 py-2 bg-white border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors text-sm">
                                        <i class="fas fa-cog mr-2"></i> Settings
                                    </a>
                                </div>
                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-5 py-2 bg-red-50 text-red-700 font-semibold rounded-xl hover:bg-red-100 transition-colors text-sm">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Links Cards -->
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('announcements.my-announcements') }}" class="portal-card p-5 hover:shadow-md transition-all group cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="portal-stat-icon bg-blue-100 group-hover:bg-blue-200 transition-colors">
                                <i class="fas fa-bullhorn text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">My Announcements</p>
                                <p class="text-xs text-gray-500">{{ $announcementsCount }} posts</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('calendar') }}" class="portal-card p-5 hover:shadow-md transition-all group cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="portal-stat-icon bg-green-100 group-hover:bg-green-200 transition-colors">
                                <i class="fas fa-calendar-check text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">My Events</p>
                                <p class="text-xs text-gray-500">{{ $eventsCount }} events</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('community-hub') }}" class="portal-card p-5 hover:shadow-md transition-all group cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="portal-stat-icon bg-purple-100 group-hover:bg-purple-200 transition-colors">
                                <i class="fas fa-users text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">My Groups</p>
                                <p class="text-xs text-gray-500">{{ $groupsCount }} groups</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('profile.edit') }}" class="portal-card p-5 hover:shadow-md transition-all group cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="portal-stat-icon bg-yellow-100 group-hover:bg-yellow-200 transition-colors">
                                <i class="fas fa-cog text-yellow-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Settings</p>
                                <p class="text-xs text-gray-500">Manage account</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>