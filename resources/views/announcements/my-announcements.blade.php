<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Announcements - UTHM Bulletin Board</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom colors */
        .uthm-blue { color: #0056a6; }
        .bg-uthm-blue { background-color: #0056a6; }
        .bg-uthm-blue-light { background-color: #e6f0fa; }
        
        /* Badge styles */
        .badge-official {
            background-color: #dcfce7;
            color: #166534;
        }
        .badge-unofficial {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-urgent {
            background-color: #fee2e2;
            color: #dc2626;
        }
        .badge-important {
            background-color: #fef3c7;
            color: #d97706;
        }
        .badge-academic {
            background-color: #dbeafe;
            color: #1d4ed8;
        }
        .badge-events {
            background-color: #f3e8ff;
            color: #7c3aed;
        }
        .badge-general {
            background-color: #f0f9ff;
            color: #0369a1;
        }

        /* Role badges */
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
        
        /* Status badges */
        .status-published {
            background-color: #10b981;
            color: white;
        }
        .status-pending-approval {
            background-color: #fef3c7;
            color: #d97706;
        }
        .status-rejected {
            background-color: #fee2e2;
            color: #dc2626;
        }
        .status-draft {
            background-color: #6b7280;
            color: white;
        }
        .status-expired {
            background-color: #9ca3af;
            color: white;
        }

        /* Custom sidebar styles */
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
        
        /* Action buttons */
        .btn-edit {
            background-color: #3b82f6;
            color: white;
        }
        .btn-delete {
            background-color: #ef4444;
            color: white;
        }
        .btn-view {
            background-color: #10b981;
            color: white;
        }
        
        /* Dropdown animation */
        .dropdown-enter {
            opacity: 0;
            transform: scale(0.95);
        }
        .dropdown-enter-active {
            opacity: 1;
            transform: scale(1);
            transition: opacity 0.2s, transform 0.2s;
        }
        .dropdown-leave {
            opacity: 1;
            transform: scale(1);
        }
        .dropdown-leave-active {
            opacity: 0;
            transform: scale(0.95);
            transition: opacity 0.2s, transform 0.2s;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar Dashboard Navigation -->
        <div id="sidebar" class="sidebar-collapsed bg-white shadow-lg h-screen fixed left-0 top-0 overflow-y-auto z-40 sidebar-transition">
            <!-- Sidebar Header -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="bg-green-600 text-white p-2 rounded-lg shrink-0">
                            <i class="fas fa-user-graduate text-lg"></i>
                        </div>
                        <div class="sidebar-text">
                            <h2 class="font-bold uthm-blue">UTHM Bulletin</h2>
                            <p class="text-xs text-gray-500">My Announcements</p>
                        </div>
                    </div>
                    
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
                            <span class="font-bold uthm-blue">{{ strtoupper(substr($user?->name ?? 'G', 0, 1)) }}</span>
                        </div>
                        <div class="sidebar-text">
                            <h3 class="font-medium text-gray-900">{{ $user?->name ?? 'Guest User' }}</h3>
                            <p class="text-xs text-gray-500">{{ $user?->uthm_id ?? 'UTHM Member' }}</p>
                            @if($user?->role)
                                <span class="mt-1 inline-block px-2 py-1 text-xs rounded-full badge-{{ $user->role }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>

            <!-- Dashboard Navigation -->
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                            <div class="shrink-0">
                                <i class="fas fa-home w-5 h-5"></i>
                            </div>
                            <span class="sidebar-text ml-3">Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('announcements.index') }}" 
                              class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                            <div class="shrink-0">
                                <i class="fas fa-bullhorn w-5 h-5"></i>
                            </div>
                            <span class="sidebar-text ml-3">Announcements</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('announcements.my-announcements') }}" 
                           class="flex items-center p-3 rounded-lg bg-uthm-blue-light text-uthm-blue">
                            <div class="shrink-0">
                                <i class="fas fa-file-alt w-5 h-5"></i>
                            </div>
                            <span class="sidebar-text ml-3">My Announcements</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('calendar') }}"
                           class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                            <div class="shrink-0">
                                <i class="fas fa-calendar-alt w-5 h-5"></i>
                            </div>
                            <span class="sidebar-text ml-3">Calendar</span>
                        </a>
                    </li>

                    <li>
                        <a href="#" 
                           class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                            <div class="shrink-0">
                                <i class="fas fa-calendar-check w-5 h-5"></i>
                            </div>
                            <span class="sidebar-text ml-3">Events</span>
                        </a>
                    </li>

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

        <!-- Main Content Wrapper -->
        <div class="content-collapsed min-h-screen content-transition flex-1">
            <!-- Top Navigation Bar -->
            <nav class="bg-white shadow">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <a href="{{ route('announcements.index') }}" class="flex items-center mr-8">
                                <i class="fas fa-arrow-left text-gray-600 mr-2"></i>
                                <span class="text-gray-700">Back to Announcements</span>
                            </a>
                            <h1 class="text-xl font-bold text-gray-900">My Announcements</h1>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('announcements.create') }}" 
                               class="bg-uthm-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                                <i class="fas fa-plus mr-2"></i>
                                New Announcement
                            </a>
                            
                            <!-- User Menu Dropdown (from student dashboard) -->
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
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <!-- Page Header -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-900">Manage Your Announcements</h2>
                        <p class="mt-2 text-gray-600">View, edit, and manage all announcements you've created</p>
                        
                        <!-- Stats -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-3 rounded-lg mr-4">
                                        <i class="fas fa-bullhorn text-blue-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Total Announcements</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $announcements->total() }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-center">
                                    <div class="bg-green-100 p-3 rounded-lg mr-4">
                                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Official Announcements</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $announcements->where('is_official', true)->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-center">
                                    <div class="bg-purple-100 p-3 rounded-lg mr-4">
                                        <i class="fas fa-eye text-purple-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Total Views</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ $totalViews ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Tabs -->
