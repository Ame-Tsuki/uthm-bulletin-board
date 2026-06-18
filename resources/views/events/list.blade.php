<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Events - UTHM Digital Bulletin Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('layouts.partials.portal-head')
</head>
<body class="portal-body">
    @include('layouts.partials.portal-sidebar', ['user' => $user ?? Auth::user()])

    <!-- Main Content -->
    <div id="main-content" class="content-collapsed min-h-screen content-transition">
        @include('layouts.partials.portal-topbar', [
            'user' => $user ?? Auth::user(),
            'pageTitle' => 'All Events',
            'breadcrumb' => 'List View',
        ])

        <!-- Calendar Content -->
        @include('layouts.partials.portal-content-open')
        <div class="portal-card mb-4">
            <form method="GET" action="{{ route('events.list') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1 flex flex-col md:flex-row gap-3">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search events by title, description or location..." class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm w-full">
                    </div>
                    <!-- Type Filter -->
                    <div class="w-full md:w-48">
                        <select name="type" onchange="this.form.submit()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Event Types</option>
                            <option value="lecture" {{ $type === 'lecture' ? 'selected' : '' }}>Lectures</option>
                            <option value="deadline" {{ $type === 'deadline' ? 'selected' : '' }}>Deadlines</option>
                            <option value="exam" {{ $type === 'exam' ? 'selected' : '' }}>Exams</option>
                            <option value="social" {{ $type === 'social' ? 'selected' : '' }}>Social Events</option>
                            <option value="workshop" {{ $type === 'workshop' ? 'selected' : '' }}>Workshops</option>
                            <option value="important" {{ $type === 'important' ? 'selected' : '' }}>Important Dates</option>
                            <option value="other" {{ $type === 'other' ? 'selected' : '' }}>Others</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2 bg-uthm-blue text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        Search
                    </button>
                    <a href="{{ route('calendar') }}" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                        Calendar View
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden p-6">
            @if($events->isEmpty())
                <div class="text-center py-12 text-gray-500 bg-gray-50 rounded-xl border border-dashed">
                    <i class="fas fa-calendar-times text-4xl mb-3 text-gray-300"></i>
                    <p class="font-medium text-gray-600">No events found matching your filter/search</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($events as $event)
                        <div class="p-4 hover:shadow-md transition bg-white border rounded-xl flex flex-col md:flex-row md:items-center justify-between gap-4 cursor-pointer" onclick="window.location.href='{{ route('events.show', $event) }}'">
                            <div class="flex items-center gap-4">
                                <div class="text-center bg-gray-50 p-2.5 rounded-lg border min-w-[75px]">
                                    <div class="font-bold text-lg text-gray-900">{{ optional($event->start_date)->format('d') }}</div>
                                    <div class="text-xs uppercase text-gray-500 font-semibold">{{ optional($event->start_date)->format('M') }}</div>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-base">{{ $event->title }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <i class="far fa-clock mr-1"></i> {{ $event->all_day ? 'All day' : ($event->start_time ? optional($event->start_time)->format('g:i A') : 'No time set') }}
                                        @if($event->location)
                                            • <i class="fas fa-map-marker-alt mx-1"></i> {{ $event->location }}
                                        @endif
                                        • <span class="text-gray-400">By {{ $event->creator->name ?? 'System' }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                @php
                                    $typeColors = [
                                        'lecture' => 'bg-blue-100 text-blue-800',
                                        'deadline' => 'bg-red-100 text-red-800',
                                        'exam' => 'bg-purple-100 text-purple-800',
                                        'social' => 'bg-green-100 text-green-800',
                                        'workshop' => 'bg-amber-100 text-amber-800',
                                        'important' => 'bg-pink-100 text-pink-800',
                                        'other' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $badgeColor = $typeColors[$event->type] ?? $typeColors['other'];
                                @endphp
                                <span class="inline-block px-3 py-1 text-xs rounded-full font-semibold {{ $badgeColor }}">{{ ucfirst($event->type) }}</span>
                                <a href="{{ route('events.show', $event) }}" class="portal-btn-primary text-xs px-3 py-1.5" onclick="event.stopPropagation()">View Details</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
        @include('layouts.partials.portal-content-close')
    </div>

    @include('layouts.partials.portal-scripts')
</body>
</html>
