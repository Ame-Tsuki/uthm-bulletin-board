<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - UTHM Bulletin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('layouts.partials.portal-head')
</head>
<body class="portal-body">
    @include('layouts.partials.portal-sidebar', ['user' => $user ?? Auth::user()])

    <div id="main-content" class="content-collapsed min-h-screen content-transition">
        @include('layouts.partials.portal-topbar', ['pageTitle' => 'Student Dashboard', 'breadcrumb' => 'Overview'])

        @include('layouts.partials.portal-content-open')
                <div class="portal-stack-lg">
                <!-- Welcome Hero -->
                <div class="portal-welcome relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center justify-between relative z-10 gap-3">
                        <div>
                            <p class="text-blue-200 text-xs font-medium mb-0.5">{{ now()->format('l, F j, Y') }}</p>
                            <h2 class="portal-welcome-title">Welcome back, {{ Auth::user()->name }}!</h2>
                            <p class="text-blue-100/90 text-sm">Your student dashboard overview for today.</p>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <span class="portal-badge">
                                <i class="fas fa-graduation-cap mr-1.5"></i> Active Student
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
                                <div class="portal-stat-icon bg-green-100"><i class="fas fa-university text-green-600"></i></div>
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Faculty</p>
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
                                <div class="portal-stat-icon bg-amber-100"><i class="fas fa-book text-amber-600"></i></div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Groups</p>
                                    <p class="font-bold text-gray-900">
                                        <a href="{{ route('community-hub') }}" class="inline-flex items-center gap-2 text-gray-900 hover:text-uthm-blue">
                                            {{ $groupsCount ?? 0 }} Joined <i class="fas fa-arrow-right text-xs"></i>
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Featured Announcements -->
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
                                                'urgent' => ['bg' => 'from-red-50 to-orange-50', 'badge' => 'bg-red-100 text-red-700', 'icon' => 'exclamation-circle', 'label' => 'Urgent'],
                                                'important' => ['bg' => 'from-yellow-50 to-amber-50', 'badge' => 'bg-yellow-100 text-yellow-700', 'icon' => 'exclamation-triangle', 'label' => 'Important'],
                                                'academic' => ['bg' => 'from-blue-50 to-indigo-50', 'badge' => 'bg-blue-100 text-blue-700', 'icon' => 'graduation-cap', 'label' => 'Academic'],
                                                'events' => ['bg' => 'from-purple-50 to-pink-50', 'badge' => 'bg-purple-100 text-purple-700', 'icon' => 'calendar-alt', 'label' => 'Events'],
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
                                                @if($announcement->priority)
                                                    <span class="px-2 py-0.5 text-xs font-semibold {{ $priorityColor }} rounded-full">{{ ucfirst($announcement->priority) }}</span>
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
                                            'showApprove' => false,
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
                                    </div>
                                </div>
                            </div>
                            <div class="portal-card portal-card-compact">
                                <div class="flex items-center gap-3">
                                    <div class="portal-stat-icon bg-green-50"><i class="fas fa-chart-line text-uthm-green"></i></div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Faculty Updates</p>
                                        <p class="font-bold text-gray-900">{{ $facultyUpdatesCount ?? 0 }} New</p>
                                    </div>
                                </div>
                            </div>
                            <div class="portal-card portal-card-compact">
                                <div class="flex items-center gap-3">
                                    <div class="portal-stat-icon bg-amber-50"><i class="fas fa-bell text-amber-500"></i></div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Urgent</p>
                                        <p class="font-bold text-gray-900">{{ $urgentCount ?? 0 }} Unread</p>
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
                                <a href="{{ route('student.calendar') }}" class="text-uthm-blue hover:text-blue-800 text-xs font-semibold">View Calendar <i class="fas fa-arrow-right ml-1"></i></a>
                            </div>
                            <div class="space-y-3">
                                @forelse($upcomingEvents ?? [] as $event)
                                    <div class="event-card flex items-center gap-4 p-4 bg-white/5 rounded-xl">
                                        <div class="bg-amber-100 p-3 rounded-xl text-center min-w-[56px]">
                                            <div class="font-bold text-lg leading-none">{{ optional($event->start_date)->format('j') }}</div>
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
                                        <a href="{{ route('calendar') }}" class="portal-btn-primary text-xs px-3 py-1.5 shrink-0">View</a>
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
                                <a href="{{ route('announcements.index') }}" class="portal-quick-link bg-blue-50 hover:bg-blue-100">
                                    <i class="fas fa-newspaper text-blue-600 text-lg mb-2 block"></i>
                                    <p class="text-xs font-semibold text-gray-800">Announcements</p>
                                </a>
                                <a href="{{ route('announcements.my-announcements') }}" class="portal-quick-link bg-green-50 hover:bg-green-100">
                                    <i class="fas fa-user-edit text-green-600 text-lg mb-2 block"></i>
                                    <p class="text-xs font-semibold text-gray-800">My Announcements</p>
                                </a>
                                <a href="{{ route('calendar') }}" class="portal-quick-link bg-amber-50 hover:bg-amber-100">
                                    <i class="fas fa-calendar-alt text-amber-600 text-lg mb-2 block"></i>
                                    <p class="text-xs font-semibold text-gray-800">Calendar</p>
                                </a>
                                <a href="{{ route('community-hub') }}" class="portal-quick-link bg-purple-50 hover:bg-purple-100">
                                    <i class="fas fa-users text-purple-600 text-lg mb-2 block"></i>
                                    <p class="text-xs font-semibold text-gray-800">Community Hub</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
        @include('layouts.partials.portal-content-close')
    </div>

    @include('layouts.partials.portal-scripts')
    @include('announcements.partials.calendar-assets')
</body>
</html>
