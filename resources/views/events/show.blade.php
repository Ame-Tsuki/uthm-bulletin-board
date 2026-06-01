<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} - Event</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('layouts.partials.portal-head')
</head>
<body class="portal-body">
    @include('layouts.partials.portal-sidebar', ['user' => $user ?? Auth::user()])

    <div id="main-content" class="content-collapsed min-h-screen content-transition">
        @include('layouts.partials.portal-topbar', ['pageTitle' => 'Event Details', 'breadcrumb' => $event->title])

        @include('layouts.partials.portal-content-open')
            <div class="portal-stack-lg">
                <div class="portal-card">
                    <div class="flex flex-col md:flex-row md:items-start gap-6">
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h2>
                            <div class="flex items-center gap-3 mt-3">
                                @php
                                    $typeClasses = [
                                        'lecture' => 'bg-blue-100 text-blue-800',
                                        'deadline' => 'bg-red-100 text-red-800',
                                        'exam' => 'bg-purple-100 text-purple-800',
                                        'social' => 'bg-green-100 text-green-800',
                                        'workshop' => 'bg-amber-100 text-amber-800',
                                        'important' => 'bg-pink-100 text-pink-800',
                                        'other' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $badgeClass = $typeClasses[$event->type] ?? $typeClasses['other'];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">{{ ucfirst($event->type) }}</span>
                                @if($event->visibility === 'public')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-uthm-blue">Public</span>
                                @endif
                                <span class="text-sm text-gray-500">• {{ optional($event->start_date)->format('l, F j, Y') }}@if($event->start_time) • {{ optional($event->start_time)->format('g:i A') }}@endif</span>
                            </div>

                            @if($event->location)
                                <p class="text-sm text-gray-500 mt-2"><i class="fas fa-map-marker-alt mr-1"></i> {{ $event->location }}</p>
                            @endif

                            <div class="mt-6 text-gray-700 prose max-w-none">{!! nl2br(e($event->description)) !!}</div>
                        </div>

                        <aside class="w-full md:w-64">
                            <div class="bg-white/95 rounded-lg p-4 border border-gray-100 shadow-sm">
                                <div class="text-sm text-gray-500">Organizer</div>
                                <div class="font-medium mt-1">{{ $event->creator->name ?? ($event->user->name ?? 'Unknown') }}</div>

                                <div class="mt-4 text-sm text-gray-500">Attendees</div>
                                <div class="font-bold text-lg">{{ $event->attendees->count() }} attending</div>

                                <div class="mt-4">
                                    <form action="{{ route('events.attend', $event) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full portal-btn-primary py-2">
                                            @if(auth()->check() && $event->isAttending(auth()->id()))
                                                Unattend
                                            @else
                                                Attend
                                            @endif
                                        </button>
                                    </form>
                                    <a href="{{ route('student.calendar') }}" class="block text-center mt-2 portal-btn-secondary py-2">Back to Calendar</a>
                                </div>
                            </div>

                            <div class="mt-4 bg-white/90 rounded-lg p-3 border border-gray-100">
                                <h4 class="text-sm font-semibold">Recent Attendees</h4>
                                <ul class="mt-2 text-sm text-gray-700 space-y-1">
                                    @forelse($event->attendees->take(6) as $att)
                                        <li>{{ $att->user->name ?? 'User #' . $att->user_id }}</li>
                                    @empty
                                        <li class="text-gray-500">No attendees yet</li>
                                    @endforelse
                                </ul>
                                @if($event->attendees->count() > 6)
                                    <div class="mt-2 text-xs text-gray-500">+{{ $event->attendees->count() - 6 }} more</div>
                                @endif
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        @include('layouts.partials.portal-content-close')
    </div>

    @include('layouts.partials.portal-scripts')
</body>
</html>
