@if(Auth::check())
    <div class="relative me-4" id="globalNotificationWrapper">
        <button id="globalNotificationToggle" type="button" class="relative text-gray-600 hover:text-gray-800 focus:outline-none transition mt-1">
            <i class="fas fa-bell text-xl"></i>
            @php
                $filteredUnreads = collect();
                foreach (Auth::user()->unreadNotifications as $n) {
                    if (isset($n->data['announcement_id'])) {
                        $announcement = \App\Models\Announcement::find($n->data['announcement_id']);
                        if ($announcement && !$announcement->is_official) {
                            continue;
                        }
                    }
                    $filteredUnreads->push($n);
                }
            @endphp
            @if($filteredUnreads->count() > 0)
                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 border-2 border-white rounded-full">
                    {{ $filteredUnreads->count() }}
                </span>
            @endif
        </button>

        <div id="globalNotificationMenu" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-xl z-50 border border-gray-100 overflow-hidden">
            <div class="py-3 px-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                <span class="text-sm font-bold text-gray-700">Notifications</span>
                @if($filteredUnreads->count() > 0)
                    <form action="{{ route('notifications.markAllRead') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-blue-600 hover:text-blue-800 focus:outline-none">Mark all read</button>
                    </form>
                @endif
            </div>

            <div class="max-h-72 overflow-y-auto">
                @if($filteredUnreads->count() > 0)
                    @foreach($filteredUnreads as $notification)
                        <a href="{{ route('notifications.read', $notification->id) }}" class="block px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition">
                            <p class="text-sm text-gray-800 font-medium leading-tight">{{ $notification->data['title'] ?? $notification->data['message'] ?? 'Notification' }}</p>
                            @if(!empty($notification->data['message']))
                                <p class="text-xs text-gray-500 mt-1 truncate">{{ $notification->data['message'] }}</p>
                            @endif
                            <p class="text-[11px] text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </a>
                    @endforeach
                @else
                    <div class="px-4 py-8 text-center text-gray-500 flex flex-col items-center">
                        <i class="far fa-bell-slash text-3xl mb-2 text-gray-300"></i>
                        <p class="text-sm">You have no new notifications.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('globalNotificationToggle');
            const menu = document.getElementById('globalNotificationMenu');

            if (toggle && menu) {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    menu.classList.toggle('hidden');
                });

                // Close when clicking outside
                document.addEventListener('click', function(event) {
                    if (!menu.contains(event.target) && !toggle.contains(event.target)) {
                        menu.classList.add('hidden');
                    }
                });
            }
        });
    </script>
@endif
