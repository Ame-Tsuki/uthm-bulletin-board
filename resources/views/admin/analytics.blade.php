<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Analytics - UTHM Bulletin Board System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .sidebar {
            transition: all 0.3s ease;
        }
        
        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .sidebar-link {
            transition: all 0.2s ease;
        }
        
        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
            padding-left: 1.5rem;
        }
        
        .active-link {
            background: rgba(255, 255, 255, 0.2);
            border-left: 4px solid #fff;
        }
        
        .badge {
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9999px;
        }
        
        .activity-item {
            transition: all 0.2s ease;
        }
        
        .activity-item:hover {
            background-color: #f9fafb;
            transform: translateX(4px);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Main Container -->
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 bg-gray-900 text-white hidden md:block">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-8">
                    <div class="gradient-bg p-3 rounded-xl">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Admin Panel</h2>
                        <p class="text-gray-400 text-sm">UTHM Bulletin System</p>
                    </div>
                </div>
                
                <nav class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center sidebar-link p-3 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-tachometer-alt mr-3 text-gray-300"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center sidebar-link p-3 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-users mr-3 text-gray-300"></i>
                        User Management
                    </a>
                    <a href="{{ route('admin.announcements.index') }}" class="flex items-center sidebar-link p-3 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-clipboard-list mr-3 text-gray-300"></i>
                        Posts & Content
                    </a>
                    <a href="{{ route('admin.moderation') }}" class="flex items-center sidebar-link p-3 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-flag mr-3 text-gray-300"></i>
                        Moderation
                    </a>
                    <a href="{{ route('admin.calendar') }}" class="flex items-center sidebar-link p-3 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-calendar-alt mr-3 text-gray-300"></i>
                        Calendar
                    </a>
                    <a href="{{ route('admin.featured-posts') }}" class="flex items-center sidebar-link p-3 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-star mr-3 text-gray-300"></i>
                        Featured Posts
                    </a>
                    <a href="{{ route('admin.analytics') }}" class="flex items-center sidebar-link active-link p-3 rounded-lg">
                        <i class="fas fa-chart-bar mr-3 text-gray-300"></i>
                        Analytics
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center sidebar-link p-3 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-cog mr-3 text-gray-300"></i>
                        System Settings
                    </a>
                </nav>
                
                <div class="mt-12 p-4 bg-gray-800 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">{{ Auth::user()->name }}</p>
                            <p class="text-sm text-gray-400">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm border-b">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <button id="menuToggle" class="md:hidden mr-4 text-gray-600">
                                <i class="fas fa-bars text-xl"></i>
                            </button>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-800">Analytics Dashboard</h1>
                                <p class="text-gray-600 text-sm">Track system performance and user engagement</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <button class="relative text-gray-600 hover:text-gray-800">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-xs text-white rounded-full h-5 w-5 flex items-center justify-center">3</span>
                            </button>
                            <div class="relative">
                                <button id="userMenu" class="flex items-center space-x-2 focus:outline-none">
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                    </div>
                                    <span class="font-medium hidden md:inline">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </button>
                                <!-- Dropdown Menu -->
                                <div id="dropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-10">
                                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i>Profile
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2"></i>Settings
                                    </a>
                                    <hr class="my-2">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Mobile Sidebar Overlay -->
            <div id="mobileSidebar" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 md:hidden hidden">
                <div class="absolute left-0 top-0 h-full w-64 bg-gray-900 text-white">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-8">
                            <div class="flex items-center space-x-3">
                                <div class="gradient-bg p-3 rounded-xl">
                                    <i class="fas fa-shield-alt text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold">Admin Panel</h2>
                                    <p class="text-gray-400 text-sm">UTHM Bulletin System</p>
                                </div>
                            </div>
                            <button id="closeMenu" class="text-white">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <nav class="space-y-1">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 rounded-lg bg-gray-800">
                                <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800">
                                <i class="fas fa-users mr-3"></i>User Management
                            </a>
                            <a href="{{ route('admin.announcements.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800">
                                <i class="fas fa-clipboard-list mr-3"></i>Posts & Content
                            </a>
                            <a href="{{ route('admin.calendar') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800">
                                <i class="fas fa-calendar-alt mr-3"></i>Calendar
                            </a>
                            <a href="{{ route('admin.analytics') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800">
                                <i class="fas fa-chart-bar mr-3"></i>Analytics
                            </a>
                            <a href="{{ route('admin.settings.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800">
                                <i class="fas fa-cog mr-3"></i>Settings
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <main class="p-6">
                <!-- Time Period Selection -->
                <div class="bg-white rounded-lg shadow p-6 mb-8">
                    <div class="flex flex-wrap gap-4 items-center">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Report Period</label>
                            <select id="periodFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="day">Today</option>
                                <option value="week">This Week</option>
                                <option value="month" selected>This Month</option>
                                <option value="year">This Year</option>
                            </select>
                        </div>
                        <button id="generateReportBtn" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 mt-6">Generate Report</button>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <!-- New Users -->
                    <div class="stat-card bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-gray-600 font-semibold text-sm">New Users</h3>
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                        <p class="text-3xl font-bold text-gray-900" id="newUsersCount">-</p>
                        <p class="text-sm text-gray-500 mt-2">Registered this period</p>
                    </div>

                    <!-- New Announcements -->
                    <div class="stat-card bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-gray-600 font-semibold text-sm">New Announcements</h3>
                            <i class="fas fa-bullhorn text-yellow-600 text-xl"></i>
                        </div>
                        <p class="text-3xl font-bold text-gray-900" id="newAnnouncementsCount">-</p>
                        <p class="text-sm text-gray-500 mt-2">Posted this period</p>
                    </div>

                    <!-- New Events -->
                    <div class="stat-card bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-gray-600 font-semibold text-sm">New Events</h3>
                            <i class="fas fa-calendar-alt text-red-600 text-xl"></i>
                        </div>
                        <p class="text-3xl font-bold text-gray-900" id="newEventsCount">-</p>
                        <p class="text-sm text-gray-500 mt-2">Created this period</p>
                    </div>

                    <!-- Active Sessions -->
                    <div class="stat-card bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-gray-600 font-semibold text-sm">Active Sessions</h3>
                            <i class="fas fa-chart-line text-green-600 text-xl"></i>
                        </div>
                        <p class="text-3xl font-bold text-gray-900" id="activeSessions">-</p>
                        <p class="text-sm text-gray-500 mt-2">Currently active</p>
                    </div>
                </div>

                <!-- Content Statistics -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Announcements Breakdown -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Announcements Status</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-gray-600">Published</span>
                                    <span id="publishedCount" class="font-semibold text-gray-900">0</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div id="publishedBar" class="bg-green-600 h-2 rounded-full" style="width: 0%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-gray-600">Pending</span>
                                    <span id="pendingCount" class="font-semibold text-gray-900">0</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div id="pendingBar" class="bg-yellow-600 h-2 rounded-full" style="width: 0%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between mb-2">
                                    <span class="text-sm text-gray-600">Rejected</span>
                                    <span id="rejectedCount" class="font-semibold text-gray-900">0</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div id="rejectedBar" class="bg-red-600 h-2 rounded-full" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Events Overview -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Events Overview</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
                                <span class="text-gray-700 font-medium">Upcoming Events</span>
                                <span id="upcomingEvents" class="text-2xl font-bold text-blue-600">0</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-purple-50 rounded-lg">
                                <span class="text-gray-700 font-medium">Past Events</span>
                                <span id="pastEvents" class="text-2xl font-bold text-purple-600">0</span>
                            </div>
                            <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
                                <span class="text-gray-700 font-medium">Total Events</span>
                                <span id="totalEvents" class="text-2xl font-bold text-green-600">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Activity Timeline -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Recent Activity</h3>
                    <div id="activityFeed" class="space-y-4">
                        <p class="text-gray-500 text-center py-8">Loading activity...</p>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t px-6 py-4">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-gray-600 text-sm">
                        <p>&copy; {{ date('Y') }} UTHM Bulletin Board System. All rights reserved.</p>
                    </div>
                    <div class="flex items-center space-x-4 mt-2 md:mt-0">
                        <span class="text-sm text-gray-600">v1.2.1</span>
                        <span class="text-sm text-gray-600">Last updated: Today, 08:45 AM</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        document.getElementById('menuToggle')?.addEventListener('click', function() {
            document.getElementById('mobileSidebar').classList.remove('hidden');
        });

        document.getElementById('closeMenu')?.addEventListener('click', function() {
            document.getElementById('mobileSidebar').classList.add('hidden');
        });

        // User dropdown
        document.getElementById('userMenu')?.addEventListener('click', function(e) {
            e.stopPropagation();
            document.getElementById('dropdownMenu').classList.toggle('hidden');
        });

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdownMenu');
            const userMenu = document.getElementById('userMenu');
            if (!userMenu?.contains(event.target) && !dropdown?.contains(event.target)) {
                dropdown?.classList.add('hidden');
            }
        });

        // Load analytics on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadAnalytics();
            loadActivityFeed();
            
            document.getElementById('generateReportBtn')?.addEventListener('click', function() {
                const period = document.getElementById('periodFilter').value;
                generateReport(period);
            });
        });

        function loadAnalytics() {
            fetch('/api/admin/analytics')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const analytics = data.data;
                        
                        // Update key metrics
                        document.getElementById('newUsersCount').textContent = analytics.new_users || 0;
                        document.getElementById('newAnnouncementsCount').textContent = analytics.new_announcements || 0;
                        document.getElementById('newEventsCount').textContent = analytics.new_events || 0;
                        document.getElementById('activeSessions').textContent = analytics.active_sessions || 0;
                        
                        // Update content stats
                        const totalAnnouncements = (analytics.announcements?.published || 0) + 
                                                  (analytics.announcements?.pending || 0) + 
                                                  (analytics.announcements?.rejected || 0);
                        
                        document.getElementById('publishedCount').textContent = analytics.announcements?.published || 0;
                        document.getElementById('pendingCount').textContent = analytics.announcements?.pending || 0;
                        document.getElementById('rejectedCount').textContent = analytics.announcements?.rejected || 0;
                        
                        const publishedPct = totalAnnouncements > 0 ? ((analytics.announcements?.published || 0) / totalAnnouncements) * 100 : 0;
                        const pendingPct = totalAnnouncements > 0 ? ((analytics.announcements?.pending || 0) / totalAnnouncements) * 100 : 0;
                        const rejectedPct = totalAnnouncements > 0 ? ((analytics.announcements?.rejected || 0) / totalAnnouncements) * 100 : 0;
                        
                        document.getElementById('publishedBar').style.width = publishedPct + '%';
                        document.getElementById('pendingBar').style.width = pendingPct + '%';
                        document.getElementById('rejectedBar').style.width = rejectedPct + '%';
                        
                        // Update events
                        document.getElementById('upcomingEvents').textContent = analytics.events?.upcoming || 0;
                        document.getElementById('pastEvents').textContent = analytics.events?.past || 0;
                        document.getElementById('totalEvents').textContent = (analytics.events?.upcoming || 0) + (analytics.events?.past || 0);
                    }
                })
                .catch(error => console.error('Error loading analytics:', error));
        }

        function loadActivityFeed() {
            fetch('/api/admin/activity')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('activityFeed');
                    if (data.success && data.data && data.data.length > 0) {
                        container.innerHTML = '';
                        data.data.forEach(activity => {
                            const icon = getActivityIcon(activity.type);
                            const color = getActivityColor(activity.type);
                            container.innerHTML += `
                                <div class="activity-item flex items-start p-4 border-b border-gray-100 hover:bg-gray-50 transition">
                                    <div class="bg-${color}-100 p-2 rounded-lg mr-4">
                                        <i class="fas ${icon} text-${color}-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">${escapeHtml(activity.description)}</p>
                                        <p class="text-xs text-gray-500 mt-1">${formatDate(activity.created_at)}</p>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        container.innerHTML = '<p class="text-gray-500 text-center py-8">No recent activity found</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading activity feed:', error);
                    document.getElementById('activityFeed').innerHTML = '<p class="text-gray-500 text-center py-8">Error loading activity</p>';
                });
        }

        function getActivityIcon(type) {
            const icons = {
                'user_created': 'fa-user-plus',
                'announcement_created': 'fa-bullhorn',
                'event_created': 'fa-calendar-plus',
                'announcement_approved': 'fa-check-circle',
                'announcement_rejected': 'fa-ban',
                'user_verified': 'fa-check-double',
                'default': 'fa-bell'
            };
            return icons[type] || icons.default;
        }

        function getActivityColor(type) {
            const colors = {
                'user_created': 'blue',
                'announcement_created': 'yellow',
                'event_created': 'red',
                'announcement_approved': 'green',
                'announcement_rejected': 'red',
                'user_verified': 'green',
                'default': 'gray'
            };
            return colors[type] || colors.default;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);
            
            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
            if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
            if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
            return date.toLocaleDateString();
        }

        function generateReport(period) {
            const generateBtn = document.getElementById('generateReportBtn');
            generateBtn.disabled = true;
            generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';
            
            fetch('/api/admin/report', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ period: period })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Report generated successfully!', 'success');
                    // You could open a modal with report data or trigger download
                    if (data.data && data.data.download_url) {
                        window.open(data.data.download_url, '_blank');
                    }
                } else {
                    showNotification(data.message || 'Error generating report', 'error');
                }
            })
            .catch(error => {
                console.error('Error generating report:', error);
                showNotification('Error generating report', 'error');
            })
            .finally(() => {
                generateBtn.disabled = false;
                generateBtn.innerHTML = 'Generate Report';
            });
        }

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} transition-opacity duration-300`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                    <span>${escapeHtml(message)}</span>
                </div>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Refresh data every 30 seconds
        setInterval(() => {
            loadAnalytics();
            loadActivityFeed();
        }, 30000);
    </script>
</body>
</html>