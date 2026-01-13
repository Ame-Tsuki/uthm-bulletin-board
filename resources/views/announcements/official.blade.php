<!DOCTYPE html>
<html lang="en">
    @php
    // Check if we have the column - default to false for safety
    $hasOfficialColumn = $tableHasOfficialColumn ?? false;
    @endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Announcements - UTHM Bulletin Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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

        /* Announcement styles */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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
        .badge-official {
            background-color: #dcfce7;
            color: #166534;
        }
        
        /* Custom colors */
        .uthm-blue { color: #0056a6; }
        .bg-uthm-blue { background-color: #0056a6; }
        .bg-uthm-blue-light { background-color: #e6f0fa; }
        .uthm-green { color: #6ea342; }
        .uthm-yellow { color: #ffc107; }
        .uthm-red { color: #dc3545; }

        /* Floating button */
        .floating-add-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 86, 166, 0.3);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 86, 166, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(0, 86, 166, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0, 86, 166, 0);
            }
        }
        
        /* Mobile floating button */
        @media (max-width: 768px) {
            .floating-add-btn {
                bottom: 5rem;
                right: 1.5rem;
            }
        }
        
        /* Tab styles */
        .tab-active {
            background-color: #0056a6;
            color: white;
        }
        
        .tab-inactive {
            background-color: #f3f4f6;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }
        
        .tab-inactive:hover {
            background-color: #e5e7eb;
        }
        
        /* Official announcement specific */
        .official-banner {
            background: linear-gradient(135deg, #166534 0%, #22c55e 100%);
            color: white;
        }
    </style>
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

    <!-- Sidebar Dashboard Navigation -->
    <div id="sidebar" class="sidebar-collapsed bg-white shadow-lg h-screen fixed left-0 top-0 overflow-y-auto z-40 sidebar-transition">
        <!-- Sidebar Header -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="bg-green-600 text-white p-2 rounded-lg shrink-0">
                        <i class="fas fa-user-graduate text-lg"></i>
                    </div>
                    <div class="sidebar-text">
                        <h2 class="font-bold uthm-blue">UTHM Bulletin</h2>
                        <p class="text-xs text-gray-500">Official Announcements</p>
                    </div>
                </div>
                
                <!-- Toggle Button -->
                <button id="sidebar-toggle" class="hidden md:block text-gray-500 hover:text-uthm-blue shrink-0">
                    <svg id="toggle-icon" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- User Profile - Now Clickable -->
        <a href="{{ route('profile') }}" class="block hover:bg-gray-50 transition-colors">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-uthm-blue-light rounded-full flex items-center justify-center shrink-0">
                        <span class="font-bold uthm-blue">{{ strtoupper(substr($user?->name ?? 'G', 0, 1)) }}</span>
                    </div>
                    <div class="sidebar-text">
                        <h3 class="font-medium text-gray-900">{{ $user?->name ?? 'Guest User' }}</h3>
                        <p class="text-xs text-gray-500">{{ $user?->uthm_id ?? 'UTHM Member' }}</p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Dashboard Navigation -->
        <nav class="p-4">
            <ul class="space-y-2">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0">
                            <i class="fas fa-home w-5 h-5"></i>
                        </div>
                        <span class="sidebar-text ml-3">Dashboard</span>
                    </a>
                </li>

                <!-- Announcements (Active) -->
                <li>
                    <a href="{{ route('announcements.index') }}" 
                       class="flex items-center p-3 rounded-lg bg-uthm-blue-light text-uthm-blue">
                        <div class="shrink-0">
                            <i class="fas fa-bullhorn w-5 h-5"></i>
                        </div>
                        <span class="sidebar-text ml-3">Announcements</span>
                    </a>
                </li>

                <!-- Calendar -->
                <li>
                    <a href="#" 
                       class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0">
                            <i class="fas fa-calendar-alt w-5 h-5"></i>
                        </div>
                        <span class="sidebar-text ml-3">Calendar</span>
                    </a>
                </li>

                <!-- Events -->
                <li>
                    <a href="#" 
                       class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0">
                            <i class="fas fa-calendar-check w-5 h-5"></i>
                        </div>
                        <span class="sidebar-text ml-3">Events</span>
                    </a>
                </li>

                <!-- Settings -->
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

    <!-- Main Content -->
    <div id="main-content" class="content-collapsed min-h-screen content-transition">
        <!-- Top Navigation Bar -->
        <nav class="bg-white shadow">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Breadcrumb -->
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">Official Announcements</h1>
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-600">Verified UTHM Announcements</span>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- Quick Create Dropdown -->
                        <div class="relative hidden md:block">
                            <a href="{{ route('announcements.create') }}?type=official" 
                               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center">
                                <i class="fas fa-plus mr-2"></i>
                                Create Official
                            </a>
                        </div>
                        
                        <!-- Notification Bell -->
                        <button class="relative p-2 text-gray-600 hover:text-uthm-blue">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        
                        <!-- User Menu -->
                        <div class="relative">
                            <button id="user-menu-button" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100">
                                <div class="w-8 h-8 bg-uthm-blue-light rounded-full flex items-center justify-center">
                                    <span class="font-bold uthm-blue">{{ strtoupper(substr($user?->name ?? 'G', 0, 1)) }}</span>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-gray-900">{{ $user?->name ?? 'Guest User' }}</p>
                                    <p class="text-xs text-gray-500">{{ $user?->uthm_id ?? 'UTHM Member' }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500"></i>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> My Profile
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

        <!-- Main Content -->
        <div class="py-8">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Official Announcements Banner -->
                <div class="official-banner rounded-xl shadow-lg mb-8 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-white/20 p-3 rounded-lg mr-4">
                                <i class="fas fa-shield-alt text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold">Official Announcements</h2>
                                <p class="mt-2 text-white/90">Verified and authorized announcements from UTHM administration and departments</p>
                            </div>
                        </div>
                        <div class="hidden md:block">
                            <i class="fas fa-check-circle text-4xl opacity-50"></i>
                        </div>
                    </div>
                </div>

                <!-- Page Header -->
                <div class="mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">All Official Announcements</h2>
                            <p class="mt-2 text-gray-600">These announcements are verified and published by authorized UTHM staff</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <a href="{{ route('announcements.create') }}?type=official" 
                               class="inline-flex items-center px-5 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors shadow-md">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Create Official Announcement
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Announcement Type Navigation -->
                <div class="mb-8 bg-white rounded-xl shadow p-4">
                    <div class="flex flex-wrap gap-4">
                        <h3 class="text-lg font-medium text-gray-900 self-center">View:</h3>
                        <div class="flex space-x-2">
                            <a href="{{ route('announcements.index') }}" 
                               class="px-4 py-2 rounded-lg text-sm font-medium tab-inactive">
                                All Announcements
                            </a>
                            <a href="{{ route('announcements.official') }}" 
                               class="px-4 py-2 rounded-lg text-sm font-medium tab-active">
                                <i class="fas fa-check-circle mr-2"></i>Official Announcements
                            </a>
                            <a href="{{ route('announcements.unofficial') }}" 
                               class="px-4 py-2 rounded-lg text-sm font-medium tab-inactive">
                                <i class="fas fa-users mr-2"></i>Unofficial Announcements
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Check if database column exists -->
                @if(!$hasOfficialColumn)
                <!-- Database Update Required Message -->
                <div class="mb-8 bg-red-50 border border-red-200 rounded-xl shadow-sm p-5">
                    <div class="flex items-start">
                        <div class="bg-red-100 p-3 rounded-lg mr-4 shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 text-lg">Database Update Required</h3>
                            <p class="text-gray-700 text-sm mt-1 mb-3">
                                The official/unofficial announcement feature requires a database update. 
                                The <code>is_official</code> column needs to be added to the announcements table.
                            </p>
                            @if(auth()->user()->role === 'admin')
                            <div class="mt-3">
                                <button onclick="showMigrationInstructions()" 
                                       class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                                    <i class="fas fa-database mr-2"></i>
                                    Show Migration Instructions
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Filters -->
                <div class="mb-8 bg-white rounded-xl shadow p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex flex-wrap gap-2">
                            <button onclick="filterAnnouncements('all')" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium">
                                All Categories
                            </button>
                            <button onclick="filterAnnouncements('urgent')" 
                                    class="px-4 py-2 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100">
                                <i class="fas fa-exclamation-circle mr-2"></i>Urgent
                            </button>
                            <button onclick="filterAnnouncements('academic')" 
                                    class="px-4 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100">
                                <i class="fas fa-graduation-cap mr-2"></i>Academic
                            </button>
                            <button onclick="filterAnnouncements('events')" 
                                    class="px-4 py-2 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-100">
                                <i class="fas fa-calendar-alt mr-2"></i>Events
                            </button>
                            <button onclick="filterAnnouncements('general')" 
                                    class="px-4 py-2 bg-uthm-blue-light text-uthm-blue rounded-lg text-sm font-medium hover:bg-blue-100">
                                <i class="fas fa-newspaper mr-2"></i>General
                            </button>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" 
                                       id="search-input"
                                       placeholder="Search official announcements..." 
                                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent w-full sm:w-64">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Announcements Grid -->
                <div id="announcements-grid" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @if($hasOfficialColumn)
                        <!-- Database Announcements -->
                        @forelse($announcements as $announcement)
                            @if($announcement->is_official)
                                <div class="announcement-card bg-white rounded-xl shadow hover:shadow-lg transition-shadow overflow-hidden border border-gray-200 border-l-4 border-l-green-500" 
                                     data-category="{{ $announcement->category }}"
                                     data-priority="{{ $announcement->priority }}">
                                    <div class="p-6">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <!-- Official Badge -->
                                                <span class="px-3 py-1 rounded-full text-xs font-medium badge-official">
                                                    <i class="fas fa-check-circle mr-1"></i> Official
                                                </span>
                                                
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
                                        
                                        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                            <div class="flex items-center text-sm text-gray-500">
                                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-2">
                                                    <i class="fas fa-user-tie text-green-600"></i>
                                                </div>
                                                <div>
                                                    <span class="font-medium">{{ $announcement->author->name ?? 'UTHM Admin' }}</span>
                                                    <div class="flex items-center mt-1">
                                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">
                                                            <i class="fas fa-shield-alt mr-1"></i>Verified Authority
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="{{ route('announcements.show', $announcement) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-green-50 text-green-700 font-medium rounded-lg hover:bg-green-100 transition-colors">
                                                View Details
                                                <i class="fas fa-arrow-right ml-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <!-- No Official Announcements Message -->
                            <div class="col-span-2 text-center py-12">
                                <div class="inline-block p-6 bg-green-100 rounded-full mb-4">
                                    <i class="fas fa-shield-alt text-green-400 text-4xl"></i>
                                </div>
                                <h3 class="text-xl font-medium text-gray-900 mb-2">No Official Announcements</h3>
                                <p class="text-gray-600 mb-6">There are no official announcements from UTHM administration at the moment.</p>
                                <div class="mt-8">
                                    <a href="{{ route('announcements.create') }}?type=official" 
                                       class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors shadow-lg">
                                        <i class="fas fa-plus-circle mr-2 text-lg"></i>
                                        Create First Official Announcement
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    @else
                        <!-- Feature Not Available Message -->
                        <div class="col-span-2 text-center py-12">
                            <div class="inline-block p-6 bg-red-100 rounded-full mb-4">
                                <i class="fas fa-exclamation-triangle text-red-400 text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-medium text-gray-900 mb-2">Feature Not Available</h3>
                            <p class="text-gray-600 mb-6">The official/unofficial announcement feature requires a database update.</p>
                            @if(auth()->user()->role === 'admin')
                            <div class="mt-8">
                                <button onclick="showMigrationInstructions()" 
                                       class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors shadow-lg">
                                    <i class="fas fa-database mr-2 text-lg"></i>
                                    Show Migration Instructions
                                </button>
                            </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if($hasOfficialColumn && method_exists($announcements, 'hasPages') && $announcements->hasPages())
                    <div class="mt-8">
                        <div class="bg-white rounded-xl shadow p-4">
                            {{ $announcements->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-6">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-500 text-sm">
                    <p>UTHM Digital Bulletin Board &copy; {{ date('Y') }}. All rights reserved.</p>
                    <p class="mt-1">For issues or inquiries, contact: <a href="mailto:support@uthm.edu.my" class="text-blue-600 hover:text-blue-800">support@uthm.edu.my</a></p>
                </div>
            </div>
        </footer>

        <!-- Floating "Add Official Post" Button -->
        <a href="{{ route('announcements.create') }}?type=official" 
           class="floating-add-btn bg-green-600 text-white p-4 rounded-full shadow-lg hover:bg-green-700 transition-colors hover:shadow-xl">
            <i class="fas fa-shield-alt text-2xl"></i>
        </a>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const toggleIcon = document.getElementById('toggle-icon');
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            const searchInput = document.getElementById('search-input');
            const announcementsGrid = document.getElementById('announcements-grid');
            
            // State variables
            let currentFilterCategory = 'all';
            let currentSearchTerm = '';
            
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
            
            // Mobile menu toggle
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    if (sidebar.classList.contains('mobile-open')) {
                        sidebar.classList.remove('mobile-open');
                    } else {
                        sidebar.classList.add('mobile-open');
                    }
                });
            }
            
            // User menu toggle
            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });
            }
            
            // Close menus when clicking outside
            document.addEventListener('click', function() {
                if (userMenu) userMenu.classList.add('hidden');
            });
            
            // Close mobile sidebar when clicking on links
            if (window.innerWidth < 768) {
                document.querySelectorAll('#sidebar a').forEach(link => {
                    link.addEventListener('click', function() {
                        sidebar.classList.remove('mobile-open');
                    });
                });
            }
            
            // Search functionality (only if we have announcements)
            @if($hasOfficialColumn && $announcements->count() > 0)
            if (searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    currentSearchTerm = this.value.toLowerCase();
                    filterAnnouncements();
                });
            }
            
            // Category Filter
            window.filterAnnouncements = function(category) {
                currentFilterCategory = category;
                filterAnnouncements();
            }
            
            // Filter Function
            function filterAnnouncements() {
                const cards = document.querySelectorAll('.announcement-card');
                let visibleCards = 0;
                
                cards.forEach(card => {
                    const category = card.getAttribute('data-category');
                    const title = card.querySelector('h3').textContent.toLowerCase();
                    const content = card.querySelector('p').textContent.toLowerCase();
                    
                    // Category filter
                    let categoryMatch = currentFilterCategory === 'all' || category === currentFilterCategory;
                    
                    // Search filter
                    let searchMatch = currentSearchTerm === '' || 
                                     title.includes(currentSearchTerm) || 
                                     content.includes(currentSearchTerm);
                    
                    if (categoryMatch && searchMatch) {
                        card.style.display = 'block';
                        visibleCards++;
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
            @endif
            
            function expandSidebar() {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                mainContent.classList.remove('content-collapsed');
                mainContent.classList.add('content-expanded');
                
                if (toggleIcon) {
                    toggleIcon.style.transform = 'rotate(180deg)';
                }
                
                localStorage.setItem('sidebarExpanded', 'true');
            }
            
            function collapseSidebar() {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
                mainContent.classList.remove('content-expanded');
                mainContent.classList.add('content-collapsed');
                
                if (toggleIcon) {
                    toggleIcon.style.transform = 'rotate(0deg)';
                }
                
                localStorage.setItem('sidebarExpanded', 'false');
            }
            
            // Migration instructions modal
            window.showMigrationInstructions = function() {
                const instructions = `To enable the official/unofficial announcement feature, run these commands:\n\n` +
                                   `1. Create migration:\n` +
                                   `   php artisan make:migration add_is_official_to_announcements_table\n\n` +
                                   `2. Edit the migration file and add:\n` +
                                   `   $table->boolean('is_official')->default(false)->after('author_id');\n\n` +
                                   `3. Run migration:\n` +
                                   `   php artisan migrate\n\n` +
                                   `4. Clear cache:\n` +
                                   `   php artisan route:clear\n` +
                                   `   php artisan config:clear\n` +
                                   `   php artisan view:clear`;
                
                alert(instructions);
            }
            
            // Responsive behavior
            window.addEventListener('resize', function() {
                if (window.innerWidth < 768) {
                    if (!sidebar.classList.contains('mobile-open')) {
                        sidebar.style.transform = 'translateX(-100%)';
                    }
                } else {
                    sidebar.style.transform = 'translateX(0)';
                }
            });
            
            // Initialize mobile state
            if (window.innerWidth < 768) {
                sidebar.style.transform = 'translateX(-100%)';
            }
            
            // Initialize filter if we have announcements
            @if($hasOfficialColumn && $announcements->count() > 0)
            filterAnnouncements();
            @endif
        });
    </script>
</body>
</html>