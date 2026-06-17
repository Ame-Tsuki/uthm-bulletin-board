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
            <!-- Welcome Hero -->
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
                            <div class="portal-stat-icon bg-amber-100"><i class="fas fa-user-shield text-amber-600"></i></div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Role</p>
                                <p class="font-bold text-gray-900 text-sm">Staff Member</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Featured Announcements Slider -->
            <div class="portal-card">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="portal-section-title flex items-center gap-2">
                            <i class="fas fa-star text-amber-400"></i> Featured Announcements
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Important updates from UTHM</p>
                    </div>
                </div>

                @if(isset($featuredAnnouncements) && $featuredAnnouncements->count() > 0)
                    <div class="relative">
                        <div class="overflow-hidden rounded-xl">
                            <div id="featured-carousel" class="flex transition-transform duration-500 ease-in-out">
                                @foreach($featuredAnnouncements as $announcement)
                                    @php
                                        $categoryConfig = [
                                            'academic' => ['bg' => 'from-blue-50 to-indigo-50', 'badge' => 'bg-blue-100 text-blue-700', 'icon' => 'graduation-cap', 'label' => 'Academic'],
                                            'events' => ['bg' => 'from-purple-50 to-pink-50', 'badge' => 'bg-purple-100 text-purple-700', 'icon' => 'calendar-alt', 'label' => 'Events'],
                                            'club' => ['bg' => 'from-green-50 to-emerald-50', 'badge' => 'bg-green-100 text-green-700', 'icon' => 'users', 'label' => 'Club'],
                                            'general' => ['bg' => 'from-gray-50 to-blue-50', 'badge' => 'bg-gray-100 text-gray-700', 'icon' => 'newspaper', 'label' => 'General']
                                        ];
                                        $category = $announcement->category ?? 'general';
                                        $config = $categoryConfig[$category] ?? $categoryConfig['general'];
                                        $imageUrl = $announcement->image_url ?? \App\Models\Announcement::DEFAULT_IMAGE_URL;
                                    @endphp

                                    <div class="w-full flex-shrink-0">
                                        <div class="bg-gradient-to-r {{ $config['bg'] }} rounded-xl overflow-hidden border border-gray-100">
                                            <div class="flex flex-col md:flex-row">
                                                <div class="md:w-2/5 relative">
                                                    <img src="{{ $imageUrl }}" alt="{{ $announcement->title }}" class="w-full h-44 md:h-full md:min-h-[11rem] object-cover" onerror="this.onerror=null;this.src='{{ \App\Models\Announcement::DEFAULT_IMAGE_URL }}'">
                                                </div>
                                                <div class="md:w-3/5 p-6 md:p-8">
                                                    <div class="flex items-center gap-2 mb-3 flex-wrap">
                                                        @if($announcement->is_official)
                                                            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                                                <i class="fas fa-check-circle mr-1"></i> Official
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                                                                <i class="fas fa-users mr-1"></i> Unofficial
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <h4 class="text-xl sm:text-2xl font-bold text-gray-900 mb-3">{{ $announcement->title }}</h4>
                                                    <p class="text-gray-600 mb-4 line-clamp-3">{{ Str::limit(strip_tags($announcement->content), 200) }}</p>
                                                    <div class="flex flex-wrap items-center gap-2 relative z-10">
                                                        @include('announcements.partials.calendar-dropdown', [
                                                            'announcement' => $announcement,
                                                            'compact' => true,
                                                        ])
                                                        <a href="{{ route('announcements.show', $announcement) }}" class="portal-btn-primary inline-flex items-center text-sm">
                                                            View Details <i class="fas fa-arrow-right ml-2"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if($featuredAnnouncements->count() > 1)
                            <button id="prev-featured" type="button" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/95 hover:bg-white text-gray-800 p-2.5 rounded-full shadow-lg border border-gray-100 transition-all hover:scale-105">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button id="next-featured" type="button" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/95 hover:bg-white text-gray-800 p-2.5 rounded-full shadow-lg border border-gray-100 transition-all hover:scale-105">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        @endif
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-2xl mb-4">
                            <i class="fas fa-star text-gray-300 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">No featured announcements</h3>
                        <p class="text-gray-500 text-sm mt-1">Check back later for important updates!</p>
                    </div>
                @endif
            </div>

            <!-- Main Grid -->
            <div class="portal-grid-2-1">
                <div>
                    <!-- Pending Verification Reviews Queue -->
                    @if(isset($pendingAnnouncements) && $pendingAnnouncements->count() > 0)
                        <div class="mb-6 bg-amber-50/50 border border-amber-200 rounded-xl p-5 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-2">
                                    <div class="bg-amber-500 text-white p-2 rounded-lg">
                                        <i class="fas fa-clipboard-list text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-lg">📋 Pending Verification Queue</h3>
                                        <p class="text-gray-600 text-xs">Official notice requests awaiting review</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-full">
                                    {{ $pendingReviewsCount ?? $pendingAnnouncements->count() }} Pending
                                </span>
                            </div>
                            <div class="space-y-3">
                                @foreach($pendingAnnouncements as $pending)
                                    <div class="bg-white rounded-lg p-4 border border-amber-100 hover:shadow-md transition-shadow flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 uppercase">{{ $pending->category }}</span>
                                                <span class="text-xs text-gray-500">By {{ $pending->author->name ?? 'Anonymous' }} ({{ ucfirst($pending->author->role ?? 'student') }})</span>
                                                <span class="text-xs text-gray-400">· {{ $pending->created_at->diffForHumans() }}</span>
                                            </div>
                                            <h4 class="font-bold text-gray-900 text-md truncate">{{ $pending->title }}</h4>
                                            <p class="text-gray-600 text-sm line-clamp-2 mt-1">{{ Str::limit(strip_tags($pending->content), 120) }}</p>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0 self-end md:self-center">
                                            <a href="{{ route('announcements.show', $pending) }}" class="px-3 py-1.5 border border-gray-300 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                                                Review
                                            </a>
                                            <button onclick="openApproveModal({{ $pending->id }}, '{{ addslashes($pending->title) }}')" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                                Approve
                                            </button>
                                            <button onclick="openRejectModal({{ $pending->id }}, '{{ addslashes($pending->title) }}')" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                                Reject
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-3">
                        <h3 class="portal-section-title">Latest Announcements</h3>
                        <a href="{{ route('announcements.index') }}" class="text-uthm-blue hover:text-blue-800 text-sm font-semibold mt-2 sm:mt-0 inline-flex items-center">
                            View All <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse($announcements ?? [] as $announcement)
                            @php
                                $priorityClass = $announcement->priority === 'urgent' ? 'urgent' : ($announcement->priority === 'important' ? 'important' : 'normal');
                                $priorityColor = $announcement->priority === 'urgent' ? 'bg-red-100 text-red-700' : ($announcement->priority === 'important' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700');
                            @endphp
                            <div class="announcement-card {{ $priorityClass }} portal-card portal-card-compact">
                                <div class="flex flex-col gap-3">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                                            <h4 class="font-semibold text-gray-900 text-lg">{{ $announcement->title }}</h4>
                                            @if($announcement->priority && $announcement->priority !== 'normal')
                                                <span class="px-2 py-0.5 text-xs font-semibold {{ $priorityColor }} rounded-full">
                                                    @if($announcement->priority === 'urgent')
                                                        <i class="fas fa-exclamation-circle mr-1 animate-pulse"></i>Urgent
                                                    @else
                                                        <i class="fas fa-star mr-1"></i>Important
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-gray-600 text-sm mb-3 leading-relaxed">{{ Str::limit(strip_tags($announcement->content), 150) }}</p>
                                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-md font-medium">
                                                <i class="fas fa-tag mr-1"></i> {{ ucfirst($announcement->category ?? 'General') }}
                                            </span>
                                            <span><i class="far fa-clock mr-1"></i> {{ $announcement->created_at->diffForHumans() }}</span>
                                            <span><i class="far fa-eye mr-1"></i> {{ $announcement->view_count ?? 0 }} views</span>
                                        </div>
                                    </div>
                                    @include('announcements.partials.announcement-card-actions', [
                                        'announcement' => $announcement,
                                        'showApprove' => true,
                                    ])
                                </div>
                            </div>
                        @empty
                            <div class="portal-card text-center py-12">
                                <i class="fas fa-bullhorn text-gray-200 text-4xl mb-4"></i>
                                <h3 class="text-lg font-semibold text-gray-700">No Announcements Yet</h3>
                                <p class="text-gray-500 text-sm mt-1">Check back later or create one.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="portal-card portal-card-compact">
                            <div class="flex items-center gap-3">
                                <div class="portal-stat-icon bg-uthm-blue-light"><i class="fas fa-fire text-uthm-blue"></i></div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Trending</p>
                                    <p class="font-bold text-gray-900 text-sm">{{ $trendingTitle ?? 'No trending announcements' }}</p>
                                    <p class="text-xs text-gray-400">{{ $trendingViews ?? 0 }} views</p>
                                </div>
                            </div>
                        </div>
                        <div class="portal-card portal-card-compact">
                            <div class="flex items-center gap-3">
                                <div class="portal-stat-icon bg-green-50"><i class="fas fa-chart-line text-uthm-green"></i></div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Faculty Updates</p>
                                    <p class="font-bold text-gray-900">{{ $facultyUpdatesCount ?? 0 }} New</p>
                                    <p class="text-xs text-gray-400">This week</p>
                                </div>
                            </div>
                        </div>
                        <div class="portal-card portal-card-compact">
                            <div class="flex items-center gap-3">
                                <div class="portal-stat-icon bg-amber-50"><i class="fas fa-bell text-amber-500"></i></div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Urgent</p>
                                    <p class="font-bold text-gray-900">{{ $urgentCount ?? 0 }} Active</p>
                                    <p class="text-xs text-gray-400">High priority</p>
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
                            <a href="{{ route('staff.calendar') }}" class="text-uthm-blue hover:text-blue-800 text-xs font-semibold font-medium">View Calendar <i class="fas fa-arrow-right ml-1"></i></a>
                        </div>
                        <div class="space-y-3">
                            @forelse($upcomingEvents ?? [] as $event)
                                <div class="event-card flex items-center gap-3 p-4 bg-white/5 rounded-xl border border-gray-100 dark:border-slate-800">
                                    <div class="bg-amber-100 p-2.5 rounded-xl text-center min-w-[52px]">
                                        <div class="font-bold text-base leading-none">{{ optional($event->start_date)->format('j') }}</div>
                                        <div class="text-[10px] uppercase font-semibold text-amber-700 mt-0.5">{{ optional($event->start_date)->format('M') }}</div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 text-sm">{{ $event->title }}</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            @if($event->start_time)
                                                {{ optional($event->start_time)->format('g:i A') }}
                                            @else
                                                All Day
                                            @endif
                                            @if($event->location)
                                                · {{ $event->location }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('events.show', $event) }}" class="portal-btn-secondary text-xs px-2.5 py-1.5">View</a>
                                        <form action="{{ route('events.attend', $event) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="portal-btn-primary text-xs px-2.5 py-1.5">
                                                @if(auth()->check() && $event->isAttending(auth()->id()))
                                                    Unattend
                                                @else
                                                    Attend
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6">
                                    <p class="text-sm text-gray-500">No upcoming events</p>
                                </div>
                            @endforelse
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
                                    <span class="text-gray-600 font-medium">My Posts</span>
                                    <span class="font-bold text-uthm-blue">{{ $myAnnouncementsCount ?? 0 }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-uthm-blue h-2 rounded-full transition-all" style="width: 80%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1.5">
                                    <span class="text-gray-600 font-medium">Pending Reviews</span>
                                    <span class="font-bold text-amber-600">{{ $pendingReviewsCount ?? 0 }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-amber-500 h-2 rounded-full transition-all" style="width: 30%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1.5">
                                    <span class="text-gray-600 font-medium">Events This Month</span>
                                    <span class="font-bold text-green-600">{{ $eventsThisMonthCount ?? 0 }}</span>
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

    <style>
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

    @include('layouts.partials.portal-scripts')
    @include('announcements.partials.calendar-assets')

    <script>
        let currentAnnouncementId = null;
        let currentAnnouncementTitle = '';

        // Carousel slider controls
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.getElementById('featured-carousel');
            const prevBtn = document.getElementById('prev-featured');
            const nextBtn = document.getElementById('next-featured');
            const totalSlides = {{ isset($featuredAnnouncements) ? $featuredAnnouncements->count() : 0 }};
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
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (!csrfToken) {
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
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Announcement approved successfully!', 'success');
                    closeApproveModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showToast(data.message || 'Error approving announcement', 'error');
                }
            })
            .catch(error => {
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
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (!csrfToken) {
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
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP ${response.status}: ${text.substring(0, 200)}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Announcement rejected successfully', 'success');
                    closeRejectModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showToast(data.message || 'Error rejecting announcement', 'error');
                }
            })
            .catch(error => {
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

        window.addEventListener('click', function(event) {
            const approveModal = document.getElementById('approveModal');
            const rejectModal = document.getElementById('rejectModal');
            if (event.target === approveModal) closeApproveModal();
            if (event.target === rejectModal) closeRejectModal();
        });
    </script>
</body>
</html>
