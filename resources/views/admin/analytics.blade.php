@extends('layouts.admin')

@section('title', 'Admin Analytics - UTHM Bulletin Board System')
@section('page_title', 'Analytics Dashboard')
@section('page_subtitle', 'Track system performance and user engagement')

@section('styles')
<style>
    .activity-item {
        transition: all 0.2s ease;
    }
    
    .activity-item:hover {
        background-color: #f9fafb;
        transform: translateX(4px);
    }
</style>
@endsection

@section('content')
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
@endsection

@section('scripts')
<script>
    const analyticsUrl = @json(route('admin.api.analytics'));
    const activityUrl = @json(route('admin.api.activity'));
    const reportUrl = @json(route('admin.api.report'));

    // Load analytics on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadAnalytics();
        loadActivityFeed();

        document.getElementById('periodFilter')?.addEventListener('change', function() {
            loadAnalytics();
        });
        
        document.getElementById('generateReportBtn')?.addEventListener('click', function() {
            const period = document.getElementById('periodFilter').value;
            generateReport(period);
        });
    });

    function loadAnalytics() {
        const period = document.getElementById('periodFilter')?.value || 'month';
        fetch(`${analyticsUrl}?period=${encodeURIComponent(period)}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
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

                    updateLastRefreshed();
                }
            })
            .catch(error => console.error('Error loading analytics:', error));
    }

    function updateLastRefreshed() {
        const el = document.getElementById('lastUpdatedText');
        if (el) {
            el.textContent = 'Last updated: ' + new Date().toLocaleString();
        }
    }

    function loadActivityFeed() {
        fetch(activityUrl, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
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
        
        fetch(reportUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ period: period })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Report generated successfully!', 'success');
                if (data.data) {
                    loadAnalytics();
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
@endsection