<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - UTHM Bulletin Board</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('layouts.partials.portal-head')
    <style>
        /* Announcement page-specific styles */
        .badge-urgent { background-color: #fee2e2; color: #dc2626; }
        .badge-important { background-color: #fef3c7; color: #d97706; }
        .badge-academic { background-color: #dbeafe; color: #1d4ed8; }
        .badge-events { background-color: #f3e8ff; color: #7c3aed; }
        .badge-general { background-color: #f0f9ff; color: #0369a1; }
        .badge-official { background-color: #dcfce7; color: #166534; }
        .badge-unofficial { background-color: #fef3c7; color: #92400e; }
        .badge-pending { background-color: #fef3c7; color: #d97706; }
        .badge-draft { background-color: #e5e7eb; color: #6b7280; }
        .badge-rejected { background-color: #fee2e2; color: #dc2626; }

        .floating-add-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 86, 166, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 86, 166, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(0, 86, 166, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 86, 166, 0); }
        }

        @media (max-width: 768px) {
            .floating-add-btn { bottom: 5rem; right: 1.5rem; }
        }

        .tab-active { background-color: #0056a6; color: white; }
        .tab-inactive { background-color: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
        .tab-inactive:hover { background-color: #e5e7eb; }

        .filter-btn-active { background-color: #0056a6 !important; color: white !important; }
        .filter-btn-inactive { background-color: #f3f4f6; color: #6b7280; }
        .filter-btn-inactive:hover { background-color: #e5e7eb; }

        .modal-enter { opacity: 0; transform: scale(0.95); }
        .modal-enter-active { opacity: 1; transform: scale(1); transition: opacity 0.2s, transform 0.2s; }

        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body class="portal-body">
    @include('layouts.partials.portal-sidebar', ['user' => $user ?? Auth::user()])

    <!-- Main Content -->
    <div id="main-content" class="content-collapsed min-h-screen content-transition">
        <!-- Top Navigation Bar -->
        <nav class="bg-white shadow">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">Announcements</h1>
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-600">All Announcements</span>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="relative hidden md:block">
                            <button id="quick-create-button" class="bg-uthm-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                                <i class="fas fa-plus mr-2"></i>
                                Create Announcement
                            </button>
                            <div id="quick-create-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden">
                                <a href="{{ route('announcements.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-bullhorn mr-2"></i> New Announcement
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-calendar-check mr-2"></i> New Event
                                </a>
                            </div>
                        </div>
                        
                        @include('layouts.partials.notification-bell')
                        
                        <div class="relative">
                            <button id="user-menu-button" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100">
                                <div class="w-8 h-8 bg-uthm-blue-light rounded-full flex items-center justify-center">
                                    <span class="font-bold uthm-blue">{{ strtoupper(substr($user?->name ?? 'G', 0, 1)) }}</span>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-gray-900">{{ $user?->name ?? 'Guest User' }}</p>
                                    <p class="text-xs text-gray-500">{{ $user?->uthm_id ?? 'UTHM Member' }}</p>
                                    @if($user?->role)
                                        <span class="inline-block px-2 py-1 text-xs rounded-full badge-{{ $user->role }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    @endif
                                </div>
                                <i class="fas fa-chevron-down text-gray-500"></i>
                            </button>
                            
                            <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden">
                                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> My Profile
                                </a>
                                <a href="{{ route('announcements.my-announcements') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-file-alt mr-2"></i> My Announcements
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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

        @include('layouts.partials.portal-content-open')
                <!-- Page Header -->
                <div class="mb-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="portal-section-title text-xl">Announcements</h2>
                            <p class="mt-1 text-gray-500 text-sm">Stay updated with the latest news and announcements from UTHM</p>
                            <div class="mt-2 text-sm text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                @if(in_array($user->role ?? 'guest', ['admin', 'staff']))
                                    As {{ $user->role }}, you can approve or reject pending announcements.
                                @else
                                    All users can create announcements. Official announcements are verified by admin/staff.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Announcement Type Navigation -->
                <div class="mb-3 portal-card">
                    <div class="flex flex-wrap gap-4">
                        <h3 class="text-lg font-medium text-gray-900 self-center">View:</h3>
                        <div class="flex space-x-2 flex-wrap gap-2">
                            <a href="{{ route('announcements.index') }}" 
                               class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('announcements.index') ? 'tab-active' : 'tab-inactive' }}">
                                All Announcements
                            </a>
                            <a href="{{ route('announcements.my-announcements') }}" 
                               class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('announcements.my-announcements') ? 'tab-active' : 'tab-inactive' }}">
                                <i class="fas fa-user mr-2"></i>My Announcements
                            </a>
                            <a href="{{ route('announcements.published') }}" 
                               class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('announcements.published') ? 'tab-active' : 'tab-inactive' }}">
                                <i class="fas fa-check-circle mr-2"></i>Published
                            </a>
                            <a href="{{ route('announcements.drafts') }}" 
                               class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('announcements.drafts') ? 'tab-active' : 'tab-inactive' }}">
                                <i class="fas fa-edit mr-2"></i>Drafts
                            </a>
                            {{-- Verification Queue removed: moderators review posts directly on the announcement page. --}}
                        </div>
                    </div>
                </div>

                <!-- Create Announcement Card -->
                @auth
                <div class="mb-3 bg-gradient-to-r from-blue-50 to-uthm-blue-light border border-blue-200 rounded-xl shadow-sm p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-uthm-blue text-white p-3 rounded-lg mr-4">
                                <i class="fas fa-bullhorn text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">Share your announcement!</h3>
                                <p class="text-gray-600 text-sm mt-1">
                                    @if(in_array($user->role ?? 'guest', ['admin', 'staff']))
                                        As {{ $user->role }}, your announcements will be marked as official.
                                    @else
                                        Create an announcement to inform the UTHM community. Admin/staff can verify to make it official.
                                    @endif
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('announcements.create') }}" 
                           class="inline-flex items-center px-5 py-3 bg-uthm-blue text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow">
                            <i class="fas fa-plus mr-2"></i>
                            Create Announcement
                        </a>
                    </div>
                </div>
                @endauth

                <!-- High-Priority Notice Board -->
                @if(isset($highPriorityAnnouncements) && $highPriorityAnnouncements->count() > 0)
                <div class="mb-3 pinned-notice-board rounded-xl p-5 shadow-sm border">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="bg-red-500 text-white p-2 rounded-lg animate-pulse">
                            <i class="fas fa-bullhorn text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">📌 High-Priority Notice Board</h3>
                            <p class="text-gray-600 text-sm">Please pay immediate attention to these urgent and important updates.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($highPriorityAnnouncements as $hpAnnouncement)
                            <div class="bg-white/80 dark:bg-slate-800/80 rounded-lg p-4 border border-red-100 dark:border-red-900/30 hover:shadow-md transition-shadow relative overflow-hidden flex flex-col justify-between">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-red-500/5 dark:bg-red-500/10 rounded-full -mr-8 -mt-8 flex items-center justify-center">
                                    @if($hpAnnouncement->priority === 'urgent')
                                        <i class="fas fa-exclamation-circle text-red-500/20 text-3xl animate-pulse"></i>
                                    @else
                                        <i class="fas fa-star text-amber-500/20 text-3xl animate-spin-slow"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2 mb-2">
                                        @if($hpAnnouncement->priority === 'urgent')
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">Urgent</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">Important</span>
                                        @endif
                                        <span class="text-xs text-gray-500">{{ $hpAnnouncement->created_at->diffForHumans() }}</span>
                                    </div>
                                    <h4 class="font-bold text-gray-900 text-md mb-1 line-clamp-1">{{ $hpAnnouncement->title }}</h4>
                                    <p class="text-gray-600 text-sm line-clamp-2 mb-3">{{ Str::limit($hpAnnouncement->content, 120) }}</p>
                                </div>
                                <div class="flex items-center justify-between border-t border-gray-100 dark:border-slate-700/50 pt-2 mt-auto">
                                    <span class="text-xs text-gray-500">By {{ $hpAnnouncement->author->name ?? 'Anonymous' }}</span>
                                    <a href="{{ route('announcements.show', $hpAnnouncement) }}" class="text-xs font-semibold text-uthm-blue hover:text-blue-700">View Notice <i class="fas fa-chevron-right ml-1"></i></a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Filters -->
                <div class="mb-3 portal-card">
                    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                        <div class="flex flex-wrap gap-2 items-center">
                            <!-- Category Group -->
                            <div class="flex flex-wrap gap-1 items-center bg-gray-50 dark:bg-slate-800 p-1 rounded-lg border border-gray-200 dark:border-slate-700">
                                <button onclick="filterAnnouncements('all', undefined, undefined)" 
                                        class="px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-all bg-uthm-blue text-white">
                                    All Categories
                                </button>
                                <button onclick="filterAnnouncements('academic', undefined, undefined)" 
                                        class="px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-category bg-blue-50 text-blue-700 hover:bg-blue-100">
                                    <i class="fas fa-graduation-cap mr-1"></i>Academic
                                </button>
                                <button onclick="filterAnnouncements('events', undefined, undefined)" 
                                        class="px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-category bg-purple-50 text-purple-700 hover:bg-purple-100">
                                    <i class="fas fa-calendar-alt mr-1"></i>Events
                                </button>
                                <button onclick="filterAnnouncements('general', undefined, undefined)" 
                                        class="px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-category bg-sky-50 text-sky-700 hover:bg-sky-100">
                                    <i class="fas fa-newspaper mr-1"></i>General
                                </button>
                                <button onclick="filterAnnouncements('club', undefined, undefined)" 
                                        class="px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-category bg-green-50 text-green-700 hover:bg-green-100">
                                    <i class="fas fa-users mr-1"></i>Club
                                </button>
                            </div>

                            <!-- Priority Group -->
                            <div class="flex flex-wrap gap-1 items-center bg-gray-50 dark:bg-slate-800 p-1 rounded-lg border border-gray-200 dark:border-slate-700 ml-0 sm:ml-2">
                                <span class="text-xs text-gray-500 px-2 font-medium">Priority:</span>
                                <button onclick="filterAnnouncements(undefined, 'urgent', undefined)" 
                                        class="px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-priority bg-red-50 text-red-700 hover:bg-red-100">
                                    <i class="fas fa-exclamation-circle mr-1 animate-pulse"></i>Urgent
                                </button>
                                <button onclick="filterAnnouncements(undefined, 'important', undefined)" 
                                        class="px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-priority bg-amber-50 text-amber-700 hover:bg-amber-100">
                                    <i class="fas fa-star mr-1 text-amber-500"></i>Important
                                </button>
                            </div>
                            
                            <!-- Official/Unofficial Group -->
                            <div class="flex flex-wrap gap-1 items-center bg-gray-50 dark:bg-slate-800 p-1 rounded-lg border border-gray-200 dark:border-slate-700 ml-0 sm:ml-2">
                                <span class="text-xs text-gray-500 px-2 font-medium">Type:</span>
                                <button onclick="filterAnnouncements(undefined, undefined, 'official')" 
                                        class="px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-type bg-emerald-50 text-emerald-700 hover:bg-emerald-100">
                                    <i class="fas fa-check-circle mr-1"></i>Official
                                </button>
                                <button onclick="filterAnnouncements(undefined, undefined, 'unofficial')" 
                                        class="px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-type bg-yellow-50 text-yellow-700 hover:bg-yellow-100">
                                    <i class="fas fa-users mr-1"></i>Unofficial
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="relative w-full">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" 
                                       id="search-input"
                                       placeholder="Search announcements..." 
                                       class="pl-10 pr-4 py-2 border border-gray-300 dark:border-slate-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-64">
                            </div>
                        </div>
                    </div>
                    
                    <div id="active-filters" class="mt-4 flex flex-wrap gap-2 hidden">
                        <div class="text-sm text-gray-600 mr-2">Active filters:</div>
                    </div>
                </div>

                <!-- Announcements Grid -->
                <div id="announcements-grid" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @forelse($announcements as $announcement)
                        <div class="announcement-card {{ $announcement->priority ?? 'normal' }} bg-white rounded-xl shadow hover:shadow-lg transition-shadow border border-gray-200" 
                             data-id="{{ $announcement->id }}"
                             data-type="{{ $announcement->is_official ? 'official' : 'unofficial' }}"
                             data-category="{{ $announcement->category }}"
                             data-priority="{{ $announcement->priority }}"
                             data-status="{{ $announcement->status }}">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <!-- Status Badge (for pending/draft/rejected) -->
                                        @if($announcement->status === 'pending_verification')
                                            <span class="px-3 py-1 rounded-full text-xs font-medium badge-pending">
                                                <i class="fas fa-clock mr-1"></i> Pending Approval
                                            </span>
                                        @elseif($announcement->status === 'draft')
                                            <span class="px-3 py-1 rounded-full text-xs font-medium badge-draft">
                                                <i class="fas fa-edit mr-1"></i> Draft
                                            </span>
                                        @elseif($announcement->status === 'rejected')
                                            <span class="px-3 py-1 rounded-full text-xs font-medium badge-rejected">
                                                <i class="fas fa-times-circle mr-1"></i> Rejected
                                            </span>
                                        @endif
                                        
                                        <!-- Official/Unofficial Badge -->
                                        @if($announcement->is_official)
                                            <span class="ml-2 px-3 py-1 rounded-full text-xs font-medium badge-official">
                                                <i class="fas fa-check-circle mr-1"></i> Official
                                            </span>
                                        @else
                                            <span class="ml-2 px-3 py-1 rounded-full text-xs font-medium badge-unofficial">
                                                <i class="fas fa-users mr-1"></i> Unofficial
                                            </span>
                                        @endif
                                        
                                        <!-- Priority Badge -->
                                        @if($announcement->priority === 'urgent')
                                            <span class="ml-2 px-3 py-1 rounded-full text-xs font-medium badge-urgent">
                                                <i class="fas fa-exclamation-circle mr-1"></i> Urgent
                                            </span>
                                        @elseif($announcement->priority === 'important')
                                            <span class="ml-2 px-3 py-1 rounded-full text-xs font-medium badge-important">
                                                <i class="fas fa-star mr-1"></i> Important
                                            </span>
                                        @endif
                                        
                                        <!-- Category Badge -->
                                        <span class="ml-2 px-3 py-1 rounded-full text-xs font-medium badge-{{ $announcement->category }}">
                                            {{ ucfirst($announcement->category) }}
                                        </span>
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        <i class="far fa-clock mr-1"></i> 
                                        {{ $announcement->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                                
                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $announcement->title }}</h3>
                                
                                <p class="text-gray-600 mb-4 line-clamp-2">
                                    {{ Str::limit($announcement->content, 150) }}
                                </p>
                                
                                @if($announcement->status === 'rejected' && $announcement->rejection_reason)
                                    <div class="mb-4 p-3 bg-red-50 rounded-lg border border-red-200">
                                        <p class="text-xs text-red-600 font-medium">Rejection Reason:</p>
                                        <p class="text-sm text-red-700">{{ $announcement->rejection_reason }}</p>
                                    </div>
                                @endif
                                
                                <div class="announcement-card-footer mt-4 pt-4 border-t border-gray-100">
                                    <div class="flex items-center text-sm text-gray-500 min-w-0 mb-3">
                                        <div class="w-8 h-8 shrink-0 bg-gray-200 rounded-full flex items-center justify-center mr-2">
                                            <span class="font-bold text-gray-600">{{ strtoupper(substr($announcement->author->name ?? 'A', 0, 1)) }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block truncate">{{ $announcement->author->name ?? 'Anonymous' }}</span>
                                            @if($announcement->author)
                                                <span class="inline-block mt-0.5 px-2 py-0.5 text-xs rounded-full badge-{{ $announcement->author->role }}">
                                                    {{ ucfirst($announcement->author->role) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @include('announcements.partials.announcement-card-actions', [
                                        'announcement' => $announcement,
                                        'user' => $user,
                                        'showApprove' => true,
                                    ])
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 col-span-2">
                            <div class="inline-block p-6 bg-gray-100 rounded-full mb-4">
                                <i class="fas fa-bullhorn text-gray-400 text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-medium text-gray-900 mb-2">No announcements yet</h3>
                            <p class="text-gray-600 mb-6">When announcements are created, they will appear here.</p>
                            @auth
                            <a href="{{ route('announcements.create') }}" 
                               class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors shadow-lg">
                                <i class="fas fa-plus-circle mr-2 text-lg"></i>
                                Create Your First Announcement
                            </a>
                            @endauth
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($announcements->hasPages())
                    <div class="mt-8">
                        <div class="bg-white rounded-xl shadow p-4">
                            {{ $announcements->links() }}
                        </div>
                    </div>
                @endif
        @include('layouts.partials.portal-content-close')

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-4">
            <div class="portal-topbar-wrap max-w-full mx-auto">
                <div class="text-center text-gray-500 text-sm">
                    <p>UTHM Digital Bulletin Board &copy; {{ date('Y') }}. All rights reserved.</p>
                    <p class="mt-1">All authenticated users can create announcements. Official announcements are verified by admin/staff.</p>
                </div>
            </div>
        </footer>

        <!-- Floating Add Button -->
        @auth
        <a href="{{ route('announcements.create') }}" 
           class="floating-add-btn bg-green-600 text-white p-4 rounded-full shadow-lg hover:bg-green-700 transition-colors hover:shadow-xl">
            <i class="fas fa-plus text-2xl"></i>
        </a>
        @endauth
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Approve Announcement</h3>
                <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <p class="text-gray-600 mb-4">Are you sure you want to approve "<span id="approveTitle" class="font-semibold"></span>"?</p>
            <p class="text-sm text-gray-500 mb-6">Approved announcements will be published and visible to all users.</p>
            <div class="flex justify-end gap-3">
                <button onclick="closeApproveModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button onclick="confirmApprove()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Yes, Approve
                </button>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Reject Announcement</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <p class="text-gray-600 mb-4">Reject "<span id="rejectTitle" class="font-semibold"></span>"</p>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for rejection *</label>
                <textarea id="rejectionReason" rows="3" placeholder="Please provide a reason for rejecting this announcement..." 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button onclick="confirmReject()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Yes, Reject
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast-notification hidden">
        <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span id="toastMessage"></span>
        </div>
    </div>

    <script>
        let currentAnnouncementId = null;
        let currentAnnouncementTitle = '';

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize sidebar
            initializeSidebar();
            
            // Initialize user menu
            initializeUserMenu();
            
            // Initialize quick create menu
            initializeQuickCreateMenu();
            
            // Initialize search
            initializeSearch();
            
            // Initialize filters
            initializeFilters();
        });

        function initializeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const toggleIcon = document.getElementById('toggle-icon');
            
            const isSidebarExpanded = localStorage.getItem('sidebarExpanded') === 'true';
            if (isSidebarExpanded) {
                expandSidebar();
            } else {
                collapseSidebar();
            }
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (sidebar.classList.contains('sidebar-expanded')) {
                        collapseSidebar();
                    } else {
                        expandSidebar();
                    }
                });
            }
            
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
            
            window.addEventListener('resize', function() {
                if (window.innerWidth < 768) {
                    if (!sidebar.classList.contains('mobile-open')) {
                        sidebar.style.transform = 'translateX(-100%)';
                    }
                } else {
                    sidebar.style.transform = 'translateX(0)';
                }
            });
        }

        function initializeUserMenu() {
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            
            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });
            }
            
            document.addEventListener('click', function() {
                if (userMenu) userMenu.classList.add('hidden');
            });
        }

        function initializeQuickCreateMenu() {
            const quickCreateButton = document.getElementById('quick-create-button');
            const quickCreateMenu = document.getElementById('quick-create-menu');
            
            if (quickCreateButton && quickCreateMenu) {
                quickCreateButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    quickCreateMenu.classList.toggle('hidden');
                });
            }
            
            document.addEventListener('click', function() {
                if (quickCreateMenu) quickCreateMenu.classList.add('hidden');
            });
        }

        function initializeSearch() {
            const searchInput = document.getElementById('search-input');
            if (searchInput) {
                // Prefill from URL query parameter
                const urlParams = new URLSearchParams(window.location.search);
                const currentSearch = urlParams.get('search');
                if (currentSearch) {
                    searchInput.value = currentSearch;
                }

                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const val = this.value.trim();
                        const newUrl = new URL(window.location.href);
                        if (val) {
                            newUrl.searchParams.set('search', val);
                        } else {
                            newUrl.searchParams.delete('search');
                        }
                        newUrl.searchParams.set('page', 1);
                        window.location.href = newUrl.toString();
                    }
                });
            }
        }

        function initializeFilters() {
            // Initialize filter buttons
            updateFilterButtonStyles('all', 'all', 'all');
            updateActiveFilters();
        }

        let currentFilterCategory = 'all';
        let currentFilterPriority = 'all';
        let currentFilterType = 'all';
        let currentSearchTerm = '';

        window.filterAnnouncements = function(category, priority, type) {
            if (category !== undefined) currentFilterCategory = category;
            if (priority !== undefined) currentFilterPriority = priority;
            if (type !== undefined) currentFilterType = type;
            
            updateFilterButtonStyles(currentFilterCategory, currentFilterPriority, currentFilterType);
            updateActiveFilters();
            applyFilters();
        }

        function updateFilterButtonStyles(category, priority, type) {
            // Reset all filter buttons
            document.querySelectorAll('.filter-btn-category, .filter-btn-priority, .filter-btn-type, .filter-btn-all').forEach(btn => {
                btn.classList.remove('filter-btn-active', 'bg-uthm-blue', 'text-white');
                
                if (btn.classList.contains('filter-btn-all')) {
                    if (category === 'all' && priority === 'all' && type === 'all') {
                        btn.classList.add('bg-uthm-blue', 'text-white');
                        btn.classList.remove('bg-gray-100', 'text-gray-600');
                    } else {
                        btn.classList.remove('bg-uthm-blue', 'text-white');
                        btn.classList.add('bg-gray-100', 'text-gray-600');
                    }
                } else if (btn.classList.contains('filter-btn-category')) {
                    const onclickStr = btn.getAttribute('onclick') || '';
                    if (onclickStr.includes('academic')) {
                        btn.className = 'px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-category bg-blue-50 text-blue-700 hover:bg-blue-100';
                    } else if (onclickStr.includes('events')) {
                        btn.className = 'px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-category bg-purple-50 text-purple-700 hover:bg-purple-100';
                    } else if (onclickStr.includes('general')) {
                        btn.className = 'px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-category bg-sky-50 text-sky-700 hover:bg-sky-100';
                    } else if (onclickStr.includes('club')) {
                        btn.className = 'px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-category bg-green-50 text-green-700 hover:bg-green-100';
                    }
                } else if (btn.classList.contains('filter-btn-priority')) {
                    const onclickStr = btn.getAttribute('onclick') || '';
                    if (onclickStr.includes('urgent')) {
                        btn.className = 'px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-priority bg-red-50 text-red-700 hover:bg-red-100';
                    } else if (onclickStr.includes('important')) {
                        btn.className = 'px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-priority bg-amber-50 text-amber-700 hover:bg-amber-100';
                    }
                } else if (btn.classList.contains('filter-btn-type')) {
                    const onclickStr = btn.getAttribute('onclick') || '';
                    if (onclickStr.includes('official')) {
                        btn.className = 'px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-type bg-emerald-50 text-emerald-700 hover:bg-emerald-100';
                    } else if (onclickStr.includes('unofficial')) {
                        btn.className = 'px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-type bg-yellow-50 text-yellow-700 hover:bg-yellow-100';
                    }
                }
            });
            
            // Set active category button
            if (category !== 'all') {
                const categoryBtn = Array.from(document.querySelectorAll('.filter-btn-category'))
                    .find(btn => btn.getAttribute('onclick').includes(`'${category}'`));
                if (categoryBtn) {
                    categoryBtn.className = 'px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-category filter-btn-active bg-uthm-blue text-white';
                }
            }
            
            // Set active priority button
            if (priority !== 'all') {
                const priorityBtn = Array.from(document.querySelectorAll('.filter-btn-priority'))
                    .find(btn => btn.getAttribute('onclick').includes(`'${priority}'`));
                if (priorityBtn) {
                    priorityBtn.className = 'px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-priority filter-btn-active bg-uthm-blue text-white';
                }
            }
            
            // Set active type button
            if (type !== 'all') {
                const typeBtn = Array.from(document.querySelectorAll('.filter-btn-type'))
                    .find(btn => btn.getAttribute('onclick').includes(`'${type}'`));
                if (typeBtn) {
                    typeBtn.className = 'px-3 py-1.5 rounded-md text-xs font-semibold filter-btn-type filter-btn-active bg-uthm-blue text-white';
                }
            }
        }

        function updateActiveFilters() {
            const container = document.getElementById('active-filters');
            if (!container) return;
            
            container.innerHTML = '<div class="text-sm text-gray-600 mr-2">Active filters:</div>';
            
            if (currentFilterCategory !== 'all' || currentFilterPriority !== 'all' || currentFilterType !== 'all') {
                container.classList.remove('hidden');
                
                if (currentFilterCategory !== 'all') {
                    const badge = document.createElement('span');
                    badge.className = 'px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full flex items-center';
                    badge.innerHTML = `<i class="fas fa-filter mr-2"></i>Category: ${currentFilterCategory.charAt(0).toUpperCase() + currentFilterCategory.slice(1)}`;
                    container.appendChild(badge);
                }
                
                if (currentFilterPriority !== 'all') {
                    const badge = document.createElement('span');
                    badge.className = 'px-3 py-1 bg-amber-100 text-amber-800 text-sm rounded-full flex items-center';
                    badge.innerHTML = `<i class="fas fa-exclamation-triangle mr-2"></i>Priority: ${currentFilterPriority.charAt(0).toUpperCase() + currentFilterPriority.slice(1)}`;
                    container.appendChild(badge);
                }
                
                if (currentFilterType !== 'all') {
                    const badge = document.createElement('span');
                    badge.className = 'px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full flex items-center';
                    badge.innerHTML = `<i class="fas fa-tag mr-2"></i>Type: ${currentFilterType.charAt(0).toUpperCase() + currentFilterType.slice(1)}`;
                    container.appendChild(badge);
                }
                
                const clearBtn = document.createElement('button');
                clearBtn.className = 'px-3 py-1 bg-gray-200 text-gray-700 text-sm rounded-full hover:bg-gray-300 transition-colors ml-2';
                clearBtn.innerHTML = '<i class="fas fa-times mr-1"></i>Clear Filters';
                clearBtn.onclick = function() { filterAnnouncements('all', 'all', 'all'); };
                container.appendChild(clearBtn);
            } else {
                container.classList.add('hidden');
            }
        }

        function applyFilters() {
            const searchInput = document.getElementById('search-input');
            currentSearchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            
            const cards = document.querySelectorAll('.announcement-card');
            let visibleCount = 0;
            
            cards.forEach(card => {
                const category = card.getAttribute('data-category');
                const priority = card.getAttribute('data-priority');
                const type = card.getAttribute('data-type');
                const title = card.querySelector('h3')?.textContent.toLowerCase() || '';
                const content = card.querySelector('p')?.textContent.toLowerCase() || '';
                
                const categoryMatch = currentFilterCategory === 'all' || category === currentFilterCategory;
                const priorityMatch = currentFilterPriority === 'all' || priority === currentFilterPriority;
                const typeMatch = currentFilterType === 'all' || type === currentFilterType;
                const searchMatch = currentSearchTerm === '' || title.includes(currentSearchTerm) || content.includes(currentSearchTerm);
                
                if (categoryMatch && priorityMatch && typeMatch && searchMatch) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            const noMessage = document.getElementById('no-announcements-message');
            if (noMessage) {
                if (visibleCount === 0 && cards.length > 0) {
                    noMessage.style.display = 'block';
                } else {
                    noMessage.style.display = 'none';
                }
            }
        }

        // Approve/Reject Functions
        function openApproveModal(id, title) {
            currentAnnouncementId = id;
            currentAnnouncementTitle = title;
            document.getElementById('approveTitle').textContent = title;
            document.getElementById('approveModal').classList.remove('hidden');
        }

        function closeApproveModal() {
            document.getElementById('approveModal').classList.add('hidden');
            currentAnnouncementId = null;
        }

        function openRejectModal(id, title) {
            currentAnnouncementId = id;
            currentAnnouncementTitle = title;
            document.getElementById('rejectTitle').textContent = title;
            document.getElementById('rejectionReason').value = '';
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            currentAnnouncementId = null;
        }

       function confirmApprove() {
    if (!currentAnnouncementId) return;
    
    console.log('Approving announcement ID:', currentAnnouncementId);
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        showToast('Security token not found. Please refresh the page.', 'error');
        return;
    }
    
    fetch(`/announcements/${currentAnnouncementId}/approve`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Error response:', text);
                throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Approve response:', data);
        if (data.success) {
            showToast(data.message || 'Announcement approved successfully!', 'success');
            closeApproveModal();
            // Reload the page after 1.5 seconds
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'Error approving announcement', 'error');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showToast('Error: ' + error.message, 'error');
    });
}

