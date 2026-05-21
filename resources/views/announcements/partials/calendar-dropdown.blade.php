@php
    $menuId = 'calendar-menu-' . $announcement->id;
    $calendar = $calendar ?? app(\App\Services\AnnouncementCalendarService::class)->forAnnouncement($announcement, auth()->id());
    $compact = $compact ?? false;
@endphp

<div class="calendar-dropdown relative inline-flex shrink-0"
     data-announcement-id="{{ $announcement->id }}"
     data-in-calendar="{{ ($calendar['in_user_calendar'] ?? false) ? '1' : '0' }}"
     data-add-url="{{ route('announcements.add-to-calendar', $announcement) }}">
    <button type="button"
            onclick="toggleCalendarMenu(event, '{{ $menuId }}')"
            aria-expanded="false"
            aria-haspopup="true"
            aria-controls="{{ $menuId }}"
            class="calendar-dropdown-btn inline-flex items-center whitespace-nowrap {{ $compact ? 'px-3 py-2 text-sm bg-indigo-50 text-indigo-700 font-medium rounded-lg hover:bg-indigo-100 border border-indigo-100' : 'px-5 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700' }} transition-colors"
            title="Add to Calendar">
        <i class="fas fa-calendar-plus {{ $compact ? 'text-sm' : 'mr-2' }}"></i>
        @if(!$compact)
            <span>Add to Calendar</span>
            <i class="fas fa-chevron-up ml-2 text-xs opacity-80"></i>
        @else
            <span class="ml-1.5">Calendar</span>
            <i class="fas fa-chevron-down ml-1.5 text-[10px] opacity-70"></i>
        @endif
    </button>
    <div id="{{ $menuId }}"
         class="calendar-menu {{ $compact ? 'calendar-menu--down' : 'calendar-menu--up' }}"
         role="menu">
        @if($calendar['in_user_calendar'] ?? false)
            <a href="{{ $calendar['calendar_url'] }}"
               role="menuitem"
               class="calendar-menu-item calendar-menu-item--added">
                <i class="fas fa-check-circle text-green-600 w-4 text-center"></i>
                <span>View in My Calendar</span>
            </a>
        @else
            <button type="button"
                    role="menuitem"
                    class="calendar-menu-item calendar-menu-item--action w-full text-left"
                    onclick="addAnnouncementToCalendar({{ $announcement->id }}, '{{ $menuId }}', this)">
                <i class="fas fa-calendar-check text-indigo-600 w-4 text-center"></i>
                <span>Add to My Calendar</span>
            </button>
        @endif
        <a href="{{ route('announcements.calendar', $announcement) }}"
           role="menuitem"
           class="calendar-menu-item">
            <i class="fas fa-download text-gray-600 w-4 text-center"></i>
            <span>Download .ics</span>
        </a>
    </div>
</div>
