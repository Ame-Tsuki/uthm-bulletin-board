<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - UTHM Bulletin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'uthm-blue': '#0056a6',
                        'uthm-blue-light': '#e6f0fa',
                        'uthm-green': '#6ea342',
                        'uthm-yellow': '#ffc107',
                        'uthm-red': '#dc3545',
                        'uthm-purple': '#6f42c1',
                    }
                }
            }
        }
    </script>
    <style>
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
        
        .sidebar-transition {
            transition: width var(--transition-speed) ease;
        }
        
        .content-transition {
            transition: margin-left var(--transition-speed) ease;
        }
        
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

        .stats-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .announcement-card {
            border-left: 4px solid;
        }
        
        .urgent {
            border-left-color: #dc3545;
        }
        
        .important {
            border-left-color: #ffc107;
        }
        
        .normal {
            border-left-color: #0056a6;
        }
        
        .event-card {
            transition: all 0.3s ease;
        }
        
        .event-card:hover {
            transform: translateY(-2px);
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
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

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar-collapsed bg-white shadow-lg h-screen fixed left-0 top-0 overflow-y-auto z-40 sidebar-transition">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="bg-green-600 text-white p-2 rounded-lg shrink-0">
                        <i class="fas fa-user-graduate text-lg"></i>
                    </div>
                    <div class="sidebar-text">
                        <h2 class="font-bold text-gray-900">UTHM Bulletin</h2>
                        <p class="text-xs text-gray-500">Student Dashboard</p>
                    </div>
                </div>
                
                <button id="sidebar-toggle" class="hidden md:block text-gray-500 hover:text-uthm-blue shrink-0">
                    <svg id="toggle-icon" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                    </svg>
                </button>
            </div>
        </div>

        <a href="{{ route('profile') }}" class="block hover:bg-gray-50 transition-colors">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-uthm-blue-light rounded-full flex items-center justify-center shrink-0">
                        <span class="font-bold text-uthm-blue">{{ strtoupper(substr($user?->name ?? 'G', 0, 1)) }}</span>
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

        <nav class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0"><i class="fas fa-home w-5 h-5"></i></div>
                        <span class="sidebar-text ml-3">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('announcements.index') }}" class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0"><i class="fas fa-bullhorn w-5 h-5"></i></div>
                        <span class="sidebar-text ml-3">Announcements</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('announcements.my-announcements') }}" class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0"><i class="fas fa-file-alt w-5 h-5"></i></div>
                        <span class="sidebar-text ml-3">My Announcements</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('calendar') }}" class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0"><i class="fas fa-calendar-alt w-5 h-5"></i></div>
                        <span class="sidebar-text ml-3">Calendar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('student.community-hub') }}" class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0"><i class="fas fa-users w-5 h-5"></i></div>
                        <span class="sidebar-text ml-3">Community Hub</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('settings') }}" class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0"><i class="fas fa-cog w-5 h-5"></i></div>
                        <span class="sidebar-text ml-3">Settings</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center p-3 rounded-lg hover:bg-red-50 text-red-600 w-full transition-colors">
                    <div class="shrink-0"><i class="fas fa-sign-out-alt w-5 h-5"></i></div>
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
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">Student Dashboard</h1>
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-600">Overview</span>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <button class="relative p-2 text-gray-600 hover:text-uthm-blue">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        
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
                            
                            <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden">
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

        <!-- Main Dashboard Content -->
        <div class="py-8">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Welcome Section -->
                <div class="bg-white rounded-xl shadow p-6 mb-8">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome back, {{ Auth::user()->name }}! 👋</h2>
                            <p class="text-gray-600">Here's your student dashboard overview for today.</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <div class="flex items-center space-x-4 text-sm">
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full">
                                    <i class="fas fa-graduation-cap mr-1"></i> Active Student
                                </span>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                                    <i class="fas fa-calendar mr-1"></i> Semester 1, 2024
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                        <div class="stats-card bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-3 rounded-lg mr-3"><i class="fas fa-id-card text-blue-600"></i></div>
                                <div>
                                    <p class="text-sm text-gray-600">UTHM ID</p>
                                    <p class="font-bold text-gray-900">{{ Auth::user()->uthm_id }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="stats-card bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-3 rounded-lg mr-3"><i class="fas fa-university text-green-600"></i></div>
                                <div>
                                    <p class="text-sm text-gray-600">Faculty</p>
                                    <p class="font-bold text-gray-900">{{ Auth::user()->faculty ?? 'Not specified' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="stats-card bg-purple-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-3 rounded-lg mr-3"><i class="fas fa-envelope text-purple-600"></i></div>
                                <div>
                                    <p class="text-sm text-gray-600">Email</p>
                                    <p class="font-bold text-gray-900">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="stats-card bg-yellow-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="bg-yellow-100 p-3 rounded-lg mr-3"><i class="fas fa-book text-yellow-600"></i></div>
                                <div>
                                    <p class="text-sm text-gray-600">Enrolled Courses</p>
                                    <p class="font-bold text-gray-900">6 Courses</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Featured Posts Carousel -->
                <div class="bg-white rounded-xl shadow p-6 mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">📌 Featured Announcements</h3>
                            <p class="text-sm text-gray-500 mt-1">Important announcements from UTHM</p>
                        </div>
                    </div>
                    
                    @if(isset($featuredAnnouncements) && $featuredAnnouncements->count() > 0)
                        <div class="relative">
                            <div class="overflow-hidden rounded-xl">
                                <div id="featured-carousel" class="flex transition-transform duration-500 ease-in-out">
                                    @foreach($featuredAnnouncements as $announcement)
                                        @php
                                            $categoryConfig = [
                                                'urgent' => ['bg' => 'from-red-50 to-orange-50', 'badge' => 'bg-red-100 text-red-700', 'icon' => 'exclamation-circle', 'label' => 'Urgent'],
                                                'important' => ['bg' => 'from-yellow-50 to-amber-50', 'badge' => 'bg-yellow-100 text-yellow-700', 'icon' => 'exclamation-triangle', 'label' => 'Important'],
                                                'academic' => ['bg' => 'from-blue-50 to-indigo-50', 'badge' => 'bg-blue-100 text-blue-700', 'icon' => 'graduation-cap', 'label' => 'Academic'],
                                                'events' => ['bg' => 'from-purple-50 to-pink-50', 'badge' => 'bg-purple-100 text-purple-700', 'icon' => 'calendar-alt', 'label' => 'Events'],
                                                'general' => ['bg' => 'from-gray-50 to-blue-50', 'badge' => 'bg-gray-100 text-gray-700', 'icon' => 'newspaper', 'label' => 'General']
                                            ];
                                            $category = $announcement->category ?? 'general';
                                            $config = $categoryConfig[$category] ?? $categoryConfig['general'];
                                            $imageUrl = $announcement->featured_image ?? $announcement->image ?? 'https://picsum.photos/id/20/600/400';
                                        @endphp
                                        
                                        <div class="w-full flex-shrink-0">
                                            <div class="bg-gradient-to-r {{ $config['bg'] }} rounded-xl overflow-hidden">
                                                <div class="flex flex-col md:flex-row">
                                                    <div class="md:w-2/5 relative">
                                                        <img src="{{ $imageUrl }}" alt="{{ $announcement->title }}" class="w-full h-64 md:h-full object-cover">
                                                        @if($announcement->priority === 'urgent')
                                                            <div class="absolute top-4 left-4">
                                                                <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold">URGENT</span>
                                                            </div>
                                                        @elseif($announcement->priority === 'important')
                                                            <div class="absolute top-4 left-4">
                                                                <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold">IMPORTANT</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="md:w-3/5 p-6 md:p-8">
                                                        <div class="flex items-center gap-2 mb-3 flex-wrap">
                                                            <span class="px-3 py-1 {{ $config['badge'] }} rounded-full text-xs font-semibold">{{ $config['label'] }}</span>
                                                            @if($announcement->is_official)
                                                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs">Official</span>
                                                            @else
                                                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">Community</span>
                                                            @endif
                                                        </div>
                                                        <h4 class="text-2xl font-bold text-gray-900 mb-3">{{ $announcement->title }}</h4>
                                                        <p class="text-gray-600 mb-4 line-clamp-3">{{ Str::limit(strip_tags($announcement->content), 200) }}</p>
                                                        <a href="{{ route('announcements.show', $announcement) }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 inline-flex items-center">
                                                            View Details <i class="fas fa-arrow-right ml-2"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            @if($featuredAnnouncements->count() > 1)
                                <button id="prev-featured" class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-2 rounded-full shadow-lg">
                                    <i class="fas fa-chevron-left text-xl"></i>
                                </button>
                                <button id="next-featured" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white/90 hover:bg-white text-gray-800 p-2 rounded-full shadow-lg">
                                    <i class="fas fa-chevron-right text-xl"></i>
                                </button>
                            @endif
                        </div>
                        
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const carousel = document.getElementById('featured-carousel');
                            const prevBtn = document.getElementById('prev-featured');
                            const nextBtn = document.getElementById('next-featured');
                            const totalSlides = {{ $featuredAnnouncements->count() }};
                            let currentIndex = 0;
                            
                            function goToSlide(index) {
                                if (!carousel) return;
                                currentIndex = index;
                                carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
                            }
                            
                            function nextSlide() { if (totalSlides > 1) { currentIndex = (currentIndex + 1) % totalSlides; goToSlide(currentIndex); } }
                            function prevSlide() { if (totalSlides > 1) { currentIndex = (currentIndex - 1 + totalSlides) % totalSlides; goToSlide(currentIndex); } }
                            
                            if (prevBtn) prevBtn.onclick = prevSlide;
                            if (nextBtn) nextBtn.onclick = nextSlide;
                            if (totalSlides > 1) setInterval(nextSlide, 5000);
                        });
                        </script>
                    @else
                        <div class="text-center py-12">
                            <div class="inline-block p-6 bg-gray-100 rounded-full mb-4">
                                <i class="fas fa-star text-gray-400 text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-medium text-gray-900">No featured announcements</h3>
                            <p class="text-gray-600">Check back later for important updates!</p>
                        </div>
                    @endif
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Latest Announcements -->
                    <div class="lg:col-span-2">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-2 sm:mb-0">Latest Announcements</h3>
                            <a href="{{ route('announcements.index') }}" class="text-uthm-blue hover:text-blue-700 text-sm font-medium">
                                View All <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>

                        <div class="space-y-4">
                            @forelse($announcements ?? [] as $announcement)
                                @php
                                    $priorityClass = $announcement->priority === 'urgent' ? 'urgent' : ($announcement->priority === 'important' ? 'important' : 'normal');
                                    $priorityColor = $announcement->priority === 'urgent' ? 'bg-red-100 text-red-700' : ($announcement->priority === 'important' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700');
                                @endphp
                                <div class="announcement-card {{ $priorityClass }} bg-white rounded-lg shadow p-6">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <h4 class="font-semibold text-gray-900 text-lg">{{ $announcement->title }}</h4>
                                                @if($announcement->priority)
                                                    <span class="px-2 py-1 text-xs font-medium {{ $priorityColor }} rounded-full">{{ ucfirst($announcement->priority) }}</span>
                                                @endif
                                            </div>
                                            <p class="text-gray-600 text-sm mb-3">{{ Str::limit(strip_tags($announcement->content), 150) }}</p>
                                            <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500">
                                                <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded">
                                                    <i class="fas fa-tag mr-1"></i> {{ ucfirst($announcement->category ?? 'General') }}
                                                </span>
                                                <span><i class="far fa-clock mr-1"></i> {{ $announcement->created_at->diffForHumans() }}</span>
                                                <span><i class="far fa-eye mr-1"></i> {{ $announcement->view_count ?? 0 }} views</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ route('announcements.show', $announcement) }}" class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12">
    <i class="fas fa-bullhorn text-gray-300 text-4xl mb-4"></i>
    <h3 class="text-lg font-semibold text-gray-700">No Announcements Yet</h3>
    <p class="text-gray-500">Check back later or create one.</p>
