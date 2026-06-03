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
                <span class="notification-badge absolute -top-1 -right-1 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 border-2 border-white rounded-full">
                    {{ $filteredUnreads->count() }}
                </span>
            @else
                <span class="notification-badge hidden absolute -top-1 -right-1 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 border-2 border-white rounded-full"></span>
            @endif
        </button>

        <div id="globalNotificationMenu" class="hidden absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-xl z-50 border border-gray-100 overflow-hidden">
            <div class="py-3 px-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                <span class="text-sm font-bold text-gray-700">Notifications</span>
                @if($filteredUnreads->count() > 0)
                    <form id="markAllReadForm" action="{{ route('notifications.markAllRead') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-blue-600 hover:text-blue-800 focus:outline-none">Mark all read</button>
                    </form>
                @endif
            </div>

            <div class="max-h-72 overflow-y-auto notification-list">
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
            const wrapper = document.getElementById('globalNotificationWrapper');
            const badge = wrapper.querySelector('.notification-badge');
            const listContainer = wrapper.querySelector('.notification-list');

            // Routes
            const unreadCountUrl = "{{ route('notifications.unread-count') }}";
            const notificationsIndexUrl = "{{ route('notifications.index') }}";
            const markAllUrl = "{{ route('notifications.markAllRead') }}";

            function setBadgeCount(count) {
                if (!badge) return;
                if (count > 0) {
                    badge.textContent = count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            async function fetchUnreadCount() {
                try {
                    const res = await fetch(unreadCountUrl, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    setBadgeCount(data.count ?? data.count ?? 0);
                } catch (e) {
                    // ignore
                }
            }

            async function fetchNotificationsList() {
                try {
                    const res = await fetch(notificationsIndexUrl, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    if (!res.ok) return;
                    const json = await res.json();
                    const notifs = json.notifications ?? json;
                    renderNotifications(notifs || []);
                } catch (e) {
                    // ignore
                }
            }

            function renderNotifications(notifs) {
                if (!listContainer) return;
                if (!notifs || notifs.length === 0) {
                    listContainer.innerHTML = `
                        <div class="px-4 py-8 text-center text-gray-500 flex flex-col items-center">
                            <i class="far fa-bell-slash text-3xl mb-2 text-gray-300"></i>
                            <p class="text-sm">You have no new notifications.</p>
                        </div>`;
                    return;
                }

                const items = notifs.map(n => {
                    const title = (n.data && (n.data.title || n.data.message)) ? (n.data.title ?? n.data.message) : 'Notification';
                    const message = n.data && n.data.message ? n.data.message : '';
                    const created = n.created_at ?? n.updated_at ?? '';
                    // Use the mark-as-read route so clicks go through the server and notifications are persisted as read/deleted
                    const readUrl = `/notifications/${n.id}/read`;

                    return `<a href="${escapeHtml(readUrl)}" class="block px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition">
                                <p class="text-sm text-gray-800 font-medium leading-tight">${escapeHtml(title)}</p>
                                ${message ? `<p class="text-xs text-gray-500 mt-1 truncate">${escapeHtml(message)}</p>` : ''}
                                <p class="text-[11px] text-gray-400 mt-1">${escapeHtml(created)}</p>
                            </a>`;
                }).join('');

                listContainer.innerHTML = items;
            }

            function escapeHtml(str) {
                if (!str) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            // Poll unread count every 15s
            fetchUnreadCount();
            setInterval(fetchUnreadCount, 15000);

            if (toggle && menu) {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    menu.classList.toggle('hidden');
                    if (!menu.classList.contains('hidden')) {
                        // load fresh notifications when opening
                        fetchNotificationsList();
                    }
                });

                document.addEventListener('click', function(event) {
                    if (!menu.contains(event.target) && !toggle.contains(event.target)) {
                        menu.classList.add('hidden');
                    }
                });
            }

            // AJAX submit for mark all as read
            const markAllForm = document.getElementById('markAllReadForm');
            if (markAllForm) {
                markAllForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').content;
                        const formData = new URLSearchParams(new FormData(markAllForm));
                        const res = await fetch(markAllForm.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                            },
                            body: formData.toString()
                        });
                        if (!res.ok) {
                            // fallback to default form submit
                            markAllForm.submit();
                            return;
                        }

                        const payload = await res.json();
                        // Clear badge and show empty state
                        setBadgeCount(0);
                        if (listContainer) {
                            listContainer.innerHTML = `
                                <div class="px-4 py-8 text-center text-gray-500 flex flex-col items-center">
                                    <i class="far fa-bell-slash text-3xl mb-2 text-gray-300"></i>
                                    <p class="text-sm">You have no new notifications.</p>
                                </div>`;
                        }
                    } catch (err) {
                        markAllForm.submit();
                    }
                });
            }
        });
    </script>
@endif
