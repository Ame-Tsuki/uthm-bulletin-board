@extends('layouts.admin')

@section('title', 'Admin Analytics - UTHM Bulletin Board System')
@section('page_title', 'Analytics & Reports Dashboard')
@section('page_subtitle', 'Monitor performance metrics, engagement trends, and generate official audit reports')

@section('styles')
<style>
    .activity-item {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .activity-item:hover {
        background-color: #f8fafc;
        transform: translateX(6px);
    }

    .analytics-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .analytics-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.05), 0 8px 10px -6px rgb(0 0 0 / 0.05);
    }

    .chart-container {
        position: relative;
        width: 100%;
        transition: all 0.3s ease;
    }
    
    /* Elegant Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endsection

@section('content')
<!-- Header Controls -->
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <div class="p-3 rounded-xl bg-blue-50 text-blue-600">
            <i class="fas fa-filter text-lg"></i>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Time Period Filter</label>
            <select id="periodFilter" class="bg-transparent border-0 font-bold text-slate-800 text-lg focus:ring-0 focus:outline-none cursor-pointer pr-8 pl-0">
                <option value="day">Today (24h)</option>
                <option value="week">This Week (7d)</option>
                <option value="month" selected>This Month (30d)</option>
                <option value="year">This Year (12m)</option>
            </select>
        </div>
    </div>

    <div class="flex items-center gap-3 flex-wrap">
        <!-- Generate Report Button -->
        <button id="generateReportBtn" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-3 rounded-xl transition duration-200 shadow-lg shadow-blue-500/20 active:scale-95">
            <i class="fas fa-sync-alt"></i>
            <span>Refresh Analytics</span>
        </button>

        <!-- Export Dropdown -->
        <div class="relative inline-block text-left" id="exportDropdownContainer">
            <button id="exportDropdownBtn" type="button" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-5 py-3 rounded-xl transition duration-200 active:scale-95" aria-expanded="false" aria-haspopup="true">
                <i class="fas fa-download"></i>
                <span>Export Report</span>
                <i class="fas fa-chevron-down text-xs ml-1"></i>
            </button>
            <div id="exportMenu" class="hidden absolute right-0 mt-2 w-60 rounded-xl bg-white shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none z-50 py-1 border border-slate-100">
                <button onclick="triggerPDFExport()" class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition flex items-center gap-3 font-medium">
                    <i class="far fa-file-pdf text-red-500 text-base"></i>
                    <span>Export PDF Audit Report</span>
                </button>
                <button onclick="triggerCSVExport()" class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 hover:text-green-600 transition flex items-center gap-3 font-medium">
                    <i class="far fa-file-excel text-green-500 text-base"></i>
                    <span>Export Raw Data (CSV)</span>
                </button>
                <button onclick="triggerPNGExport('trendChart', 'System_Activity_Trends')" class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 hover:text-purple-600 transition flex items-center gap-3 font-medium">
                    <i class="far fa-image text-purple-500 text-base"></i>
                    <span>Download Activity Graph</span>
                </button>
                <button onclick="triggerPNGExport('communityTrendChart', 'Community_Engagement_Trends')" class="w-full text-left px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition flex items-center gap-3 font-medium">
                    <i class="far fa-image text-indigo-500 text-base"></i>
                    <span>Download Community Graph</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Section: System Overview Metrics -->
<h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
    <span>System Core Metrics</span>
    <span class="h-[1px] bg-slate-200 flex-1"></span>
</h2>

<!-- Key Performance Metrics Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card: New Users -->
    <div class="analytics-card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-slate-400 font-semibold text-sm tracking-wide uppercase">New Users</span>
            <div class="p-3 rounded-xl bg-blue-50 text-blue-600">
                <i class="fas fa-users text-lg"></i>
            </div>
        </div>
        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight" id="newUsersCount">-</h3>
        <p class="text-sm text-slate-500 mt-2 flex items-center gap-1.5">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>Registrations this period</span>
        </p>
    </div>

    <!-- Card: Announcements -->
    <div class="analytics-card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-slate-400 font-semibold text-sm tracking-wide uppercase">Announcements</span>
            <div class="p-3 rounded-xl bg-amber-50 text-amber-600">
                <i class="fas fa-bullhorn text-lg"></i>
            </div>
        </div>
        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight" id="newAnnouncementsCount">-</h3>
        <p class="text-sm text-slate-500 mt-2 flex items-center gap-1.5">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>Broadcasts posted</span>
        </p>
    </div>

    <!-- Card: New Events -->
    <div class="analytics-card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-slate-400 font-semibold text-sm tracking-wide uppercase">New Events</span>
            <div class="p-3 rounded-xl bg-rose-50 text-rose-600">
                <i class="fas fa-calendar-alt text-lg"></i>
            </div>
        </div>
        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight" id="newEventsCount">-</h3>
        <p class="text-sm text-slate-500 mt-2 flex items-center gap-1.5">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>Events created</span>
        </p>
    </div>

    <!-- Card: Active Sessions -->
    <div class="analytics-card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-slate-400 font-semibold text-sm tracking-wide uppercase">Active Sessions</span>
            <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600">
                <i class="fas fa-chart-line text-lg"></i>
            </div>
        </div>
        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight" id="activeSessions">-</h3>
        <p class="text-sm text-slate-500 mt-2 flex items-center gap-1.5">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
            <span>Active database sessions</span>
        </p>
    </div>
</div>

<!-- Section: Community Hub Engagement Metrics -->
<h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
    <span>Community Hub Engagement</span>
    <span class="h-[1px] bg-slate-200 flex-1"></span>
</h2>

<!-- Community Engagement Metrics Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card: Total groups -->
    <div class="analytics-card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-slate-400 font-semibold text-sm tracking-wide uppercase">Active Groups</span>
            <div class="p-3 rounded-xl bg-indigo-50 text-indigo-600">
                <i class="fas fa-users-cog text-lg"></i>
            </div>
        </div>
        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight" id="totalGroupsCount">-</h3>
        <p class="text-sm text-slate-500 mt-2 flex items-center gap-1.5">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
            <span>Active communities</span>
        </p>
    </div>

    <!-- Card: Community Posts -->
    <div class="analytics-card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-slate-400 font-semibold text-sm tracking-wide uppercase">Group Posts</span>
            <div class="p-3 rounded-xl bg-violet-50 text-violet-600">
                <i class="fas fa-mail-bulk text-lg"></i>
            </div>
        </div>
        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight" id="newGroupPostsCount">-</h3>
        <p class="text-xs text-slate-500 mt-2 truncate flex items-center gap-1.5">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>New: <span id="periodGroupPosts" class="font-bold">0</span> | Total: <span id="totalGroupPosts" class="font-bold">0</span></span>
        </p>
    </div>

    <!-- Card: Comments -->
    <div class="analytics-card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-slate-400 font-semibold text-sm tracking-wide uppercase">Post Comments</span>
            <div class="p-3 rounded-xl bg-teal-50 text-teal-600">
                <i class="fas fa-comments text-lg"></i>
            </div>
        </div>
        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight" id="newGroupCommentsCount">-</h3>
        <p class="text-xs text-slate-500 mt-2 truncate flex items-center gap-1.5">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>New: <span id="periodGroupComments" class="font-bold">0</span> | Total: <span id="totalGroupComments" class="font-bold">0</span></span>
        </p>
    </div>

    <!-- Card: Likes -->
    <div class="analytics-card bg-white rounded-2xl border border-slate-100 shadow-sm p-6 relative overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <span class="text-slate-400 font-semibold text-sm tracking-wide uppercase">Post Likes</span>
            <div class="p-3 rounded-xl bg-rose-50 text-rose-600">
                <i class="fas fa-heart text-lg"></i>
            </div>
        </div>
        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight" id="newGroupLikesCount">-</h3>
        <p class="text-xs text-slate-500 mt-2 truncate flex items-center gap-1.5">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>New: <span id="periodGroupLikes" class="font-bold">0</span> | Total: <span id="totalGroupLikes" class="font-bold">0</span></span>
        </p>
    </div>
</div>

<!-- Secondary Stats (Views) -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-sm text-white p-6 relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 opacity-15 text-9xl">
            <i class="fas fa-eye"></i>
        </div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-blue-100 font-semibold text-sm tracking-wide uppercase">Total Announcement Views</span>
            <div class="p-2 bg-white/10 rounded-xl text-white">
                <i class="fas fa-chart-bar"></i>
            </div>
        </div>
        <h3 class="text-4xl font-extrabold tracking-tight" id="totalViewsCount">-</h3>
        <p class="text-sm text-blue-200 mt-2">All-time accumulated view logs</p>
    </div>

    <div class="bg-gradient-to-r from-violet-600 to-purple-700 rounded-2xl shadow-sm text-white p-6 relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 opacity-15 text-9xl">
            <i class="fas fa-fire"></i>
        </div>
        <div class="flex items-center justify-between mb-4">
            <span class="text-purple-100 font-semibold text-sm tracking-wide uppercase">Average Views per Announcement</span>
            <div class="p-2 bg-white/10 rounded-xl text-white">
                <i class="fas fa-percentage"></i>
            </div>
        </div>
        <h3 class="text-4xl font-extrabold tracking-tight" id="avgViewsCount">-</h3>
        <p class="text-sm text-purple-200 mt-2">Overall user engagement factor</p>
    </div>
</div>

<!-- Main Analytics Visualization Charts -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Trends Graph -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-800">System Activity Trends Over Time</h3>
                <p class="text-slate-500 text-sm">Registrations, announcements, and events</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="flex items-center gap-1 text-xs text-blue-600 font-semibold bg-blue-50 px-2.5 py-1 rounded-lg">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span> Users
                </span>
                <span class="flex items-center gap-1 text-xs text-amber-600 font-semibold bg-amber-50 px-2.5 py-1 rounded-lg">
                    <span class="w-2 h-2 bg-amber-500 rounded-full"></span> Posts
                </span>
                <span class="flex items-center gap-1 text-xs text-rose-600 font-semibold bg-rose-50 px-2.5 py-1 rounded-lg">
                    <span class="w-2 h-2 bg-rose-500 rounded-full"></span> Events
                </span>
            </div>
        </div>
        <div class="chart-container h-80">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    <!-- Announcement Status Breakdown Pie -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Announcement Status</h3>
            <p class="text-slate-500 text-sm mb-6">Breakdown of submitted posts</p>
        </div>
        <div class="chart-container h-60 mb-6 flex justify-center items-center">
            <canvas id="statusChart"></canvas>
        </div>
        <div class="grid grid-cols-3 gap-2 text-center text-xs">
            <div class="p-2 bg-slate-50 rounded-xl">
                <p class="text-emerald-600 font-bold text-base" id="publishedCount">0</p>
                <p class="text-slate-500 font-medium">Published</p>
            </div>
            <div class="p-2 bg-slate-50 rounded-xl">
                <p class="text-amber-600 font-bold text-base" id="pendingCount">0</p>
                <p class="text-slate-500 font-medium">Pending</p>
            </div>
            <div class="p-2 bg-slate-50 rounded-xl">
                <p class="text-rose-600 font-bold text-base" id="rejectedCount">0</p>
                <p class="text-slate-500 font-medium">Rejected</p>
            </div>
        </div>
    </div>
</div>

<!-- Section: Community Hub Analytics Visualizer -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Community Hub Trends Graph -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Community Engagement Trends</h3>
                <p class="text-slate-500 text-sm">Posts, comments, and post likes over time</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="flex items-center gap-1 text-xs text-indigo-600 font-semibold bg-indigo-50 px-2.5 py-1 rounded-lg">
                    <span class="w-2 h-2 bg-indigo-500 rounded-full"></span> Posts
                </span>
                <span class="flex items-center gap-1 text-xs text-teal-600 font-semibold bg-teal-50 px-2.5 py-1 rounded-lg">
                    <span class="w-2 h-2 bg-teal-500 rounded-full"></span> Comments
                </span>
                <span class="flex items-center gap-1 text-xs text-rose-600 font-semibold bg-rose-50 px-2.5 py-1 rounded-lg">
                    <span class="w-2 h-2 bg-rose-500 rounded-full"></span> Likes
                </span>
            </div>
        </div>
        <div class="chart-container h-80">
            <canvas id="communityTrendChart"></canvas>
        </div>
    </div>

    <!-- Events Breakdown Panel -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-6">Events Overview</h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center p-4 bg-blue-50/50 border border-blue-100 rounded-2xl">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-blue-50 text-blue-600 rounded-xl">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <span class="text-slate-700 font-semibold">Upcoming Events</span>
                </div>
                <span id="upcomingEvents" class="text-2xl font-black text-blue-600">0</span>
            </div>
            
            <div class="flex justify-between items-center p-4 bg-purple-50/50 border border-purple-100 rounded-2xl">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-purple-50 text-purple-600 rounded-xl">
                        <i class="fas fa-history"></i>
                    </div>
                    <span class="text-slate-700 font-semibold">Past Events</span>
                </div>
                <span id="pastEvents" class="text-2xl font-black text-purple-600">0</span>
            </div>
            
            <div class="flex justify-between items-center p-4 bg-emerald-50/50 border border-emerald-100 rounded-2xl">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <span class="text-slate-700 font-semibold">Total Events</span>
                </div>
                <span id="totalEvents" class="text-2xl font-black text-emerald-600">0</span>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Stats & Activity Log -->
<div class="grid grid-cols-1 gap-8">
    <!-- Timeline Activity Logs Feed -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-6">Recent System Activity Logs</h3>
        <div id="activityFeed" class="space-y-4 max-h-[350px] overflow-y-auto pr-2 custom-scrollbar">
            <p class="text-slate-400 text-center py-12">Loading system activity feed...</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Visual Assets and Export Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    // System Routes
    const analyticsUrl = @json(route('admin.api.analytics'));
    const activityUrl = @json(route('admin.api.activity'));
    const reportUrl = @json(route('admin.api.report'));
    
    // Global Data Cache for report downloads
    let currentAnalyticsData = null;
    let trendChartInstance = null;
    let statusChartInstance = null;
    let communityTrendChartInstance = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Init Menu Controls
        initExportDropdown();
        
        // Load Initial Panel Data
        loadAnalyticsData();
        loadActivityFeed();

        // Change Filter Listener
        document.getElementById('periodFilter')?.addEventListener('change', function() {
            loadAnalyticsData();
        });
        
        // Refresh Trigger
        document.getElementById('generateReportBtn')?.addEventListener('click', function() {
            loadAnalyticsData();
            loadActivityFeed();
        });
    });

    // Toggle logic for dropdown
    function initExportDropdown() {
        const btn = document.getElementById('exportDropdownBtn');
        const menu = document.getElementById('exportMenu');
        const container = document.getElementById('exportDropdownContainer');
        
        if (btn && menu) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.classList.toggle('hidden');
            });
            
            document.addEventListener('click', function(event) {
                if (!container.contains(event.target)) {
                    menu.classList.add('hidden');
                }
            });
        }
    }

    // Refresh visual content
    function loadAnalyticsData() {
        const period = document.getElementById('periodFilter')?.value || 'month';
        const generateBtn = document.getElementById('generateReportBtn');
        
        if (generateBtn) {
            generateBtn.disabled = true;
            generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
        }

        fetch(`${analyticsUrl}?period=${encodeURIComponent(period)}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const analytics = data.data;
                    currentAnalyticsData = analytics;
                    
                    // Update key metrics UI
                    document.getElementById('newUsersCount').textContent = analytics.new_users ?? 0;
                    document.getElementById('newAnnouncementsCount').textContent = analytics.new_announcements ?? 0;
                    document.getElementById('newEventsCount').textContent = analytics.new_events ?? 0;
                    document.getElementById('activeSessions').textContent = analytics.active_sessions ?? 0;
                    document.getElementById('totalViewsCount').textContent = Number(analytics.total_views ?? 0).toLocaleString();
                    document.getElementById('avgViewsCount').textContent = analytics.avg_views_per_announcement ?? 0;
                    
                    // Update Community Hub metrics UI
                    document.getElementById('totalGroupsCount').textContent = analytics.total_groups ?? 0;
                    document.getElementById('newGroupPostsCount').textContent = analytics.new_group_posts ?? 0;
                    document.getElementById('periodGroupPosts').textContent = analytics.new_group_posts ?? 0;
                    document.getElementById('totalGroupPosts').textContent = analytics.total_group_posts ?? 0;
                    
                    document.getElementById('newGroupCommentsCount').textContent = analytics.new_group_comments ?? 0;
                    document.getElementById('periodGroupComments').textContent = analytics.new_group_comments ?? 0;
                    document.getElementById('totalGroupComments').textContent = analytics.total_group_comments ?? 0;
                    
                    document.getElementById('newGroupLikesCount').textContent = analytics.new_group_likes ?? 0;
                    document.getElementById('periodGroupLikes').textContent = analytics.new_group_likes ?? 0;
                    document.getElementById('totalGroupLikes').textContent = analytics.total_group_likes ?? 0;

                    // Update content stats counts
                    document.getElementById('publishedCount').textContent = analytics.announcements?.published ?? 0;
                    document.getElementById('pendingCount').textContent = analytics.announcements?.pending ?? 0;
                    document.getElementById('rejectedCount').textContent = analytics.announcements?.rejected ?? 0;
                    
                    // Update events numbers
                    document.getElementById('upcomingEvents').textContent = analytics.events?.upcoming ?? 0;
                    document.getElementById('pastEvents').textContent = analytics.events?.past ?? 0;
                    document.getElementById('totalEvents').textContent = (analytics.events?.upcoming ?? 0) + (analytics.events?.past ?? 0);

                    // Render Charts
                    renderTrendGraph(analytics.trends);
                    renderStatusPieChart(analytics.announcements);
                    renderCommunityTrendGraph(analytics.trends?.labels, analytics.community_trends);
                }
            })
            .catch(error => {
                console.error('Error loading analytics:', error);
                showNotification('Failed to fetch analytics metrics.', 'error');
            })
            .finally(() => {
                if (generateBtn) {
                    generateBtn.disabled = false;
                    generateBtn.innerHTML = '<i class="fas fa-sync-alt"></i> <span>Refresh Analytics</span>';
                }
            });
    }

    // Chart.js: Trend Chart Builder
    function renderTrendGraph(trendData) {
        const ctx = document.getElementById('trendChart').getContext('2d');
        
        if (trendChartInstance) {
            trendChartInstance.destroy();
        }

        if (!trendData || !trendData.labels) return;

        // Custom gradients
        const blueGradient = ctx.createLinearGradient(0, 0, 0, 300);
        blueGradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
        blueGradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        const amberGradient = ctx.createLinearGradient(0, 0, 0, 300);
        amberGradient.addColorStop(0, 'rgba(245, 158, 11, 0.4)');
        amberGradient.addColorStop(1, 'rgba(245, 158, 11, 0.0)');

        const roseGradient = ctx.createLinearGradient(0, 0, 0, 300);
        roseGradient.addColorStop(0, 'rgba(239, 68, 68, 0.4)');
        roseGradient.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

        trendChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [
                    {
                        label: 'New Users',
                        data: trendData.users,
                        borderColor: '#3b82f6',
                        backgroundColor: blueGradient,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointBackgroundColor: '#3b82f6',
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Announcements',
                        data: trendData.announcements,
                        borderColor: '#f59e0b',
                        backgroundColor: amberGradient,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointBackgroundColor: '#f59e0b',
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Events',
                        data: trendData.events,
                        borderColor: '#ef4444',
                        backgroundColor: roseGradient,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointBackgroundColor: '#ef4444',
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { weight: 500 } }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: { color: '#64748b', precision: 0 }
                    }
                }
            }
        });
    }

    // Chart.js: Pie Chart Builder
    function renderStatusPieChart(announcements) {
        const ctx = document.getElementById('statusChart').getContext('2d');
        
        if (statusChartInstance) {
            statusChartInstance.destroy();
        }

        if (!announcements) return;

        const published = announcements.published || 0;
        const pending = announcements.pending || 0;
        const rejected = announcements.rejected || 0;

        if (published === 0 && pending === 0 && rejected === 0) {
            // Empty state placeholder
            statusChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['No Data'],
                    datasets: [{
                        data: [1],
                        backgroundColor: ['#e2e8f0']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
            return;
        }

        statusChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Published', 'Pending', 'Rejected'],
                datasets: [{
                    data: [published, pending, rejected],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '70%'
            }
        });
    }

    // Chart.js: Community Trends Chart Builder
    function renderCommunityTrendGraph(labels, communityTrends) {
        const ctx = document.getElementById('communityTrendChart').getContext('2d');
        
        if (communityTrendChartInstance) {
            communityTrendChartInstance.destroy();
        }

        if (!labels || !communityTrends) return;

        // Custom gradients
        const indigoGradient = ctx.createLinearGradient(0, 0, 0, 300);
        indigoGradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
        indigoGradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

        const tealGradient = ctx.createLinearGradient(0, 0, 0, 300);
        tealGradient.addColorStop(0, 'rgba(20, 184, 166, 0.4)');
        tealGradient.addColorStop(1, 'rgba(20, 184, 166, 0.0)');

        const pinkGradient = ctx.createLinearGradient(0, 0, 0, 300);
        pinkGradient.addColorStop(0, 'rgba(244, 63, 94, 0.4)');
        pinkGradient.addColorStop(1, 'rgba(244, 63, 94, 0.0)');

        communityTrendChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Group Posts',
                        data: communityTrends.posts,
                        borderColor: '#6366f1',
                        backgroundColor: indigoGradient,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointBackgroundColor: '#6366f1',
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Post Comments',
                        data: communityTrends.comments,
                        borderColor: '#14b8a6',
                        backgroundColor: tealGradient,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointBackgroundColor: '#14b8a6',
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Post Likes',
                        data: communityTrends.likes,
                        borderColor: '#f43f5e',
                        backgroundColor: pinkGradient,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointBackgroundColor: '#f43f5e',
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { weight: 500 } }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: { color: '#64748b', precision: 0 }
                    }
                }
            }
        });
    }

    // Render activity feed
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
                            <div class="activity-item flex items-start p-4 border-b border-slate-50 hover:bg-slate-50/50 rounded-xl transition">
                                <div class="bg-${color}-55 p-2.5 rounded-xl mr-4 text-${color}-600 bg-${color}-50">
                                    <i class="fas ${icon} text-base"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-slate-800 leading-snug">${escapeHtml(activity.description)}</p>
                                    <p class="text-xs text-slate-400 mt-1">${formatDate(activity.created_at)}</p>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    container.innerHTML = '<p class="text-slate-400 text-center py-12">No recent activity logs found</p>';
                }
            })
            .catch(error => {
                console.error('Error loading activity feed:', error);
                document.getElementById('activityFeed').innerHTML = '<p class="text-red-500 text-center py-12">Error loading activity feed</p>';
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
            'announcement_created': 'amber',
            'event_created': 'rose',
            'announcement_approved': 'emerald',
            'announcement_rejected': 'rose',
            'user_verified': 'emerald',
            'default': 'slate'
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
        if (diffMins < 60) return `${diffMins} min${diffMins > 1 ? 's' : ''} ago`;
        if (diffHours < 24) return `${diffHours} hr${diffHours > 1 ? 's' : ''} ago`;
        if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
        return date.toLocaleDateString();
    }

    // ============================================
    // EXPORT FUNCTIONS
    // ============================================

    // Export PDF Audit Report
    function triggerPDFExport() {
        const exportMenu = document.getElementById('exportMenu');
        if (exportMenu) exportMenu.classList.add('hidden');

        if (!currentAnalyticsData) {
            showNotification('No analytics data available to export.', 'error');
            return;
        }

        const data = currentAnalyticsData;
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4');
        
        const pageWidth = doc.internal.pageSize.width;
        const pageHeight = doc.internal.pageSize.height;

        // Page 1 Header
        doc.setFillColor(30, 41, 59); // Dark blue header
        doc.rect(0, 0, pageWidth, 42, 'F');
        
        doc.setTextColor(255, 255, 255);
        doc.setFont("helvetica", "bold");
        doc.setFontSize(16);
        doc.text("UTHM DIGITAL BULLETIN BOARD SYSTEM", 15, 18);
        doc.setFont("helvetica", "normal");
        doc.setFontSize(9);
        doc.setTextColor(203, 213, 225);
        doc.text("ADMINISTRATOR AUDIT & ANALYTICS PERFORMANCE REPORT", 15, 26);

        doc.setFillColor(59, 130, 246); // Accent line
        doc.rect(0, 42, pageWidth, 3, 'F');

        // Document Details
        doc.setTextColor(30, 41, 59);
        doc.setFont("helvetica", "bold");
        doc.setFontSize(20);
        doc.text("Analytics Audit Report", 15, 60);

        doc.setFont("helvetica", "normal");
        doc.setFontSize(9.5);
        doc.setTextColor(100, 116, 139);
        doc.text(`Generated On: ${new Date().toLocaleString()}`, 15, 70);
        doc.text(`Reporting Period: ${data.period.toUpperCase()}`, 15, 76);
        doc.text(`Generated By: ${document.querySelector('#userMenu span')?.textContent?.trim() || 'Administrator'}`, 15, 82);

        doc.setDrawColor(226, 232, 240);
        doc.line(15, 90, pageWidth - 15, 90);

        // 1. Executive Summary
        doc.setFont("helvetica", "bold");
        doc.setFontSize(13);
        doc.setTextColor(30, 41, 59);
        doc.text("1. Executive Analytics Summary", 15, 101);
        
        doc.setFont("helvetica", "normal");
        doc.setFontSize(9.5);
        doc.setTextColor(71, 85, 105);
        
        const summaryText = `This report provides the official audit report of the UTHM Digital Bulletin Board System's key performance indexes during the ${data.period} filter period. System telemetry registered ${data.new_users} new user registrations, ${data.new_announcements} announcements, and ${data.new_events} events. In addition, the Community Hub recorded substantial engagement with ${data.total_groups} active groups, ${data.new_group_posts} posts during this period (totaling ${data.total_group_posts}), ${data.new_group_comments} comments (totaling ${data.total_group_comments}), and ${data.new_group_likes} likes (totaling ${data.total_group_likes}). Cumulative announcement view logs recorded ${data.total_views} hits, and active peak server/database sessions logged ${data.active_sessions} concurrent connects.`;
        const splitText = doc.splitTextToSize(summaryText, pageWidth - 30);
        doc.text(splitText, 15, 110);

        // 2. Key Performance Metrics Table
        doc.setFont("helvetica", "bold");
        doc.setFontSize(13);
        doc.setTextColor(30, 41, 59);
        doc.text("2. System & Community Core Metrics", 15, 145);

        const tableY = 152;
        const rowHeight = 7.0;

        // Table Header
        doc.setFillColor(241, 245, 249);
        doc.rect(15, tableY, pageWidth - 30, rowHeight, 'F');
        doc.setTextColor(30, 41, 59);
        doc.setFont("helvetica", "bold");
        doc.setFontSize(8.5);
        doc.text("Metric Description", 20, tableY + 4.5);
        doc.text("Report Value", 85, tableY + 4.5);
        doc.text("Measurement Description", 115, tableY + 4.5);

        const rows = [
            ["New Users Registered", String(data.new_users), "Created user profiles during period"],
            ["New Announcements", String(data.new_announcements), "Announcements submitted by users"],
            ["New Events Created", String(data.new_events), "Activities logged into calendar"],
            ["Peak Active Sessions", String(data.active_sessions), "Peak concurrent connections logged"],
            ["Total Announcement Views", String(data.total_views), "Cumulative visual hits on posts"],
            ["Active Community Groups", String(data.total_groups), "Total communities hosted in hub"],
            ["New Group Posts", String(data.new_group_posts), `New group postings (Total: ${data.total_group_posts})`],
            ["New Post Comments", String(data.new_group_comments), `New comments posted (Total: ${data.total_group_comments})`],
            ["New Post Likes", String(data.new_group_likes), `Likes registered (Total: ${data.total_group_likes})`]
        ];

        doc.setFont("helvetica", "normal");
        doc.setFontSize(8.5);
        doc.setTextColor(71, 85, 105);
        rows.forEach((row, idx) => {
            const currY = tableY + rowHeight + (idx * rowHeight);
            if (idx % 2 === 1) {
                doc.setFillColor(248, 250, 252);
                doc.rect(15, currY, pageWidth - 30, rowHeight, 'F');
            }
            doc.setDrawColor(241, 245, 249);
            doc.line(15, currY + rowHeight, pageWidth - 15, currY + rowHeight);

            doc.text(row[0], 20, currY + 4.5);
            doc.text(row[1], 85, currY + 4.5);
            doc.text(row[2], 115, currY + 4.5);
        });

        // Footer Page 1
        doc.setFontSize(7.5);
        doc.setTextColor(148, 163, 184);
        doc.text("UTHM Bulletin Board Administration System - Confidential Audit", 15, pageHeight - 10);
        doc.text("Page 1 of 2", pageWidth - 28, pageHeight - 10);

        // --- Page 2: Visual Charts and Live Logs ---
        doc.addPage();

        doc.setFillColor(30, 41, 59);
        doc.rect(0, 0, pageWidth, 18, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFont("helvetica", "bold");
        doc.setFontSize(11);
        doc.text("System Audit Report - Data Charts & Logs", 15, 12);
        
        doc.setFillColor(59, 130, 246);
        doc.rect(0, 18, pageWidth, 2, 'F');

        doc.setTextColor(30, 41, 59);
        doc.setFont("helvetica", "bold");
        doc.setFontSize(13);
        doc.text("3. Chart & Graphical Visual Analysis", 15, 30);

        // Render Trend Chart PNG into PDF
        const trendCanvas = document.getElementById('trendChart');
        if (trendCanvas) {
            const trendPNG = trendCanvas.toDataURL('image/png', 1.0);
            doc.addImage(trendPNG, 'PNG', 15, 35, 90, 45);
        }

        // Render Community Trend Chart PNG into PDF
        const commCanvas = document.getElementById('communityTrendChart');
        if (commCanvas) {
            const commPNG = commCanvas.toDataURL('image/png', 1.0);
            doc.addImage(commPNG, 'PNG', 110, 35, 90, 45);
        }

        doc.setFont("helvetica", "italic");
        doc.setFontSize(8);
        doc.setTextColor(148, 163, 184);
        doc.text("Figure 3.1: Core activity trend logs", 15, 84);
        doc.text("Figure 3.2: Community Hub engagement trends", 110, 84);

        // Recent Activity Timeline Table
        doc.setFont("helvetica", "bold");
        doc.setFontSize(13);
        doc.setTextColor(30, 41, 59);
        doc.text("4. Recent Activity Log Entries", 15, 97);

        const actTableY = 104;
        const actRowH = 8.0;

        // Table Header
        doc.setFillColor(241, 245, 249);
        doc.rect(15, actTableY, pageWidth - 30, actRowH, 'F');
        doc.setTextColor(30, 41, 59);
        doc.setFont("helvetica", "bold");
        doc.setFontSize(9);
        doc.text("Log Description", 20, actTableY + 5.0);
        doc.text("Timestamp", 152, actTableY + 5.0);

        doc.setFont("helvetica", "normal");
        doc.setTextColor(71, 85, 105);

        const activities = Array.from(document.querySelectorAll('.activity-item')).slice(0, 14);
        if (activities.length > 0) {
            activities.forEach((act, i) => {
                const currY = actTableY + actRowH + (i * actRowH);
                if (i % 2 === 1) {
                    doc.setFillColor(248, 250, 252);
                    doc.rect(15, currY, pageWidth - 30, actRowH, 'F');
                }
                doc.setDrawColor(241, 245, 249);
                doc.line(15, currY + actRowH, pageWidth - 15, currY + actRowH);

                const desc = act.querySelector('.flex-1 p.font-semibold')?.textContent?.trim() || '';
                const time = act.querySelector('.flex-1 p.text-xs')?.textContent?.trim() || '';
                const truncated = desc.length > 70 ? desc.substring(0, 67) + '...' : desc;

                doc.text(truncated, 20, currY + 5.0);
                doc.text(time, 152, currY + 5.0);
            });
        } else {
            doc.text("No log entries recorded.", 20, actTableY + actRowH + 5.0);
        }

        // Footer Page 2
        doc.setFontSize(7.5);
        doc.setTextColor(148, 163, 184);
        doc.text("UTHM Bulletin Board Administration System - Confidential Audit", 15, pageHeight - 10);
        doc.text("Page 2 of 2", pageWidth - 28, pageHeight - 10);

        doc.save(`UTHM_Analytics_Audit_${data.period}_${Date.now()}.pdf`);
        showNotification('PDF Audit Report exported successfully.', 'success');
    }

    // Export Raw CSV
    function triggerCSVExport() {
        const exportMenu = document.getElementById('exportMenu');
        if (exportMenu) exportMenu.classList.add('hidden');

        if (!currentAnalyticsData) {
            showNotification('No analytics data available to export.', 'error');
            return;
        }

        const data = currentAnalyticsData;
        let csv = "data:text/csv;charset=utf-8,";
        
        // Block 1: Overview
        csv += "UTHM DIGITAL BULLETIN BOARD SYSTEM - SYSTEM ANALYTICS AUDIT\n";
        csv += `Filter Period,${data.period.toUpperCase()}\n`;
        csv += `Export Generated At,${new Date().toISOString()}\n\n`;

        csv += "METRIC DESCRIPTION,VALUE\n";
        csv += `New Registered Users,${data.new_users}\n`;
        csv += `New Announcements Created,${data.new_announcements}\n`;
        csv += `New Events Created,${data.new_events}\n`;
        csv += `Peak Active Connections,${data.active_sessions}\n`;
        csv += `Total Announcement Views,${data.total_views}\n`;
        csv += `Average Post Engagement Views,${data.avg_views_per_announcement}\n`;
        csv += `Active Community Groups,${data.total_groups}\n`;
        csv += `New Group Posts in Period,${data.new_group_posts}\n`;
        csv += `Total Group Posts,${data.total_group_posts}\n`;
        csv += `New Comments in Period,${data.new_group_comments}\n`;
        csv += `Total Comments,${data.total_group_comments}\n`;
        csv += `New Likes in Period,${data.new_group_likes}\n`;
        csv += `Total Likes,${data.total_group_likes}\n\n`;

        // Block 2: Post status
        csv += "ANNOUNCEMENT STATUS BREAKDOWN,COUNT\n";
        csv += `Published,${data.announcements?.published ?? 0}\n`;
        csv += `Pending,${data.announcements?.pending ?? 0}\n`;
        csv += `Rejected,${data.announcements?.rejected ?? 0}\n\n`;

        // Block 3: Events Status
        csv += "EVENTS STATUS,COUNT\n";
        csv += `Upcoming Events,${data.events?.upcoming ?? 0}\n`;
        csv += `Past Events,${data.events?.past ?? 0}\n\n`;

        // Block 4: Logs
        csv += "SYSTEM ACTIVITY LOG,TIMESTAMP\n";
        const activities = Array.from(document.querySelectorAll('.activity-item'));
        activities.forEach(act => {
            const desc = act.querySelector('.flex-1 p.font-semibold')?.textContent?.trim() || '';
            const time = act.querySelector('.flex-1 p.text-xs')?.textContent?.trim() || '';
            csv += `"${desc.replace(/"/g, '""')}","${time.replace(/"/g, '""')}"\n`;
        });

        const encoded = encodeURI(csv);
        const link = document.createElement("a");
        link.setAttribute("href", encoded);
        link.setAttribute("download", `UTHM_Analytics_Export_${data.period}_${Date.now()}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showNotification('CSV audit report downloaded successfully.', 'success');
    }

    // Export individual Chart/PNG
    function triggerPNGExport(canvasId, filename) {
        const exportMenu = document.getElementById('exportMenu');
        if (exportMenu) exportMenu.classList.add('hidden');

        const canvas = document.getElementById(canvasId);
        if (!canvas) {
            showNotification('Target chart not found.', 'error');
            return;
        }

        const url = canvas.toDataURL('image/png', 1.0);
        const link = document.createElement('a');
        link.href = url;
        link.download = `${filename}_${Date.now()}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showNotification('Chart image downloaded successfully.', 'success');
    }

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-xl text-white z-50 shadow-lg ${
            type === 'success' ? 'bg-slate-900 border border-slate-800' : 'bg-rose-600'
        } transition-opacity duration-300 flex items-center gap-2.5`;
        notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle text-emerald-500' : 'fa-exclamation-circle'} text-lg"></i>
            <span class="font-medium text-sm">${escapeHtml(message)}</span>
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
</script>
@endsection