<div class="mb-6 bg-white rounded-lg shadow p-4">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('announcements.my-announcements') }}?status=all" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request('status', 'all') == 'all' ? 'bg-uthm-blue text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fas fa-list mr-2"></i>All
            <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ request('status', 'all') == 'all' ? 'bg-white bg-opacity-30 text-white' : 'bg-gray-200 text-gray-600' }}">{{ $totalCount ?? 0 }}</span>
        </a>
        
        <a href="{{ route('announcements.my-announcements') }}?status=published" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request('status') == 'published' ? 'bg-green-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fas fa-check-circle mr-2"></i>Published
            <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ request('status') == 'published' ? 'bg-white bg-opacity-30 text-white' : 'bg-gray-200 text-gray-600' }}">{{ $publishedCount ?? 0 }}</span>
        </a>

        <a href="{{ route('announcements.my-announcements') }}?status=expired" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request('status') == 'expired' ? 'bg-gray-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fas fa-hourglass-end mr-2"></i>Expired
            <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ request('status') == 'expired' ? 'bg-white bg-opacity-30 text-white' : 'bg-gray-200 text-gray-600' }}">{{ $expiredCount ?? 0 }}</span>
        </a>
        
        <a href="{{ route('announcements.my-announcements') }}?status=draft" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request('status') == 'draft' ? 'bg-gray-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fas fa-edit mr-2"></i>Drafts
            <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ request('status') == 'draft' ? 'bg-white bg-opacity-30 text-white' : 'bg-gray-200 text-gray-600' }}">{{ $draftCount ?? 0 }}</span>
        </a>
        
        <a href="{{ route('announcements.my-announcements') }}?status=pending_verification" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request('status') == 'pending_verification' ? 'bg-yellow-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fas fa-clock mr-2"></i>Pending Approval
            <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ request('status') == 'pending_verification' ? 'bg-white bg-opacity-30 text-white' : 'bg-gray-200 text-gray-600' }}">{{ $pendingCount ?? 0 }}</span>
        </a>
        
        <a href="{{ route('announcements.my-announcements') }}?status=rejected" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request('status') == 'rejected' ? 'bg-red-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fas fa-times-circle mr-2"></i>Rejected
            <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ request('status') == 'rejected' ? 'bg-white bg-opacity-30 text-white' : 'bg-gray-200 text-gray-600' }}">{{ $rejectedCount ?? 0 }}</span>
        </a>

        <a href="{{ route('announcements.my-announcements') }}?status=banned" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request('status') == 'banned' ? 'bg-red-800 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fas fa-ban mr-2"></i>Banned
            <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ request('status') == 'banned' ? 'bg-white bg-opacity-30 text-white' : 'bg-gray-200 text-gray-600' }}">{{ $bannedCount ?? 0 }}</span>
        </a>
    </div>