function confirmReject() {
    if (!currentAnnouncementId) return;
    
    const reason = document.getElementById('rejectionReason').value.trim();
    if (!reason) {
        showToast('Please provide a reason for rejection', 'error');
        return;
    }
    
    console.log('Rejecting announcement ID:', currentAnnouncementId);
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        showToast('Security token not found. Please refresh the page.', 'error');
        return;
    }
    
    fetch(`/announcements/${currentAnnouncementId}/reject`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Error response:', text);
                throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Reject response:', data);
        if (data.success) {
            showToast(data.message || 'Announcement rejected successfully', 'success');
            closeRejectModal();
            // Reload the page after 1.5 seconds
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'Error rejecting announcement', 'error');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showToast('Error: ' + error.message, 'error');
    });
}

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            const toastDiv = toast.querySelector('div');
            
            toastMessage.textContent = message;
            
            if (type === 'error') {
                toastDiv.classList.remove('bg-green-500');
                toastDiv.classList.add('bg-red-500');
                toastDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i><span id="toastMessage"></span>';
                document.getElementById('toastMessage').textContent = message;
            } else {
                toastDiv.classList.remove('bg-red-500');
                toastDiv.classList.add('bg-green-500');
                toastDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i><span id="toastMessage"></span>';
                document.getElementById('toastMessage').textContent = message;
            }
            
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const approveModal = document.getElementById('approveModal');
            const rejectModal = document.getElementById('rejectModal');
            
            if (event.target === approveModal) closeApproveModal();
            if (event.target === rejectModal) closeRejectModal();
        });
    </script>
    @include('announcements.partials.calendar-assets')
