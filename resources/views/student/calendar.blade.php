<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Calendar - UTHM Digital Bulletin Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('layouts.partials.portal-head')
    <style>
        .calendar-day {
            position: relative;
            background: white;
            border-right: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            min-height: 110px;
            padding: 6px;
            overflow: hidden;
            transition: background 0.2s ease;
        }

        .calendar-day:hover {
            background: #f8fafc;
        }

        .calendar-day.other-month {
            background: #f9fafb;
            color: #9ca3af;
        }

        .calendar-day.today .day-number {
            background: #1a73e8;
            color: white;
        }

        .day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .day-number {
            width: 28px;
            height: 28px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 500;
        }

        .add-event-btn {
            opacity: 0;
            transition: opacity 0.2s ease;
            color: #6b7280;
        }

        .calendar-day:hover .add-event-btn {
            opacity: 1;
        }

        .day-events {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .calendar-event {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        .calendar-event:hover {
            opacity: 0.9;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            grid-auto-rows: minmax(110px, auto);
            width: 100%;
        }

        .calendar-weekday {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 12px;
            text-align: center;
            font-weight: 500;
            color: #5f6368;
            font-size: 14px;
        }
        
        .event-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 4px;
        }
        
        .event-lecture { background-color: #0056a6; }
        .event-deadline { background-color: #dc3545; }
        .event-exam { background-color: #6f42c1; }
        .event-social { background-color: #6ea342; }
        .event-workshop { background-color: #ffc107; }
        .event-other { background-color: #6c757d; }
        .event-important { background-color: #8b5cf6; }
        
        .synced-event { position: relative; }
        .synced-event::after {
            content: '\f1a0';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 2px;
            right: 2px;
            font-size: 10px;
            color: #4285f4;
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #0056a6;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 24px;
            border-radius: 8px;
            color: white;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }
        .toast-success { background-color: #10b981; }
        .toast-error { background-color: #ef4444; }
        .toast-info { background-color: #3b82f6; }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body class="portal-body">
    @include('layouts.partials.portal-sidebar', ['user' => $user ?? Auth::user()])

    <!-- Main Content -->
    <div id="main-content" class="content-collapsed min-h-screen content-transition">
        @include('layouts.partials.portal-topbar', [
            'user' => $user ?? Auth::user(),
            'pageTitle' => 'Calendar',
            'breadcrumbId' => 'current-month-year',
            'headerActionsHtml' => '<button type="button" onclick="openEventModal()" class="portal-btn-primary text-sm inline-flex items-center px-3 py-2"><i class="fas fa-plus mr-2"></i><span class="hidden md:inline">Add Event</span></button>',
        ])

        <!-- Calendar Content -->
        @include('layouts.partials.portal-content-open')
                <!-- Calendar Controls -->
                <div class="portal-card mb-3">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div>
                            <h2 class="portal-section-title text-lg">Academic Calendar</h2>
                            <p class="text-gray-500 text-sm">Track your lectures, deadlines, and events</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="flex bg-gray-100 p-1 rounded-lg">
                                <button id="month-view" class="px-3 py-1 rounded text-sm font-medium bg-white shadow text-uthm-blue">Month</button>
                                <button id="week-view" class="px-3 py-1 rounded text-sm font-medium text-gray-600 hover:text-gray-900">Week</button>
                                <button id="day-view" class="px-3 py-1 rounded text-sm font-medium text-gray-600 hover:text-gray-900">Day</button>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button id="prev-month" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button id="today-btn" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">Today</button>
                                <button id="next-month" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex flex-wrap gap-1.5">
                        <button class="event-filter active px-3 py-1 bg-uthm-blue text-white rounded-full text-sm hover:bg-blue-700 transition" data-type="all">All Events</button>
                        <button class="event-filter px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition" data-type="lecture">
                            <span class="event-dot event-lecture"></span> Lectures
                        </button>
                        <button class="event-filter px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition" data-type="deadline">
                            <span class="event-dot event-deadline"></span> Deadlines
                        </button>
                        <button class="event-filter px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition" data-type="exam">
                            <span class="event-dot event-exam"></span> Exams
                        </button>
                        <button class="event-filter px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition" data-type="social">
                            <span class="event-dot event-social"></span> Social Events
                        </button>
                        <button class="event-filter px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition" data-type="workshop">
                            <span class="event-dot event-workshop"></span> Workshops
                        </button>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
                    <div class="grid grid-cols-7 bg-white border-b">
                        <div class="calendar-weekday">Sunday</div>
                        <div class="calendar-weekday">Monday</div>
                        <div class="calendar-weekday">Tuesday</div>
                        <div class="calendar-weekday">Wednesday</div>
                        <div class="calendar-weekday">Thursday</div>
                        <div class="calendar-weekday">Friday</div>
                        <div class="calendar-weekday">Saturday</div>
                    </div>
                    <div id="calendar-grid" class="calendar-grid">
                        <div class="col-span-7 p-8 text-center">
                            <div class="loading-spinner mx-auto mb-2"></div>
                            <p class="text-gray-500">Loading calendar...</p>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Events & Sidebar -->
                <div class="portal-grid-2-1 mt-3">
                    <div>
                        <div class="portal-card">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="portal-section-title">Upcoming Events This Week</h3>
                                <a href="#" class="text-uthm-blue hover:text-blue-700 text-sm font-medium">
                                    View All <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                            <div id="upcoming-events" class="space-y-4">
                                <div class="text-center py-8 text-gray-500">
                                    <div class="loading-spinner mx-auto mb-2"></div>
                                    <p>Loading events...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Calendar Statistics -->
                        <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-2">
                            <div class="portal-card portal-card-compact">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-3 rounded-lg mr-3">
                                        <i class="fas fa-chalkboard-teacher text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Lectures This Month</p>
                                        <p id="stat-lectures" class="font-bold text-gray-900">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="portal-card portal-card-compact">
                                <div class="flex items-center">
                                    <div class="bg-red-100 p-3 rounded-lg mr-3">
                                        <i class="fas fa-exclamation-circle text-red-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Upcoming Deadlines</p>
                                        <p id="stat-deadlines" class="font-bold text-gray-900">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="portal-card portal-card-compact">
                                <div class="flex items-center">
                                    <div class="bg-purple-100 p-3 rounded-lg mr-3">
                                        <i class="fas fa-file-alt text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Exams Scheduled</p>
                                        <p id="stat-exams" class="font-bold text-gray-900">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions & Sync -->
                    <div class="portal-stack">
                        <div class="portal-card">
                            <h3 class="portal-section-title mb-3">Quick Actions</h3>
                            <div class="space-y-2">
                                <button onclick="openEventModal()" class="w-full portal-btn-primary py-2.5 flex items-center justify-center text-sm">
                                    <i class="fas fa-plus mr-2"></i> Add New Event
                                </button>
                                <button onclick="syncWithGoogle()" id="sync-all-btn" class="w-full bg-green-600 text-white px-4 py-2.5 rounded-lg hover:bg-green-700 transition flex items-center justify-center text-sm">
                                    <i class="fas fa-sync-alt mr-2"></i> Sync All to Google
                                </button>
                                <button onclick="window.print()" class="w-full bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-200 transition flex items-center justify-center text-sm">
                                    <i class="fas fa-print mr-2"></i> Print Schedule
                                </button>
                            </div>
                        </div>

                        <div class="portal-card">
                            <h3 class="portal-section-title mb-2">Sync Calendar</h3>
                            <p id="google-status-text" class="text-sm text-gray-600 mb-3">
                                <i class="fas fa-circle text-gray-400 mr-1"></i> Checking status...
                            </p>
                            <div class="space-y-2">
                                <button id="connect-google-btn" 
                                        class="w-full bg-gray-800 text-white px-4 py-2.5 rounded-lg hover:bg-gray-900 transition flex items-center justify-center text-sm">
                                    <i class="fab fa-google mr-2"></i> Connect Google Calendar
                                </button>
                                <button id="disconnect-google-btn" 
                                        class="w-full bg-red-50 text-red-600 px-4 py-2.5 rounded-lg hover:bg-red-100 transition flex items-center justify-center text-sm hidden">
                                    <i class="fas fa-unlink mr-2"></i> Disconnect Google Calendar
                                </button>
                            </div>
                        </div>

                        <div class="portal-card">
                            <h3 class="portal-section-title mb-3">Event Categories</h3>
                            <div class="space-y-2" id="event-categories">
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="event-dot event-lecture mr-3"></span>
                                        <span class="font-medium">Lectures</span>
                                    </div>
                                    <span id="cat-lecture" class="bg-white px-2 py-1 rounded text-sm">0</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="event-dot event-deadline mr-3"></span>
                                        <span class="font-medium">Deadlines</span>
                                    </div>
                                    <span id="cat-deadline" class="bg-white px-2 py-1 rounded text-sm">0</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="event-dot event-exam mr-3"></span>
                                        <span class="font-medium">Exams</span>
                                    </div>
                                    <span id="cat-exam" class="bg-white px-2 py-1 rounded text-sm">0</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="event-dot event-social mr-3"></span>
                                        <span class="font-medium">Social Events</span>
                                    </div>
                                    <span id="cat-social" class="bg-white px-2 py-1 rounded text-sm">0</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="event-dot event-workshop mr-3"></span>
                                        <span class="font-medium">Workshops</span>
                                    </div>
                                    <span id="cat-workshop" class="bg-white px-2 py-1 rounded text-sm">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        @include('layouts.partials.portal-content-close')
    </div>

    <!-- Event Modal -->
    <div id="event-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 id="modal-title" class="text-lg font-bold text-gray-900">Add New Event</h3>
                    <button onclick="closeEventModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="event-form">
                    <input type="hidden" id="event-id">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Title *</label>
                            <input type="text" id="event-title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent" placeholder="Enter event title">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                                <input type="date" id="event-start-date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                <input type="date" id="event-end-date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                                <input type="time" id="event-start-time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                                <input type="time" id="event-end-time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Type *</label>
                            <select id="event-type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent">
                                <option value="lecture">Lecture</option>
                                <option value="deadline">Deadline</option>
                                <option value="exam">Exam</option>
                                <option value="social">Social Event</option>
                                <option value="workshop">Workshop</option>
                                <option value="other">Other</option>
                                @if(Auth::user()->role === 'admin')
                                <option value="important">Important</option>
                                @endif
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input type="text" id="event-location" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent" placeholder="Enter location">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="event-description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent" placeholder="Enter event description"></textarea>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="checkbox" id="event-all-day" class="rounded border-gray-300 text-uthm-blue focus:ring-uthm-blue">
                                <span class="ml-2 text-sm text-gray-700">All Day Event</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" id="event-reminder" class="rounded border-gray-300 text-uthm-blue focus:ring-uthm-blue">
                                <span class="ml-2 text-sm text-gray-700">Set Reminder</span>
                            </label>
                            <label class="flex items-center" id="google-sync-option">
                                <input type="checkbox" id="event-sync-google" class="rounded border-gray-300 text-uthm-blue focus:ring-uthm-blue" checked>
                                <span class="ml-2 text-sm text-gray-700">Sync to Google</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeEventModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" id="event-submit-btn" class="px-4 py-2 bg-uthm-blue text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save mr-2"></i> Save Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Event Detail Modal -->
    <div id="event-detail-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 id="detail-title" class="text-lg font-bold text-gray-900"></h3>
                        <div class="flex space-x-2">
                        <button id="detail-edit-btn" onclick="editEventFromDetail()" class="text-blue-600 hover:text-blue-800" title="Edit event">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button id="detail-delete-btn" onclick="deleteEventFromDetail()" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                        <a id="detail-open-link" href="#" target="_blank" class="text-gray-700 hover:text-gray-900 hidden" title="Open full page">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <button onclick="closeEventDetailModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="space-y-3">
                    <p id="detail-date" class="text-gray-600"></p>
                    <p id="detail-time" class="text-gray-600"></p>
                    <p id="detail-location" class="text-gray-600"></p>
                    <p id="detail-type" class="text-gray-600"></p>
                    <p id="detail-description" class="text-gray-700 mt-4"></p>
                    <div id="detail-sync-status" class="mt-2"></div>
                </div>
                <div class="flex justify-end mt-6">
                    <button onclick="closeEventDetailModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Global variables
    const authUserId = {{ Auth::id() }};
    const isAdmin = {{ Auth::user()->role === 'admin' ? 'true' : 'false' }};
    let currentDate = new Date();
    let allEvents = [];
    let filteredEvents = [];
    let currentFilter = 'all';
    let currentDetailEvent = null;
    let googleConnected = false;
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        setupNavigation();
        setupFilters();
        checkGoogleStatus();
        loadEvents();
    });
    
    // Setup navigation
    function setupNavigation() {
        document.getElementById('prev-month').addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            loadEvents();
        });
        
        document.getElementById('next-month').addEventListener('click', function() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            loadEvents();
        });
        
        document.getElementById('today-btn').addEventListener('click', function() {
            currentDate = new Date();
            loadEvents();
        });
    }
    
    // Setup filters
    function setupFilters() {
        document.querySelectorAll('.event-filter').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.event-filter').forEach(btn => {
                    btn.classList.remove('active', 'bg-uthm-blue', 'text-white');
                    btn.classList.add('bg-gray-100', 'text-gray-700');
                });
                this.classList.add('active', 'bg-uthm-blue', 'text-white');
                this.classList.remove('bg-gray-100', 'text-gray-700');
                currentFilter = this.dataset.type;
                applyFilter();
            });
        });
    }
    
    // Apply filter
    function applyFilter() {
        if (currentFilter === 'all') {
            filteredEvents = [...allEvents];
        } else {
            filteredEvents = allEvents.filter(event => event.type === currentFilter);
        }
        renderCalendar();
        renderUpcomingEvents();
    }
    
    // Load events from API
    function loadEvents() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth() + 1;
        
        fetch(`/api/events?year=${year}&month=${month}`)
            .then(response => response.json())
            .then(events => {
                allEvents = events;
                applyFilter();
                updateStatistics();
                updateCategories();
            })
            .catch(error => {
                console.error('Error loading events:', error);
                allEvents = [];
                applyFilter();
            });
    }

    function formatEventDate(dateStr) {
        if (!dateStr) return '';
        return dateStr.includes('T') ? dateStr.split('T')[0] : String(dateStr).substring(0, 10);
    }

    function canManageEvent(event) {
        if (isAdmin) return true;
        if (event.visibility === 'public') return false;
        return event.user_id === authUserId;
    }

    function updateStatistics() {
        fetch('/api/events/statistics')
            .then(res => res.json())
            .then(data => {
                const lectures = document.getElementById('stat-lectures');
                const deadlines = document.getElementById('stat-deadlines');
                const exams = document.getElementById('stat-exams');
                if (lectures) lectures.textContent = data.lectures ?? 0;
                if (deadlines) deadlines.textContent = data.deadlines ?? 0;
                if (exams) exams.textContent = data.exams ?? 0;
            })
            .catch(err => console.error('Stats error:', err));
    }

    function updateCategories() {
        const categories = {};
        allEvents.forEach(event => {
            categories[event.type] = (categories[event.type] || 0) + 1;
        });
        ['lecture', 'deadline', 'exam', 'social', 'workshop'].forEach(type => {
            const el = document.getElementById(`cat-${type}`);
            if (el) el.textContent = categories[type] || 0;
        });
    }
    
    // Render calendar
    function renderCalendar() {
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];
        document.getElementById('current-month-year').textContent = 
            `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
        
        const calendarGrid = document.getElementById('calendar-grid');
        calendarGrid.innerHTML = '';
        
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const totalDays = lastDay.getDate();
        const startingDay = firstDay.getDay();
        
        const prevMonthLastDay = new Date(year, month, 0).getDate();
        for (let i = 0; i < startingDay; i++) {
            const day = prevMonthLastDay - startingDay + i + 1;
            const cell = createDayCell(day, 'other-month');
            calendarGrid.appendChild(cell);
        }
        
        const today = new Date();
        for (let day = 1; day <= totalDays; day++) {
            let classes = '';
            if (year === today.getFullYear() && month === today.getMonth() && day === today.getDate()) {
                classes = 'today';
            }
            
            const cell = createDayCell(day, classes);
            
            const dayEvents = filteredEvents.filter(event => {
                const eventDate = new Date(event.start_date);
                return eventDate.getFullYear() === year && 
                       eventDate.getMonth() === month && 
                       eventDate.getDate() === day;
            });
            
                    if (dayEvents.length > 0) {
                const eventsContainer = cell.querySelector('.day-events');
                dayEvents.slice(0, 3).forEach(event => {
                    const eventEl = document.createElement('div');
                    eventEl.className = `calendar-event ${getEventClass(event.type)}`;
                    eventEl.textContent = event.title;
                    eventEl.title = event.title;
                    // Navigate to full event page on click
                    eventEl.onclick = function(e) {
                        e.stopPropagation();
                        window.location.href = `/events/${event.id}`;
                    };

                    // Small open-in-new-tab icon
                    const openLink = document.createElement('a');
                    openLink.href = `/events/${event.id}`;
                    openLink.target = '_blank';
                    openLink.className = 'ml-2 inline-block text-white/80';
                    openLink.innerHTML = '<i class="fas fa-external-link-alt"></i>';
                    eventEl.appendChild(openLink);

                    eventsContainer.appendChild(eventEl);
                });
                
                if (dayEvents.length > 3) {
                    const moreEl = document.createElement('div');
                    moreEl.className = 'text-xs text-gray-500 mt-1';
                    moreEl.textContent = `+${dayEvents.length - 3} more`;
                    eventsContainer.appendChild(moreEl);
                }
            }
            
            calendarGrid.appendChild(cell);
        }
        
        const totalCells = Math.ceil((startingDay + totalDays) / 7) * 7;
        const remainingCells = totalCells - (startingDay + totalDays);
        for (let i = 1; i <= remainingCells; i++) {
            const cell = createDayCell(i, 'other-month');
            calendarGrid.appendChild(cell);
        }
    }
    
    function createDayCell(day, additionalClasses = '') {
        const cell = document.createElement('div');
        cell.className = `calendar-day ${additionalClasses}`;
        cell.innerHTML = `
            <div class="day-header">
                <div class="day-number">${day}</div>
                <button class="add-event-btn" onclick="event.stopPropagation(); openEventModal()">
                    <i class="fas fa-plus text-xs"></i>
                </button>
            </div>
            <div class="day-events"></div>
        `;
        return cell;
    }
    
    function getEventClass(type) {
        const classes = {
            lecture: 'bg-blue-500 text-white',
            deadline: 'bg-red-500 text-white',
            exam: 'bg-purple-500 text-white',
            social: 'bg-green-500 text-white',
            workshop: 'bg-yellow-400 text-black',
            important: 'bg-pink-500 text-white',
            other: 'bg-gray-500 text-white'
        };
        return classes[type] || 'bg-gray-500 text-white';
    }
    
    function renderUpcomingEvents() {
        const container = document.getElementById('upcoming-events');
        if (!container) return;

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const nextWeek = new Date(today);
        nextWeek.setDate(today.getDate() + 7);

        const upcoming = filteredEvents
            .filter(event => {
                const eventDate = new Date(event.start_date);
                eventDate.setHours(0, 0, 0, 0);
                return eventDate >= today && eventDate <= nextWeek;
            })
            .sort((a, b) => new Date(a.start_date) - new Date(b.start_date));

        if (upcoming.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-calendar-times text-3xl mb-2"></i>
                    <p>No upcoming events this week</p>
                </div>
            `;
            return;
        }

        container.innerHTML = '';
        upcoming.forEach(event => {
            const eventDate = new Date(event.start_date);
            const div = document.createElement('div');
            div.className = 'flex items-center p-4 bg-white border rounded-xl hover:shadow-md transition cursor-pointer';
            // Click the upcoming item to open the full event page
            div.onclick = () => window.location.href = `/events/${event.id}`;
            div.innerHTML = `
                <div class="mr-4 text-center min-w-[60px]">
                    <div class="font-bold text-xl text-gray-900">${eventDate.getDate()}</div>
                    <div class="text-xs uppercase text-gray-500">${eventDate.toLocaleDateString('en-US', { month: 'short' })}</div>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900">
                        ${event.title}${event.synced_with_google ? ' <i class="fab fa-google text-blue-500 text-xs ml-1" title="Synced with Google Calendar"></i>' : ''}
                    </h4>
                    <p class="text-sm text-gray-500 mt-1">
                        ${event.start_time ? `<i class="far fa-clock mr-1"></i>${event.start_time}` : 'All day'}
                        ${event.location ? ` • ${event.location}` : ''}
                    </p>
                    <span class="inline-block mt-2 px-2 py-1 text-xs rounded-full ${getEventClass(event.type)}">${event.type}</span>
                    <div class="mt-3">
                        <a href="/events/${event.id}" class="portal-btn-primary text-xs px-3 py-1.5">View</a>
                    </div>
                </div>
            `;
            container.appendChild(div);
        });
    }
    
    function checkGoogleStatus() {
        fetch('/google-calendar/status')
            .then(res => res.json())
            .then(data => {
                updateGoogleUI(data);
            })
            .catch(err => {
                console.error('Status check error:', err);
            });
    }
    
    function updateGoogleUI(status) {
        googleConnected = !!status.connected;
        const connectBtn = document.getElementById('connect-google-btn');
        const disconnectBtn = document.getElementById('disconnect-google-btn');
        const statusText = document.getElementById('google-status-text');
        const syncOption = document.getElementById('google-sync-option');
        const syncAllBtn = document.getElementById('sync-all-btn');
        const syncCheckbox = document.getElementById('event-sync-google');

        if (status.connected) {
            if (connectBtn) connectBtn.classList.add('hidden');
            if (disconnectBtn) disconnectBtn.classList.remove('hidden');
            if (syncAllBtn) syncAllBtn.classList.remove('hidden');
            if (statusText) statusText.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-1"></i> Connected to Google Calendar';
            if (syncOption) syncOption.classList.remove('hidden');
            if (syncCheckbox && !document.getElementById('event-id').value) syncCheckbox.checked = true;
        } else {
            if (connectBtn) connectBtn.classList.remove('hidden');
            if (disconnectBtn) disconnectBtn.classList.add('hidden');
            if (syncAllBtn) syncAllBtn.classList.add('hidden');
            if (statusText) statusText.innerHTML = '<i class="fas fa-times-circle text-red-500 mr-1"></i> Not Connected';
            if (syncOption) syncOption.classList.add('hidden');
        }
    }

    async function connectGoogle() {
        try {
            const response = await fetch('/google-calendar/connect');
            const data = await response.json();

            if (data.success && data.auth_url) {
                const width = 600, height = 600;
                const left = (screen.width - width) / 2;
                const top = (screen.height - height) / 2;
                const popup = window.open(data.auth_url, 'GoogleAuth',
                    `width=${width},height=${height},left=${left},top=${top}`);

                const checkPopup = setInterval(() => {
                    if (popup.closed) {
                        clearInterval(checkPopup);
                        showToast('Checking Google Calendar connection...', 'info');
                        setTimeout(() => {
                            checkGoogleStatus();
                            loadEvents();
                        }, 1500);
                    }
                }, 500);
            } else {
                showToast('Failed to connect: ' + (data.message || 'Unknown error'), 'error');
            }
        } catch (error) {
            console.error('Connection error:', error);
            showToast('Failed to connect to Google Calendar', 'error');
        }
    }

    async function disconnectGoogle() {
        if (!confirm('Disconnect Google Calendar? Your events will remain in the system.')) return;

        try {
            const response = await fetch('/google-calendar/disconnect', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                }
            });
            const data = await response.json();
            if (data.success) {
                showToast('Google Calendar disconnected', 'success');
                checkGoogleStatus();
                loadEvents();
            }
        } catch (error) {
            console.error('Disconnect error:', error);
            showToast('Failed to disconnect', 'error');
        }
    }
    
    function openEventModal() {
        document.getElementById('event-modal').classList.remove('hidden');
        document.getElementById('modal-title').textContent = 'Add New Event';
        document.getElementById('event-id').value = '';
        document.getElementById('event-form').reset();
        document.getElementById('event-start-date').value = new Date().toISOString().split('T')[0];
        const syncCheckbox = document.getElementById('event-sync-google');
        if (syncCheckbox) syncCheckbox.checked = googleConnected;
    }

    function closeEventModal() {
        document.getElementById('event-modal').classList.add('hidden');
    }

    function populateEventForm(event) {
        document.getElementById('modal-title').textContent = 'Edit Event';
        document.getElementById('event-id').value = event.id;
        document.getElementById('event-title').value = event.title;
        document.getElementById('event-start-date').value = formatEventDate(event.start_date);
        document.getElementById('event-end-date').value = formatEventDate(event.end_date);
        document.getElementById('event-start-time').value = event.start_time || '';
        document.getElementById('event-end-time').value = event.end_time || '';
        document.getElementById('event-type').value = event.type;
        document.getElementById('event-location').value = event.location || '';
        document.getElementById('event-description').value = event.description || '';
        document.getElementById('event-all-day').checked = event.all_day || false;
        document.getElementById('event-reminder').checked = event.set_reminder || false;
        const syncCheckbox = document.getElementById('event-sync-google');
        if (syncCheckbox) syncCheckbox.checked = event.synced_with_google || googleConnected;
    }

    function showEventDetail(event) {
        currentDetailEvent = event;
        document.getElementById('detail-title').textContent = event.title;
        document.getElementById('detail-date').innerHTML =
            `<i class="far fa-calendar mr-2"></i> ${formatEventDate(event.start_date)}` +
            (event.end_date && formatEventDate(event.end_date) !== formatEventDate(event.start_date)
                ? ' - ' + formatEventDate(event.end_date) : '');
        document.getElementById('detail-time').innerHTML = event.start_time
            ? `<i class="far fa-clock mr-2"></i> ${event.start_time}${event.end_time ? ' - ' + event.end_time : ''}`
            : '';
        document.getElementById('detail-location').innerHTML = event.location
            ? `<i class="fas fa-map-marker-alt mr-2"></i> ${event.location}` : '';
        document.getElementById('detail-type').innerHTML =
            `<span class="inline-block px-2 py-1 text-xs rounded-full ${getEventClass(event.type)}">${event.type}</span>` +
            (event.visibility === 'public' ? '<span class="ml-2 px-2 py-1 bg-blue-100 text-uthm-blue text-xs rounded">Public</span>' : '');
        document.getElementById('detail-description').textContent = event.description || 'No description';

        const syncStatus = document.getElementById('detail-sync-status');
        if (event.synced_with_google) {
            syncStatus.innerHTML = '<span class="text-sm text-green-600"><i class="fab fa-google mr-1"></i> Synced with Google Calendar</span>';
        } else {
            syncStatus.innerHTML = '<span class="text-sm text-gray-500"><i class="fas fa-cloud-upload-alt mr-1"></i> Not synced to Google</span>';
        }

        const canManage = canManageEvent(event);
        document.getElementById('detail-edit-btn').classList.toggle('hidden', !canManage);
        document.getElementById('detail-delete-btn').classList.toggle('hidden', !canManage);

        // Set link to full event page in the modal
        const openLink = document.getElementById('detail-open-link');
        if (openLink) {
            openLink.href = `/events/${event.id}`;
            openLink.classList.remove('hidden');
        }

        document.getElementById('event-detail-modal').classList.remove('hidden');
    }

    function closeEventDetailModal() {
        document.getElementById('event-detail-modal').classList.add('hidden');
        currentDetailEvent = null;
        const openLink = document.getElementById('detail-open-link');
        if (openLink) openLink.classList.add('hidden');
    }

    function editEventFromDetail() {
        if (!currentDetailEvent) return;
        if (!canManageEvent(currentDetailEvent)) {
            showToast('You can only edit your own events', 'error');
            return;
        }
        populateEventForm(currentDetailEvent);
        closeEventDetailModal();
        document.getElementById('event-modal').classList.remove('hidden');
    }

    function deleteEventFromDetail() {
        if (!currentDetailEvent) return;
        if (!canManageEvent(currentDetailEvent)) {
            showToast('You can only delete your own events', 'error');
            return;
        }
        if (!confirm('Are you sure you want to delete this event? It will also be removed from Google Calendar if synced.')) return;

        const eventId = currentDetailEvent.id;
        closeEventDetailModal();

        fetch(`/api/events/${eventId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                loadEvents();
            } else {
                showToast(data.message || 'Failed to delete event', 'error');
            }
        })
        .catch(err => {
            console.error('Delete error:', err);
            showToast('Failed to delete event', 'error');
        });
    }
    
    function saveEvent(e) {
        e.preventDefault();
        
        const eventId = document.getElementById('event-id').value;
        const isEdit = eventId !== '';
        
        const eventData = {
            title: document.getElementById('event-title').value,
            start_date: document.getElementById('event-start-date').value,
            end_date: document.getElementById('event-end-date').value || document.getElementById('event-start-date').value,
            start_time: document.getElementById('event-start-time').value || null,
            end_time: document.getElementById('event-end-time').value || null,
            type: document.getElementById('event-type').value,
            location: document.getElementById('event-location').value,
            description: document.getElementById('event-description').value,
            all_day: document.getElementById('event-all-day').checked,
            set_reminder: document.getElementById('event-reminder').checked,
            sync_to_google: googleConnected && (document.getElementById('event-sync-google')?.checked ?? false),
        };

        const url = isEdit ? `/api/events/${eventId}` : '/api/events';
        const method = isEdit ? 'PUT' : 'POST';
        const submitBtn = document.getElementById('event-submit-btn');
        const originalHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';

        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(eventData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                closeEventModal();
                loadEvents();
            } else {
                const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Error saving event');
                showToast(msg, 'error');
            }
        })
        .catch(err => {
            console.error('Save error:', err);
            showToast('Failed to save event', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        });
    }

    async function syncWithGoogle() {
        if (!googleConnected) {
            showToast('Connect Google Calendar first', 'error');
            return;
        }
        const btn = document.getElementById('sync-all-btn');
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Syncing...';

        try {
            const response = await fetch('/api/events/sync-all-google', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                }
            });
            const data = await response.json();
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) loadEvents();
        } catch (err) {
            console.error('Sync error:', err);
            showToast('Sync failed', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    }
    
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 24px;
            border-radius: 8px;
            color: white;
            z-index: 9999;
            background-color: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
    
    document.getElementById('event-form').addEventListener('submit', saveEvent);
    document.getElementById('connect-google-btn').addEventListener('click', connectGoogle);
    document.getElementById('disconnect-google-btn').addEventListener('click', disconnectGoogle);
    </script>
    @include('layouts.partials.portal-scripts')
</body>
</html>