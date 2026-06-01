<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - UTHM Bulletin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('layouts.partials.portal-head')
</head>
<body class="portal-body">
    @include('layouts.partials.portal-sidebar', ['user' => $user ?? Auth::user()])

    <div id="main-content" class="content-collapsed min-h-screen content-transition">
        @include('layouts.partials.portal-topbar', ['pageTitle' => 'Staff Dashboard', 'breadcrumb' => 'Overview'])

        @include('layouts.partials.portal-content-open')
                <div class="portal-stack-lg">
                <div class="portal-welcome relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center justify-between relative z-10 gap-3">
                        <div>
                            <p class="text-blue-200 text-xs font-medium mb-0.5">{{ now()->format('l, F j, Y') }}</p>
                            <h2 class="portal-welcome-title">Welcome back, {{ Auth::user()->name }}!</h2>
                            <p class="text-blue-100/90 text-sm">Your staff dashboard overview for today.</p>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <span class="portal-badge">
                                <i class="fas fa-chalkboard-teacher mr-1.5"></i> Active Staff
                            </span>
                            <span class="portal-badge">
                                <i class="fas fa-calendar mr-1.5"></i> Semester 1, 2024
                            </span>
                        </div>
                    </div>

                    <div class="portal-grid-4 mt-4 relative z-10">
                        <div class="stats-card bg-white/95 backdrop-blur rounded-lg p-3">
                            <div class="flex items-center gap-3">
                                <div class="portal-stat-icon bg-blue-100"><i class="fas fa-id-card text-blue-600"></i></div>
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">UTHM ID</p>
                                    <p class="font-bold text-gray-900 truncate">{{ Auth::user()->uthm_id }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="stats-card bg-white/95 backdrop-blur rounded-lg p-3">
                            <div class="flex items-center gap-3">
                                <div class="portal-stat-icon bg-green-100"><i class="fas fa-building text-green-600"></i></div>
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Department</p>
                                    <p class="font-bold text-gray-900 truncate">{{ Auth::user()->faculty ?? 'Not specified' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="stats-card bg-white/95 backdrop-blur rounded-lg p-3">
                            <div class="flex items-center gap-3">
                                <div class="portal-stat-icon bg-purple-100"><i class="fas fa-envelope text-purple-600"></i></div>
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Email</p>
                                    <p class="font-bold text-gray-900 truncate text-sm">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="stats-card bg-white/95 backdrop-blur rounded-lg p-3">
                            <div class="flex items-center gap-3">
                                <div class="portal-stat-icon bg-amber-100"><i class="fas fa-bullhorn text-amber-600"></i></div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Role</p>
                                    <p class="font-bold text-gray-900">Staff Member</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Grid -->
                <div class="portal-grid-2-1">
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-3 gap-3">
                            <h3 class="portal-section-title">Today's Announcements</h3>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" class="px-3 py-1.5 bg-uthm-blue text-white rounded-lg text-xs font-semibold shadow-sm">All</button>
                                <button type="button" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-600 rounded-lg text-xs font-medium hover:bg-gray-50 transition-colors">My Faculty</button>
                                <button type="button" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-600 rounded-lg text-xs font-medium hover:bg-gray-50 transition-colors">University</button>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="announcement-card important portal-card portal-card-compact">
                                <div class="flex justify-between items-start gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                                            <h4 class="font-semibold text-gray-900 text-lg">FYP Submission Deadline Extended</h4>
                                            <span class="px-2 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-700 rounded-full">Important</span>
                                        </div>
                                        <p class="text-gray-600 text-sm mb-3 leading-relaxed">Final Year Project submission deadline extended to December 20, 2024. Please ensure all submissions are complete.</p>
                                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-md font-medium">
                                                <i class="fas fa-university mr-1"></i> Faculty of Computer Science
                                            </span>
                                            <span><i class="far fa-clock mr-1"></i> 2 hours ago</span>
                                            <span><i class="far fa-eye mr-1"></i> 342 views</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-1 shrink-0">
                                        <button type="button" class="p-2 text-uthm-blue hover:bg-uthm-blue-light rounded-lg transition-colors"><i class="far fa-calendar-plus"></i></button>
                                        <button type="button" class="p-2 text-gray-400 hover:bg-gray-100 rounded-lg transition-colors"><i class="far fa-bookmark"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="announcement-card normal portal-card portal-card-compact">
                                <div class="flex justify-between items-start gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                                            <h4 class="font-semibold text-gray-900 text-lg">Sports Day Registration Open</h4>
                                            <span class="px-2 py-0.5 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Event</span>
                                        </div>
                                        <p class="text-gray-600 text-sm mb-3 leading-relaxed">Annual UTHM Sports Day registration is now open. Click here to register your team for various sports competitions.</p>
                                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                            <span class="px-2 py-1 bg-green-50 text-green-700 rounded-md font-medium">
                                                <i class="fas fa-running mr-1"></i> Student Affairs
                                            </span>
                                            <span><i class="far fa-clock mr-1"></i> 1 day ago</span>
                                            <span><i class="far fa-eye mr-1"></i> 521 views</span>
                                        </div>
                                    </div>
                                    <button type="button" class="portal-btn-primary text-xs px-3 py-1.5 shrink-0">
                                        <i class="fas fa-external-link-alt mr-1"></i> Register
                                    </button>
                                </div>
                            </div>

                            <div class="text-center pt-2">
                                <a href="{{ route('announcements.index') }}" class="inline-flex items-center text-uthm-blue hover:text-blue-800 font-semibold text-sm">
                                    View All Announcements <i class="fas fa-arrow-right ml-2 text-xs"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="portal-card portal-card-compact">
                                <div class="flex items-center gap-3">
                                    <div class="portal-stat-icon bg-uthm-blue-light"><i class="fas fa-fire text-uthm-blue"></i></div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Trending</p>
                                        <p class="font-bold text-gray-900 text-sm">Campus Fest 2024</p>
                                        <p class="text-xs text-gray-400">862 views</p>
                                    </div>
                                </div>
                            </div>
                            <div class="portal-card portal-card-compact">
                                <div class="flex items-center gap-3">
                                    <div class="portal-stat-icon bg-green-50"><i class="fas fa-chart-line text-uthm-green"></i></div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Faculty Updates</p>
                                        <p class="font-bold text-gray-900">12 New Posts</p>
                                        <p class="text-xs text-gray-400">This week</p>
                                    </div>
                                </div>
                            </div>
                            <div class="portal-card portal-card-compact">
                                <div class="flex items-center gap-3">
                                    <div class="portal-stat-icon bg-amber-50"><i class="fas fa-bell text-amber-500"></i></div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Unread</p>
                                        <p class="font-bold text-gray-900">5 New</p>
                                        <p class="text-xs text-gray-400">2 Urgent</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Column -->
                    <div class="portal-stack">
                        <div class="portal-card">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="portal-section-title">Upcoming Events</h3>
                                <a href="{{ route('staff.calendar') }}" class="text-uthm-blue hover:text-blue-800 text-xs font-semibold">View Calendar <i class="fas fa-arrow-right ml-1"></i></a>
                            </div>
                            <div class="space-y-3">
                                <div class="event-card flex items-center gap-3 p-4 bg-amber-50/80 rounded-xl">
                                    <div class="bg-amber-100 p-2.5 rounded-xl text-center min-w-[52px]">
                                        <div class="font-bold text-base leading-none">15</div>
                                        <div class="text-[10px] uppercase font-semibold text-amber-700">DEC</div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 text-sm">Career Workshop</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">2:00 PM · Main Hall</p>
                                        <span class="inline-block mt-1.5 px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] rounded font-medium">Career</span>
                                    </div>
                                    <button type="button" class="portal-btn-primary text-xs px-2.5 py-1.5 shrink-0">Attend</button>
                                </div>
                                <div class="event-card flex items-center gap-3 p-4 bg-purple-50/80 rounded-xl">
                                    <div class="bg-purple-100 p-2.5 rounded-xl text-center min-w-[52px]">
                                        <div class="font-bold text-base leading-none">20</div>
                                        <div class="text-[10px] uppercase font-semibold text-purple-700">DEC</div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 text-sm">FYP Submission</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">All Day · Faculty Offices</p>
                                        <span class="inline-block mt-1.5 px-2 py-0.5 bg-red-50 text-red-700 text-[10px] rounded font-medium">Deadline</span>
                                    </div>
                                    <button type="button" class="portal-btn-primary text-xs px-2.5 py-1.5 shrink-0"><i class="far fa-calendar-plus"></i></button>
                                </div>
                                <div class="event-card flex items-center gap-3 p-4 bg-green-50/80 rounded-xl">
                                    <div class="bg-green-100 p-2.5 rounded-xl text-center min-w-[52px]">
                                        <div class="font-bold text-base leading-none">25</div>
                                        <div class="text-[10px] uppercase font-semibold text-green-700">DEC</div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 text-sm">Christmas Celebration</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">6:00 PM · Student Center</p>
                                        <span class="inline-block mt-1.5 px-2 py-0.5 bg-green-50 text-green-700 text-[10px] rounded font-medium">Social</span>
                                    </div>
                                    <button type="button" class="bg-uthm-green text-white text-xs px-2.5 py-1.5 rounded-lg font-semibold shrink-0 hover:opacity-90 transition-opacity">Join</button>
                                </div>
                            </div>
                        </div>

                        <div class="portal-card">
                            <h3 class="portal-section-title mb-3">Quick Links</h3>
                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('announcements.create') }}" class="portal-quick-link bg-blue-50 hover:bg-blue-100">
                                    <i class="fas fa-plus-circle text-blue-600 text-lg mb-2 block"></i>
                                    <p class="text-xs font-semibold text-gray-800">New Announcement</p>
                                </a>
                                <a href="{{ route('announcements.my-announcements') }}" class="portal-quick-link bg-green-50 hover:bg-green-100">
                                    <i class="fas fa-file-alt text-green-600 text-lg mb-2 block"></i>
                                    <p class="text-xs font-semibold text-gray-800">My Posts</p>
                                </a>
                                <a href="{{ route('staff.calendar') }}" class="portal-quick-link bg-amber-50 hover:bg-amber-100">
                                    <i class="fas fa-calendar-alt text-amber-600 text-lg mb-2 block"></i>
                                    <p class="text-xs font-semibold text-gray-800">Calendar</p>
                                </a>
                                <a href="{{ route('settings') }}" class="portal-quick-link bg-purple-50 hover:bg-purple-100">
                                    <i class="fas fa-cog text-purple-600 text-lg mb-2 block"></i>
                                    <p class="text-xs font-semibold text-gray-800">Settings</p>
                                </a>
                            </div>
                        </div>

                        <div class="portal-card">
                            <h3 class="portal-section-title mb-3">Activity Summary</h3>
                            <div class="space-y-3">
                                <div>
                                    <div class="flex justify-between text-sm mb-1.5">
                                        <span class="text-gray-600 font-medium">Announcements Posted</span>
                                        <span class="font-bold text-uthm-blue">24</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="bg-uthm-blue h-2 rounded-full transition-all" style="width: 80%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1.5">
                                        <span class="text-gray-600 font-medium">Pending Reviews</span>
                                        <span class="font-bold text-amber-600">3</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="bg-amber-500 h-2 rounded-full transition-all" style="width: 30%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between text-sm mb-1.5">
                                        <span class="text-gray-600 font-medium">Events This Month</span>
                                        <span class="font-bold text-green-600">8</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="bg-uthm-green h-2 rounded-full transition-all" style="width: 65%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
        @include('layouts.partials.portal-content-close')
    </div>

    @include('layouts.partials.portal-scripts')
</body>
</html>