<!-- Mobile Sidebar Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    
    console.log('Announcements page loaded - Sidebar:', sidebar, 'Toggle:', mobileMenuToggle);
    
    if (mobileMenuToggle && sidebar) {
        // Create overlay if not exists
        let overlay = document.querySelector('.mobile-sidebar-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'mobile-sidebar-overlay';
            overlay.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:39;display:none;';
            document.body.appendChild(overlay);
        }
        
        mobileMenuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Mobile menu clicked');
            if (sidebar.classList.contains('mobile-open')) {
                sidebar.classList.remove('mobile-open');
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            } else {
                sidebar.classList.add('mobile-open');
                overlay.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        });
        
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            overlay.style.display = 'none';
            document.body.style.overflow = '';
        });
    }
});
</script>

<style>
@media (max-width: 768px) {
    #sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 280px;
        z-index: 40;
        background: white;
        overflow-y: auto;
    }
    
    #sidebar.mobile-open {
        transform: translateX(0);
    }
    
    .mobile-sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 39;
    }
}
</style>
<!-- Mobile Sidebar Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.getElementById('mobile-menu-toggle');
    
    if (!sidebar || !mobileToggle) {
        console.warn('Sidebar or toggle not found');
        return;
    }
    
    // Create overlay if not exists
    let overlay = document.querySelector('.mobile-sidebar-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'mobile-sidebar-overlay';
        overlay.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:999;display:none;';
        document.body.appendChild(overlay);
    }
    
    // Ensure sidebar has correct styles on mobile
    function applyMobileStyles() {
        if (window.innerWidth < 768) {
            sidebar.style.position = 'fixed';
            sidebar.style.top = '0';
            sidebar.style.left = '0';
            sidebar.style.height = '100vh';
            sidebar.style.width = '280px';
            sidebar.style.transform = 'translateX(-100%)';
            sidebar.style.transition = 'transform 0.3s ease-in-out';
            sidebar.style.zIndex = '1000';
            sidebar.style.background = '#fff';
            sidebar.style.overflowY = 'auto';
            sidebar.style.boxShadow = '2px 0 10px rgba(0,0,0,0.1)';
        } else {
            sidebar.style.transform = 'translateX(0)';
            sidebar.style.position = '';
            sidebar.style.height = '';
            sidebar.style.width = '';
            sidebar.style.zIndex = '';
            sidebar.style.background = '';
            sidebar.style.overflowY = '';
            sidebar.style.boxShadow = '';
        }
    }
    
    applyMobileStyles();
    
    function openSidebar() {
        sidebar.style.transform = 'translateX(0)';
        overlay.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    
    function closeSidebar() {
        sidebar.style.transform = 'translateX(-100%)';
        overlay.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    mobileToggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (sidebar.style.transform === 'translateX(0px)') {
            closeSidebar();
        } else {
            openSidebar();
        }
    });
    
    overlay.addEventListener('click', closeSidebar);
    
    // Close on link click on mobile
    sidebar.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                closeSidebar();
            }
        });
    });
    
    // Handle resize
    window.addEventListener('resize', function() {
        applyMobileStyles();
        if (window.innerWidth >= 768) {
            closeSidebar();
        }
    });
});
</script>

<style>
/* Ensure toggle button is above overlay */
#mobile-menu-toggle {
    position: relative;
    z-index: 1001 !important;
}

.mobile-sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 999;
    display: none;
}
</style>
</body>
</html>