</div>

                    <!-- Announcements List -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        @if($announcements->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($announcements as $announcement)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $announcement->title }}
                                                    </div>
                                                    @if(($announcement->status === 'banned' || $announcement->is_banned) && $announcement->ban_reason)
                                                        <p class="text-xs text-red-600 mt-1">{{ \Illuminate\Support\Str::limit($announcement->ban_reason, 80) }}</p>
                                                    @endif
                                                    <div class="text-sm text-gray-500">
                                                        @if($announcement->is_official)
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs badge-official">
                                                                <i class="fas fa-check-circle mr-1"></i> Official
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs badge-unofficial">
                                                                <i class="fas fa-users mr-1"></i> Unofficial
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($announcement->status == 'published')
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            <i class="fas fa-check-circle mr-1"></i> Published
                                                        </span>
                                                    @elseif($announcement->status == 'draft')
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            <i class="fas fa-edit mr-1"></i> Draft
                                                        </span>
                                                    @elseif($announcement->status == 'pending_verification')
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                            <i class="fas fa-clock mr-1"></i> Pending Approval
                                                        </span>
                                                    @elseif($announcement->status == 'rejected')
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            <i class="fas fa-times-circle mr-1"></i> Rejected
                                                        </span>
                                                    @elseif($announcement->status == 'expired')
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800">
                                                            <i class="fas fa-hourglass-end mr-1"></i> Expired
                                                        </span>
                                                        @if($announcement->expiry_date)
                                                            <p class="text-xs text-gray-500 mt-1">Expired {{ $announcement->expiry_date->format('M d, Y') }}</p>
                                                        @endif
                                                    @elseif($announcement->status == 'banned' || $announcement->is_banned)
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-200 text-red-900">
                                                            <i class="fas fa-ban mr-1"></i> Banned
                                                        </span>
                                                    @else
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            <i class="fas fa-question-circle mr-1"></i> {{ ucfirst($announcement->status) }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full badge-{{ $announcement->category }}">
                                                        {{ ucfirst($announcement->category) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $announcement->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="announcement-actions-cell px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        @if(in_array($announcement->status, ['published', 'expired', 'pending_verification', 'draft']))
                                                            @include('announcements.partials.calendar-dropdown', [
                                                                'announcement' => $announcement,
                                                                'compact' => true,
                                                            ])
                                                        @endif
                                                        <a href="{{ route('announcements.show', $announcement) }}" 
                                                           class="inline-flex items-center px-3 py-1 border border-transparent text-xs rounded btn-view hover:bg-green-700">
                                                            <i class="fas fa-eye mr-1"></i> View
                                                        </a>
                                                        @if($announcement->status !== 'banned' && !$announcement->is_banned)
                                                        <a href="{{ route('announcements.edit', $announcement) }}" 
                                                           class="inline-flex items-center px-3 py-1 border border-transparent text-xs rounded btn-edit hover:bg-blue-700">
                                                            <i class="fas fa-edit mr-1"></i> Edit
                                                        </a>
                                                        @endif
                                                        @if($announcement->status === 'published' && in_array(auth()->user()->role, ['staff', 'club_admin']))
                                                            <form action="{{ route('announcements.toggle-featured', $announcement) }}" 
                                                                  method="POST" 
                                                                  class="inline">
                                                                @csrf
                                                                <button type="submit" 
                                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs rounded {{ $announcement->is_featured ? 'text-amber-600 bg-amber-50 hover:bg-amber-100' : 'text-gray-600 bg-gray-50 hover:bg-gray-100' }}">
                                                                    <i class="fas fa-star mr-1"></i> {{ $announcement->is_featured ? 'Unfeature' : 'Feature' }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <form action="{{ route('announcements.destroy', $announcement) }}" 
                                                              method="POST" 
                                                              class="inline"
                                                              onsubmit="return confirm('Are you sure you want to delete this announcement?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs rounded btn-delete hover:bg-red-700">
                                                                <i class="fas fa-trash mr-1"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-12">
                                                    <div class="text-center">
                                                        <div class="inline-block p-6 bg-gray-100 rounded-full mb-4">
                                                            <i class="fas fa-file-alt text-gray-400 text-4xl"></i>
                                                        </div>
                                                        <h3 class="text-xl font-medium text-gray-900 mb-2">No announcements yet</h3>
                                                        <p class="text-gray-600 mb-6">
                                                            @if(request('status') == 'published')
                                                                You haven't published any announcements yet.
                                                            @elseif(request('status') == 'draft')
                                                                You don't have any draft announcements.
                                                            @else
                                                                You haven't created any announcements yet.
                                                            @endif
                                                        </p>
                                                        <a href="{{ route('announcements.create') }}" 
                                                           class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                                                            <i class="fas fa-plus-circle mr-2 text-lg"></i>
                                                            Create Your First Announcement
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            @if($announcements->hasPages())
                                <div class="px-6 py-4 border-t border-gray-200">
                                    {{ $announcements->appends(request()->query())->links() }}
                                </div>
                            @endif
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-12">
                                <div class="inline-block p-6 bg-gray-100 rounded-full mb-4">
                                    <i class="fas fa-file-alt text-gray-400 text-4xl"></i>
                                </div>
                                <h3 class="text-xl font-medium text-gray-900 mb-2">No announcements yet</h3>
                                <p class="text-gray-600 mb-6">
                                    @if(request('status') == 'published')
                                        You haven't published any announcements yet.
                                    @elseif(request('status') == 'draft')
                                        You don't have any draft announcements.
                                    @else
                                        You haven't created any announcements yet.
                                    @endif
                                </p>
                                <a href="{{ route('announcements.create') }}" 
                                   class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-plus-circle mr-2 text-lg"></i>
                                    Create Your First Announcement
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <h3 class="font-bold text-gray-900 text-lg mb-2">Need help?</h3>
                            <p class="text-gray-600 text-sm mb-4">Learn how to create effective announcements that reach your audience.</p>
                            <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                <i class="fas fa-book mr-2"></i>
                                View Guidelines
                            </a>
                        </div>
                        
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                            <h3 class="font-bold text-gray-900 text-lg mb-2">Make it official</h3>
                            <p class="text-gray-600 text-sm mb-4">Contact admin/staff to verify your announcement and make it official.</p>
                            <a href="mailto:admin@uthm.edu.my" class="inline-flex items-center text-green-600 hover:text-green-800">
                                <i class="fas fa-envelope mr-2"></i>
                                Request Verification
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-6 mt-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center text-gray-500 text-sm">
                        <p>UTHM Digital Bulletin Board &copy; {{ date('Y') }}</p>
                        <p class="mt-1">Manage your announcements and stay connected with the community</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const toggleIcon = document.getElementById('toggle-icon');
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            
            // Load sidebar state from localStorage
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
            
            // User Menu Dropdown Toggle
            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                        userMenu.classList.add('hidden');
                    }
                });
            }
            
            function expandSidebar() {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                
                if (toggleIcon) {
                    toggleIcon.style.transform = 'rotate(180deg)';
                }
                
                localStorage.setItem('sidebarExpanded', 'true');
            }
            
            function collapseSidebar() {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
                
                if (toggleIcon) {
                    toggleIcon.style.transform = 'rotate(0deg)';
                }
                
                localStorage.setItem('sidebarExpanded', 'false');
            }
            
           // Status filter tab styling - Updated for all 5 tabs
