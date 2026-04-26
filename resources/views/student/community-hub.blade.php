{{-- resources/views/student/community-hub.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Hub - UTHM Bulletin</title>
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

        /* Dashboard custom styles */
        .stats-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Community specific styles */
        .post-card {
            transition: all 0.3s ease;
        }
        .post-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
        .club-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .trending-tag {
            transition: all 0.2s ease;
        }
        .trending-tag:hover {
            transform: scale(1.05);
            background-color: #0056a6;
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

        <!-- User Profile -->
        <a href="{{ route('profile') }}" class="block hover:bg-gray-50 transition-colors">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-uthm-blue-light rounded-full flex items-center justify-center shrink-0">
                        <span class="font-bold text-uthm-blue">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</span>
                    </div>
                    <div class="sidebar-text">
                        <h3 class="font-medium text-gray-900">{{ Auth::user()->name ?? 'Ahmad Faiz' }}</h3>
                        <p class="text-xs text-gray-500">{{ Auth::user()->uthm_id ?? 'CD220055' }}</p>
                        @if(Auth::user()?->role)
                            <span class="mt-1 inline-block px-2 py-1 text-xs rounded-full badge-{{ Auth::user()->role }}">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                        @else
                            <span class="mt-1 inline-block px-2 py-1 text-xs rounded-full badge-student">Student</span>
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
                    <a href="{{ route('student.community-hub') }}" class="flex items-center p-3 rounded-lg bg-uthm-blue-light text-uthm-blue font-medium transition-colors">
                        <div class="shrink-0"><i class="fas fa-users w-5 h-5"></i></div>
                        <span class="sidebar-text ml-3">Community Hub</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600 hover:text-uthm-blue transition-colors">
                        <div class="shrink-0"><i class="fas fa-calendar-check w-5 h-5"></i></div>
                        <span class="sidebar-text ml-3">Events</span>
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

        <!-- Logout -->
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
                        <h1 class="text-xl font-bold text-gray-900">Community Hub</h1>
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-600">Connect & Engage</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="relative p-2 text-gray-600 hover:text-uthm-blue">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <div class="relative">
                            <button id="user-menu-button" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <span class="font-bold text-green-700">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</span>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name ?? 'Ahmad Faiz' }}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->uthm_id ?? 'CD220055' }}</p>
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

        <!-- Main Community Hub Content -->
        <div class="py-8">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Welcome & Create Post Section -->
                <div class="bg-white rounded-xl shadow p-6 mb-8">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Community Hub ✨</h2>
                            <p class="text-gray-600">Connect with fellow students, join clubs, and share your university experience.</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <button onclick="openCreatePostModal()" class="bg-uthm-blue text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition shadow-md flex items-center">
                                <i class="fas fa-plus-circle mr-2"></i> Create New Post
                            </button>
                        </div>
                    </div>
                    
                    <!-- Trending Topics Row -->
                    <div class="mt-6 flex flex-wrap gap-2">
                        <span class="trending-tag px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm cursor-pointer"><i class="fas fa-fire text-red-500 mr-1"></i> #ExamTips</span>
                        <span class="trending-tag px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm cursor-pointer"><i class="fas fa-laptop-code mr-1"></i> #WebDev</span>
                        <span class="trending-tag px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm cursor-pointer"><i class="fas fa-futbol mr-1"></i> #SportsDay</span>
                        <span class="trending-tag px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm cursor-pointer"><i class="fas fa-music mr-1"></i> #CampusFest</span>
                        <span class="trending-tag px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm cursor-pointer"><i class="fas fa-book-open mr-1"></i> #StudyGroup</span>
                        <span class="trending-tag px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm cursor-pointer"><i class="fas fa-microphone-alt mr-1"></i> #Debate2024</span>
                    </div>
                </div>

                <!-- Main Grid: Feed + Sidebar -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- LEFT COLUMN: FEED -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Filter Tabs -->
                        <div class="bg-white rounded-xl shadow-sm p-2 flex space-x-1">
                            <button class="filter-btn flex-1 py-2 px-4 bg-uthm-blue text-white rounded-lg text-sm font-medium transition" data-filter="latest">Latest Posts</button>
                            <button class="filter-btn flex-1 py-2 px-4 text-gray-600 hover:bg-gray-100 rounded-lg text-sm font-medium transition" data-filter="trending">Trending</button>
                            <button class="filter-btn flex-1 py-2 px-4 text-gray-600 hover:bg-gray-100 rounded-lg text-sm font-medium transition" data-filter="following">Following</button>
                        </div>

                        <!-- Posts Container -->
                        <div id="posts-container">
                            <!-- Post 1 - Study Group -->
                            <div class="post-card bg-white rounded-xl shadow p-5" data-type="latest">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center shrink-0">
                                        <span class="font-bold text-purple-600">SN</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center justify-between">
                                            <div>
                                                <h4 class="font-bold text-gray-900">Sara Nabila</h4>
                                                <p class="text-xs text-gray-500">Computer Science • 2 hours ago</p>
                                            </div>
                                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full"><i class="fas fa-globe mr-1"></i> Public</span>
                                        </div>
                                        <p class="mt-2 text-gray-700">Anyone interested in joining a weekend coding bootcamp? We're forming a group to prepare for the upcoming hackathon! 🚀</p>
                                        <div class="mt-3 flex items-center gap-4 text-gray-500 text-sm">
                                            <button class="like-btn hover:text-uthm-blue transition"><i class="far fa-heart mr-1"></i> <span class="like-count">24</span> Likes</button>
                                            <button class="comment-btn hover:text-uthm-blue transition"><i class="far fa-comment mr-1"></i> 8 Comments</button>
                                            <button class="share-btn hover:text-uthm-blue transition"><i class="fas fa-share-alt mr-1"></i> Share</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Post 2 - Club Workshop -->
                            <div class="post-card bg-white rounded-xl shadow p-5" data-type="trending">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
                                        <span class="font-bold text-blue-600">KM</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center justify-between">
                                            <div>
                                                <h4 class="font-bold text-gray-900">Kelab Multimedia</h4>
                                                <p class="text-xs text-gray-500">Official Club • 5 hours ago</p>
                                            </div>
                                            <span class="px-2 py-1 bg-uthm-blue-light text-uthm-blue text-xs rounded-full"><i class="fas fa-crown mr-1"></i> Club</span>
                                        </div>
                                        <p class="mt-2 text-gray-700">🎬 Workshop: "Intro to Video Editing for Beginners" this Friday at Dewan Serbaguna. Free admission for all students! Don't miss out.</p>
                                        <div class="mt-3 bg-gray-100 rounded-lg p-3 flex items-center gap-3">
                                            <i class="fas fa-calendar-alt text-uthm-blue text-lg"></i>
                                            <div><p class="font-medium text-sm">Friday, 22 Nov 2024</p><p class="text-xs text-gray-500">3:00 PM - 6:00 PM</p></div>
                                        </div>
                                        <div class="mt-3 flex items-center gap-4 text-gray-500 text-sm">
                                            <button class="like-btn hover:text-uthm-blue transition"><i class="far fa-heart mr-1"></i> <span class="like-count">56</span> Likes</button>
                                            <button class="comment-btn hover:text-uthm-blue transition"><i class="far fa-comment mr-1"></i> 12 Comments</button>
                                            <button class="share-btn hover:text-uthm-blue transition"><i class="fas fa-share-alt mr-1"></i> Share</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Post 3 - Help Request -->
                            <div class="post-card bg-white rounded-xl shadow p-5 border-l-4 border-uthm-red" data-type="latest">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center shrink-0">
                                        <span class="font-bold text-red-600">HR</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center justify-between">
                                            <div>
                                                <h4 class="font-bold text-gray-900">Hafiz Rosli</h4>
                                                <p class="text-xs text-gray-500">Mechanical Engineering • Yesterday</p>
                                            </div>
                                            <span class="px-2 py-1 bg-red-100 text-red-600 text-xs rounded-full"><i class="fas fa-hand-holding-heart mr-1"></i> Looking for help</span>
                                        </div>
                                        <p class="mt-2 text-gray-700">Does anyone have past year papers for Thermodynamics (MEM231)? I'm struggling with the upcoming quiz. Would really appreciate any study materials! 🙏</p>
                                        <div class="mt-3 p-2 bg-gray-50 rounded-lg flex items-center gap-2 text-sm">
                                            <i class="fas fa-user-friends text-uthm-blue"></i>
                                            <span>3 people are willing to help</span>
                                            <button class="ml-auto text-uthm-blue font-medium text-xs">Offer help +</button>
                                        </div>
                                        <div class="mt-3 flex items-center gap-4 text-gray-500 text-sm">
                                            <button class="like-btn hover:text-uthm-blue transition"><i class="far fa-heart mr-1"></i> <span class="like-count">18</span> Likes</button>
                                            <button class="comment-btn hover:text-uthm-blue transition"><i class="far fa-comment mr-1"></i> 5 Comments</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Post 4 - Career Talk -->
                            <div class="post-card bg-white rounded-xl shadow p-5" data-type="trending">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center shrink-0">
                                        <span class="font-bold text-yellow-600">CC</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center justify-between">
                                            <div>
                                                <h4 class="font-bold text-gray-900">Career Center</h4>
                                                <p class="text-xs text-gray-500">Official • 1 day ago</p>
                                            </div>
                                            <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full"><i class="fas fa-briefcase mr-1"></i> Career</span>
                                        </div>
                                        <p class="mt-2 text-gray-700">🚀 Industry Talk with Google Engineers! Learn about career opportunities in tech. Free registration for all UTHM students. Limited seats available!</p>
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <span class="text-xs bg-gray-100 px-2 py-1 rounded">#TechCareer</span>
                                            <span class="text-xs bg-gray-100 px-2 py-1 rounded">#Google</span>
                                        </div>
                                        <div class="mt-3 flex items-center gap-4 text-gray-500 text-sm">
                                            <button class="like-btn hover:text-uthm-blue transition"><i class="far fa-heart mr-1"></i> <span class="like-count">102</span> Likes</button>
                                            <button class="comment-btn hover:text-uthm-blue transition"><i class="far fa-comment mr-1"></i> 34 Comments</button>
                                            <button class="register-btn bg-uthm-blue text-white px-3 py-1 rounded text-xs">Register Now</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Load more button -->
                        <div class="text-center pt-4">
                            <button class="load-more-btn px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">Load More Posts <i class="fas fa-arrow-down ml-1"></i></button>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: CLUBS & COMMUNITY SIDEBAR -->
                    <div class="space-y-6">
                        <!-- Student Clubs Spotlight -->
                        <div class="bg-white rounded-xl shadow p-5">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-bold text-gray-900 text-lg"><i class="fas fa-users text-uthm-blue mr-2"></i> Featured Clubs</h3>
                                <a href="#" class="text-uthm-blue text-sm font-medium">View All <i class="fas fa-arrow-right ml-1"></i></a>
                            </div>
                            <div class="space-y-4">
                                <div class="club-item flex items-center p-3 bg-gray-50 rounded-lg hover:shadow transition">
                                    <div class="club-badge w-10 h-10 rounded-full flex items-center justify-center text-white font-bold shrink-0">RC</div>
                                    <div class="ml-3 flex-1">
                                        <h4 class="font-semibold text-gray-900">Robotics Club</h4>
                                        <p class="text-xs text-gray-500">342 members • Active</p>
                                    </div>
                                    <button class="join-club-btn bg-uthm-blue text-white px-3 py-1 rounded text-xs" data-club="Robotics Club">Join</button>
                                </div>
                                <div class="club-item flex items-center p-3 bg-gray-50 rounded-lg hover:shadow transition">
                                    <div class="bg-uthm-green w-10 h-10 rounded-full flex items-center justify-center text-white font-bold shrink-0">DE</div>
                                    <div class="ml-3 flex-1">
                                        <h4 class="font-semibold text-gray-900">Debate Society</h4>
                                        <p class="text-xs text-gray-500">189 members • Tryouts open</p>
                                    </div>
                                    <button class="join-club-btn bg-uthm-blue text-white px-3 py-1 rounded text-xs" data-club="Debate Society">Join</button>
                                </div>
                                <div class="club-item flex items-center p-3 bg-gray-50 rounded-lg hover:shadow transition">
                                    <div class="bg-uthm-purple w-10 h-10 rounded-full flex items-center justify-center text-white font-bold shrink-0">IA</div>
                                    <div class="ml-3 flex-1">
                                        <h4 class="font-semibold text-gray-900">International Club</h4>
                                        <p class="text-xs text-gray-500">521 members • Cultural events</p>
                                    </div>
                                    <button class="join-club-btn bg-uthm-blue text-white px-3 py-1 rounded text-xs" data-club="International Club">Join</button>
                                </div>
                                <div class="club-item flex items-center p-3 bg-gray-50 rounded-lg hover:shadow transition">
                                    <div class="bg-orange-500 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold shrink-0">EC</div>
                                    <div class="ml-3 flex-1">
                                        <h4 class="font-semibold text-gray-900">Entrepreneurs Club</h4>
                                        <p class="text-xs text-gray-500">267 members • Startup focus</p>
                                    </div>
                                    <button class="join-club-btn bg-uthm-blue text-white px-3 py-1 rounded text-xs" data-club="Entrepreneurs Club">Join</button>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Community Events -->
                        <div class="bg-white rounded-xl shadow p-5">
                            <h3 class="font-bold text-gray-900 text-lg mb-4"><i class="fas fa-calendar-check text-uthm-yellow mr-2"></i> Upcoming Events</h3>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 p-2 border-b border-gray-100">
                                    <div class="bg-uthm-blue-light text-center p-2 rounded-lg min-w-[60px]">
                                        <div class="font-bold text-uthm-blue">28</div>
                                        <div class="text-xs uppercase">NOV</div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Cultural Night 2024</p>
                                        <p class="text-xs text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i> Main Hall, 8PM</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 p-2 border-b border-gray-100">
                                    <div class="bg-uthm-blue-light text-center p-2 rounded-lg min-w-[60px]">
                                        <div class="font-bold text-uthm-blue">5</div>
                                        <div class="text-xs uppercase">DEC</div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Charity Run: Fun Walk</p>
                                        <p class="text-xs text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i> UTHM Track Field</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 p-2">
                                    <div class="bg-uthm-blue-light text-center p-2 rounded-lg min-w-[60px]">
                                        <div class="font-bold text-uthm-blue">12</div>
                                        <div class="text-xs uppercase">DEC</div>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">Startup Weekend</p>
                                        <p class="text-xs text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i> FSKTM Auditorium</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Discussion Poll -->
                        <div class="bg-gradient-to-r from-uthm-blue to-blue-800 rounded-xl shadow p-5 text-white">
                            <h3 class="font-bold text-lg mb-2"><i class="fas fa-chart-simple mr-2"></i> Weekly Poll</h3>
                            <p class="text-sm opacity-90 mb-3">What's your favorite study spot on campus?</p>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2"><input type="radio" name="poll" class="w-4 h-4"> <label class="text-sm">Library Level 4</label></div>
                                <div class="flex items-center gap-2"><input type="radio" name="poll" class="w-4 h-4"> <label class="text-sm">Cafe Nusantara</label></div>
                                <div class="flex items-center gap-2"><input type="radio" name="poll" class="w-4 h-4"> <label class="text-sm">Digital Lab</label></div>
                                <button class="mt-3 w-full bg-white text-uthm-blue py-1 rounded-lg text-sm font-medium hover:bg-gray-100 transition">Vote</button>
                            </div>
                            <p class="text-xs mt-3 text-center opacity-80">247 votes so far</p>
                        </div>

                        <!-- Recent Community Activity -->
                        <div class="bg-white rounded-xl shadow p-5">
                            <h3 class="font-bold text-gray-900 text-lg mb-3"><i class="fas fa-clock text-gray-500 mr-2"></i> Recent Activity</h3>
                            <ul class="space-y-3 text-sm">
                                <li class="flex items-start gap-2"><i class="fas fa-user-plus text-green-600 mt-0.5"></i><span><span class="font-medium">Ali M.</span> joined <span class="font-medium">Robotics Club</span> • 10m ago</span></li>
                                <li class="flex items-start gap-2"><i class="fas fa-heart text-red-500 mt-0.5"></i><span><span class="font-medium">Nina D.</span> liked a post about study tips • 1h ago</span></li>
                                <li class="flex items-start gap-2"><i class="fas fa-comment text-uthm-blue mt-0.5"></i><span><span class="font-medium">Faris I.</span> commented on "Hackathon team recruitment" • 3h ago</span></li>
                                <li class="flex items-start gap-2"><i class="fas fa-plus-circle text-uthm-green mt-0.5"></i><span>New post in <span class="font-medium">#ExamTips</span> • 5h ago</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Post Modal (Hidden by default) -->
    <div id="createPostModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-900">Create New Post</h3>
                <button onclick="closeCreatePostModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="createPostForm">
                <textarea class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:border-uthm-blue" rows="4" placeholder="What's on your mind? Share with the community..."></textarea>
                <div class="mt-4 flex gap-2">
                    <button type="button" onclick="closeCreatePostModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-uthm-blue text-white rounded-lg hover:bg-blue-700">Post</button>
                </div>
            </form>
        </div>
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
            
            // Load sidebar state
            const isSidebarExpanded = localStorage.getItem('sidebarExpanded') === 'true';
            if (isSidebarExpanded) expandSidebar();
            else collapseSidebar();
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (sidebar.classList.contains('sidebar-expanded')) collapseSidebar();
                    else expandSidebar();
                });
            }
            
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    if (sidebar.classList.contains('mobile-open')) sidebar.classList.remove('mobile-open');
                    else sidebar.classList.add('mobile-open');
                });
            }
            
            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });
                document.addEventListener('click', function() { userMenu.classList.add('hidden'); });
            }
            
            if (window.innerWidth < 768) {
                document.querySelectorAll('#sidebar a').forEach(link => {
                    link.addEventListener('click', () => sidebar.classList.remove('mobile-open'));
                });
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
                    if (!sidebar.classList.contains('mobile-open')) sidebar.style.transform = 'translateX(-100%)';
                } else {
                    sidebar.style.transform = 'translateX(0)';
                }
            });
            if (window.innerWidth < 768) sidebar.style.transform = 'translateX(-100%)';

            // Filter functionality
            const filterBtns = document.querySelectorAll('.filter-btn');
            const posts = document.querySelectorAll('.post-card');
            
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => {
                        b.classList.remove('bg-uthm-blue', 'text-white');
                        b.classList.add('text-gray-600');
                    });
                    this.classList.add('bg-uthm-blue', 'text-white');
                    this.classList.remove('text-gray-600');
                    
                    const filter = this.dataset.filter;
                    posts.forEach(post => {
                        if (filter === 'latest') {
                            post.style.display = 'block';
                        } else if (filter === 'trending') {
                            if (post.dataset.type === 'trending') post.style.display = 'block';
                            else post.style.display = 'none';
                        } else {
                            post.style.display = 'none';
                        }
                    });
                });
            });

            // Like functionality
            const likeBtns = document.querySelectorAll('.like-btn');
            likeBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const likeSpan = this.querySelector('.like-count');
                    let count = parseInt(likeSpan.textContent);
                    const icon = this.querySelector('i');
                    
                    if (icon.classList.contains('far')) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        icon.style.color = '#dc2626';
                        likeSpan.textContent = count + 1;
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        icon.style.color = '';
                        likeSpan.textContent = count - 1;
                    }
                });
            });

            // Join club functionality
            const joinBtns = document.querySelectorAll('.join-club-btn');
            joinBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const clubName = this.dataset.club;
                    this.textContent = 'Joined ✓';
                    this.classList.remove('bg-uthm-blue');
                    this.classList.add('bg-green-600');
                    this.disabled = true;
                    
                    // Show notification
                    showNotification(`You've joined ${clubName}!`, 'success');
                });
            });

            // Load more posts
            const loadMoreBtn = document.querySelector('.load-more-btn');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function() {
                    this.textContent = 'Loading...';
                    setTimeout(() => {
                        this.textContent = 'Load More Posts';
                        showNotification('No more posts to load', 'info');
                    }, 1000);
                });
            }

            // Create post form
            const createPostForm = document.getElementById('createPostForm');
            if (createPostForm) {
                createPostForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const textarea = this.querySelector('textarea');
                    if (textarea.value.trim()) {
                        showNotification('Your post has been published!', 'success');
                        textarea.value = '';
                        closeCreatePostModal();
                    }
                });
            }

            // Helper function to show notifications
            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = `fixed bottom-4 right-4 bg-${type === 'success' ? 'green' : 'blue'}-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
                notification.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'} mr-2"></i>${message}`;
                document.body.appendChild(notification);
                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }
        });

        // Modal functions
        function openCreatePostModal() {
            document.getElementById('createPostModal').classList.remove('hidden');
            document.getElementById('createPostModal').classList.add('flex');
        }
        
        function closeCreatePostModal() {
            document.getElementById('createPostModal').classList.add('hidden');
            document.getElementById('createPostModal').classList.remove('flex');
        }
    </script>
</body>
</html>