</div>
                            @endforelse
                        </div>

                        <!-- Quick Stats -->
                        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-center">
                                    <div class="bg-uthm-blue-light p-3 rounded-lg mr-3"><i class="fas fa-fire text-uthm-blue"></i></div>
                                    <div>
                                        <p class="text-sm text-gray-600">Trending</p>
                                        <p class="font-bold text-gray-900 text-sm">Campus Fest 2024</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-center">
                                    <div class="bg-green-50 p-3 rounded-lg mr-3"><i class="fas fa-chart-line text-uthm-green"></i></div>
                                    <div>
                                        <p class="text-sm text-gray-600">Faculty Updates</p>
                                        <p class="font-bold text-gray-900">{{ $announcements->count() ?? 0 }} New</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-center">
                                    <div class="bg-yellow-50 p-3 rounded-lg mr-3"><i class="fas fa-bell text-uthm-yellow"></i></div>
                                    <div>
                                        <p class="text-sm text-gray-600">Unread</p>
                                        <p class="font-bold text-gray-900">{{ $announcements->where('priority', 'urgent')->count() ?? 0 }} Urgent</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-8">
                        <!-- Upcoming Events -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-gray-900">Upcoming Events</h3>
                                <a href="{{ route('student.calendar') }}" class="text-uthm-blue hover:text-blue-700 text-sm font-medium">View Calendar <i class="fas fa-arrow-right ml-1"></i></a>
                            </div>
                            <div class="space-y-4">
                                <div class="event-card flex items-center p-4 bg-yellow-50 rounded-lg">
                                    <div class="bg-yellow-100 p-3 rounded-lg mr-4 text-center min-w-[60px]">
                                        <div class="font-bold text-lg">15</div>
                                        <div class="text-xs uppercase">DEC</div>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-900">Career Workshop</h4>
                                        <p class="text-sm text-gray-600">2:00 PM • Main Hall</p>
                                    </div>
                                    <button class="bg-uthm-blue text-white px-3 py-2 rounded text-sm">Attend</button>
                                </div>
                                <div class="event-card flex items-center p-4 bg-purple-50 rounded-lg">
                                    <div class="bg-purple-100 p-3 rounded-lg mr-4 text-center min-w-[60px]">
                                        <div class="font-bold text-lg">20</div>
                                        <div class="text-xs uppercase">DEC</div>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-900">FYP Submission</h4>
                                        <p class="text-sm text-gray-600">All Day • Faculty Offices</p>
                                    </div>
                                    <button class="bg-uthm-blue text-white px-3 py-2 rounded text-sm">Remind</button>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Links -->
                        <div class="bg-white rounded-xl shadow p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Links</h3>
                            <div class="grid grid-cols-2 gap-3">
                                <a href="#" class="bg-blue-50 hover:bg-blue-100 rounded-lg p-4 text-center">
                                    <i class="fas fa-graduation-cap text-blue-600 text-xl mb-2 block"></i>
                                    <p class="text-sm font-medium">Academic Portal</p>
                                </a>
                                <a href="#" class="bg-green-50 hover:bg-green-100 rounded-lg p-4 text-center">
                                    <i class="fas fa-book text-green-600 text-xl mb-2 block"></i>
                                    <p class="text-sm font-medium">E-Library</p>
                                </a>
                                <a href="#" class="bg-yellow-50 hover:bg-yellow-100 rounded-lg p-4 text-center">
                                    <i class="fas fa-file-alt text-yellow-600 text-xl mb-2 block"></i>
                                    <p class="text-sm font-medium">Assignments</p>
                                </a>
                                <a href="#" class="bg-purple-50 hover:bg-purple-100 rounded-lg p-4 text-center">
                                    <i class="fas fa-users text-purple-600 text-xl mb-2 block"></i>
                                    <p class="text-sm font-medium">Student Clubs</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const sidebarToggle = document.getElementById('sidebar-toggle');
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const toggleIcon = document.getElementById('toggle-icon');
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            
            const isSidebarExpanded = localStorage.getItem('sidebarExpanded') === 'true';
            if (isSidebarExpanded) {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
                mainContent.classList.remove('content-collapsed');
                mainContent.classList.add('content-expanded');
                if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
            }
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (sidebar.classList.contains('sidebar-expanded')) {
                        sidebar.classList.remove('sidebar-expanded');
                        sidebar.classList.add('sidebar-collapsed');
                        mainContent.classList.remove('content-expanded');
                        mainContent.classList.add('content-collapsed');
                        if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
                        localStorage.setItem('sidebarExpanded', 'false');
                    } else {
                        sidebar.classList.remove('sidebar-collapsed');
                        sidebar.classList.add('sidebar-expanded');
                        mainContent.classList.remove('content-collapsed');
                        mainContent.classList.add('content-expanded');
                        if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
                        localStorage.setItem('sidebarExpanded', 'true');
                    }
                });
            }
            
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('mobile-open');
                });
            }
            
            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });
                document.addEventListener('click', function() {
                    userMenu.classList.add('hidden');
                });
            }
        });
    </script>
</body>
</html>