const statusTabs = document.querySelectorAll('a[href*="status="]');
statusTabs.forEach(tab => {
    tab.addEventListener('click', function(e) {
        statusTabs.forEach(t => {
            t.classList.remove('bg-uthm-blue', 'text-white', 'bg-green-600', 'bg-gray-600', 'bg-yellow-600', 'bg-red-600');
            t.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
        });
        
        const href = this.getAttribute('href');
        if (href.includes('status=published')) {
            this.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            this.classList.add('bg-green-600', 'text-white');
        } else if (href.includes('status=draft')) {
            this.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            this.classList.add('bg-gray-600', 'text-white');
        } else if (href.includes('status=pending_verification')) {
            this.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            this.classList.add('bg-yellow-600', 'text-white');
        } else if (href.includes('status=rejected')) {
            this.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            this.classList.add('bg-red-600', 'text-white');
        } else {
            this.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
            this.classList.add('bg-uthm-blue', 'text-white');
        }
    });
});
            
            // Mobile sidebar handling
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    if (sidebar.classList.contains('mobile-open')) {
                        sidebar.classList.remove('mobile-open');
                    } else {
                        sidebar.classList.add('mobile-open');
                    }
                });
            }
            
            if (window.innerWidth < 768) {
                sidebar.style.transform = 'translateX(-100%)';
            }
            
            window.addEventListener('resize', function() {
                if (window.innerWidth < 768) {
                    if (!sidebar.classList.contains('mobile-open')) {
                        sidebar.style.transform = 'translateX(-100%)';
                    }
                } else {
                    sidebar.style.transform = 'translateX(0)';
                }
            });
        });
    </script>
    @include('announcements.partials.calendar-assets')
</body>
</html>