@php
    $user = $user ?? auth()->user();
    $showCalendar = $showCalendar ?? true;
@endphp

<div class="flex flex-wrap justify-end gap-2 items-center">
    @if($showCalendar)
        @include('announcements.partials.calendar-dropdown', [
            'announcement' => $announcement,
            'compact' => true,
        ])
    @endif

    @if(($showApprove ?? false) && in_array($user->role ?? 'guest', ['admin', 'staff']) && $announcement->status === 'pending_verification')
        <button type="button"
                onclick="openDetailedVerifyModal({{ $announcement->id }})"
                class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shrink-0"
                title="Verify Announcement">
            <i class="fas fa-shield-alt mr-1"></i> Verify
        </button>
    @endif

    <a href="{{ route('announcements.show', $announcement) }}"
       class="inline-flex items-center shrink-0 px-4 py-2 bg-blue-50 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-100 transition-colors">
        View Details
        <i class="fas fa-arrow-right ml-2"></i>
    </a>
</div>
