<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - UTHM Bulletin Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('layouts.partials.portal-head')
</head>
<body class="portal-body">
    <div class="min-h-screen">
        <nav class="portal-topbar">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-600 hover:text-uthm-blue hover:bg-uthm-blue-light rounded-xl transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                        </a>
                    </div>
                    <div class="flex items-center">
                        <h1 class="text-lg font-bold text-gray-900">My Profile</h1>
                    </div>
                    <div class="w-32"></div>
                </div>
            </div>
        </nav>

        <div class="portal-main">
            <div class="portal-container max-w-4xl mx-auto">
                <div class="portal-card overflow-hidden">
                    <div class="portal-welcome p-8 relative">
                        <div class="flex items-center relative z-10">
                            <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center mr-6 shadow-lg">
                                <span class="font-bold text-2xl text-uthm-blue">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                                <p class="text-blue-100/90 mt-0.5">{{ $user->uthm_id ?? 'UTHM Member' }}</p>
                                <span class="inline-flex items-center mt-2 px-3 py-1 portal-badge text-xs">
                                    <i class="fas fa-user-tag mr-1.5"></i>
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <h3 class="portal-section-title mb-4 flex items-center gap-2 text-base">
                                    <i class="fas fa-user-circle text-uthm-blue"></i> Personal Information
                                </h3>
                                <div class="space-y-4">
                                    <div class="p-3 bg-gray-50 rounded-xl">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-0.5">Full Name</p>
                                        <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-xl">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-0.5">UTHM ID</p>
                                        <p class="font-semibold text-gray-900">{{ $user->uthm_id ?? 'Not provided' }}</p>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-xl">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-0.5">Email</p>
                                        <p class="font-semibold text-gray-900">{{ $user->email ?? 'Not provided' }}</p>
                                    </div>
                                    @if($user->faculty)
                                    <div class="p-3 bg-gray-50 rounded-xl">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-0.5">Faculty</p>
                                        <p class="font-semibold text-gray-900">{{ $user->faculty }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <h3 class="portal-section-title mb-4 flex items-center gap-2 text-base">
                                    <i class="fas fa-shield-alt text-uthm-blue"></i> Account Information
                                </h3>
                                <div class="space-y-4">
                                    <div class="p-3 bg-gray-50 rounded-xl">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Role</p>
                                        <span class="inline-block px-3 py-1 badge-{{ $user->role }} rounded-full text-xs font-semibold">
                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-xl">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-0.5">Account Created</p>
                                        <p class="font-semibold text-gray-900">{{ $user->created_at->format('F d, Y') }}</p>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-xl">
                                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-0.5">Last Login</p>
                                        <p class="font-semibold text-gray-900">{{ now()->format('F d, Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-100 flex flex-wrap gap-3">
                            <a href="{{ route('profile.edit') }}" class="portal-btn-primary inline-flex items-center text-sm">
                                <i class="fas fa-edit mr-2"></i> Edit Profile
                            </a>
                            <a href="{{ route('settings') }}" class="inline-flex items-center px-5 py-2 bg-white border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors text-sm">
                                <i class="fas fa-cog mr-2"></i> Settings
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-5 py-2 bg-red-50 text-red-700 font-semibold rounded-xl hover:bg-red-100 transition-colors text-sm">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="portal-card p-5">
                        <div class="flex items-center gap-3">
                            <div class="portal-stat-icon bg-blue-100"><i class="fas fa-bullhorn text-blue-600"></i></div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Announcements</p>
                                <p class="text-xl font-bold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="portal-card p-5">
                        <div class="flex items-center gap-3">
                            <div class="portal-stat-icon bg-green-100"><i class="fas fa-calendar-check text-green-600"></i></div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Events Joined</p>
                                <p class="text-xl font-bold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="portal-card p-5">
                        <div class="flex items-center gap-3">
                            <div class="portal-stat-icon bg-purple-100"><i class="fas fa-users text-purple-600"></i></div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Groups</p>
                                <p class="text-xl font-bold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
