@extends('layouts.admin')

@section('title', 'Admin Calendar - UTHM Bulletin Board System')
@section('page_title', 'Calendar Management')
@section('page_subtitle', 'Manage important dates visible to all users')

@section('styles')
<style>
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        background-color: #e5e7eb;
        border: 1px solid #e5e7eb;
    }
    
    .calendar-day {
        background-color: white;
        min-height: 120px;
        padding: 8px;
        transition: all 0.2s ease;
    }
    
    .calendar-day:hover {
        background-color: #f9fafb;
    }
    
    .calendar-day.other-month {
        background-color: #f9fafb;
        color: #9ca3af;
    }
    
    .calendar-day.today {
        background-color: #dbeafe;
        border-top: 3px solid #0284c7;
    }
    
    .calendar-day-number {
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 0.95rem;
        display: inline-block;
        width: 28px;
        height: 28px;
        line-height: 28px;
        text-align: center;
        border-radius: 50%;
    }
    
    .calendar-day.today .calendar-day-number {
        background-color: #0284c7;
        color: white;
    }
    
    .event-item {
        font-size: 0.7rem;
        padding: 4px 8px;
        border-radius: 4px;
        margin-bottom: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .event-item:hover {
        opacity: 0.85;
        transform: translateX(2px);
    }
    
    .event-important { background-color: #f3e8ff; color: #5b21b6; border-left: 3px solid #8b5cf6; }
    .event-lecture { background-color: #dbeafe; color: #0c4a6e; border-left: 3px solid #3b82f6; }
    .event-deadline { background-color: #fee2e2; color: #7f1d1d; border-left: 3px solid #ef4444; }
    .event-exam { background-color: #ede9fe; color: #4c1d95; border-left: 3px solid #8b5cf6; }
    .event-social { background-color: #dcfce7; color: #166534; border-left: 3px solid #22c55e; }
    .event-workshop { background-color: #fef3c7; color: #78350f; border-left: 3px solid #f59e0b; }
    
    .week-view-grid {
        background: white;
        overflow-x: auto;
    }
    
    .week-day-cell {
        min-height: 400px;
        border-right: 1px solid #e5e7eb;
        padding: 8px;
    }
    
    .week-event, .day-event {
        background-color: #f3e8ff;
        border-left: 3px solid #8b5cf6;
        padding: 6px 8px;
        margin-bottom: 6px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.75rem;
    }
    
    .week-event:hover, .day-event:hover {
        transform: translateX(2px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .time-slot {
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.2s;
        cursor: pointer;
        min-height: 60px;
    }
    
    .time-slot:hover {
        background-color: #f9fafb;
    }
    
    .toast-notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #8b5cf6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .month-selector {
        position: relative;
    }
    
    .month-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 50;
        min-width: 200px;
        max-height: 300px;
        overflow-y: auto;
    }
    
    .month-option {
        padding: 0.5rem 1rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .month-option:hover {
        background-color: #f3f4f6;
    }
    
    .year-btn {
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        background: #f3f4f6;
        transition: all 0.2s;
        font-weight: 500;
    }
    
    .year-btn:hover {
        background: #e5e7eb;
    }
    
    .view-btn-active {
        background-color: #7c3aed;
        color: white;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    
    .view-btn-inactive {
        background-color: white;
        color: #374151;
        border: 1px solid #d1d5db;
    }
    
    .view-btn-inactive:hover {
        background-color: #f9fafb;
    }
</style>
@endsection

@section('content')
<!-- Calendar -->
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b bg-gradient-to-r from-gray-50 to-white">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <!-- Month/Year Navigation -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2 bg-gray-100 rounded-lg p-1">
                    <button onclick="changeYear(-1)" class="year-btn px-4 py-2 rounded-lg hover:bg-gray-200 transition flex items-center gap-2">
                        <i class="fas fa-chevron-double-left"></i>
                        <span class="hidden sm:inline">Prev Year</span>
                    </button>
                    <span id="currentYear" class="text-2xl font-bold text-gray-800 min-w-[100px] text-center"></span>
                    <button onclick="changeYear(1)" class="year-btn px-4 py-2 rounded-lg hover:bg-gray-200 transition flex items-center gap-2">
                        <span class="hidden sm:inline">Next Year</span>
                        <i class="fas fa-chevron-double-right"></i>
                    </button>
                </div>
                
                <div class="flex items-center space-x-2">
                    <button onclick="changeMonth(-1)" class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    
                    <div class="month-selector relative">
                        <button id="monthDropdownBtn" onclick="toggleMonthDropdown()" 
                                class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition flex items-center gap-2 min-w-[160px] justify-between bg-white">
                            <span id="currentMonthName" class="font-medium"></span>
                            <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                        </button>
                        <div id="monthDropdown" class="month-dropdown hidden">
                            <div class="py-1">
                                <div class="month-option" data-month="0">January</div>
                                <div class="month-option" data-month="1">February</div>
                                <div class="month-option" data-month="2">March</div>
                                <div class="month-option" data-month="3">April</div>
                                <div class="month-option" data-month="4">May</div>
                                <div class="month-option" data-month="5">June</div>
                                <div class="month-option" data-month="6">July</div>
                                <div class="month-option" data-month="7">August</div>
                                <div class="month-option" data-month="8">September</div>
                                <div class="month-option" data-month="9">October</div>
                                <div class="month-option" data-month="10">November</div>
                                <div class="month-option" data-month="11">December</div>
                            </div>
                        </div>
                    </div>
                    
                    <button onclick="changeMonth(1)" class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                
                <button onclick="goToToday()" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fas fa-calendar-day"></i>
                    <span class="hidden sm:inline">Today</span>
                </button>
            </div>
            
            <!-- Search Bar -->
            <div class="relative min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="calendar-search" placeholder="Search events..." class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm w-full">
            </div>
            
            <div class="flex gap-2 bg-gray-100 rounded-lg p-1">
                <button onclick="setView('month')" id="monthViewBtn" class="view-btn-active px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Month</span>
                </button>
                <button onclick="setView('week')" id="weekViewBtn" class="view-btn-inactive px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-calendar-week"></i>
                    <span>Week</span>
                </button>
                <button onclick="setView('day')" id="dayViewBtn" class="view-btn-inactive px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-calendar-day"></i>
                    <span>Day</span>
                </button>
                <a href="{{ route('events.list') }}" class="view-btn-inactive px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <i class="fas fa-list"></i>
                    <span>List All</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Month View -->
    <div id="monthView">
        <div class="grid grid-cols-7 bg-gray-50 border-b">
            <div class="p-4 text-center font-semibold text-gray-700">Sun</div>
            <div class="p-4 text-center font-semibold text-gray-700">Mon</div>
            <div class="p-4 text-center font-semibold text-gray-700">Tue</div>
            <div class="p-4 text-center font-semibold text-gray-700">Wed</div>
            <div class="p-4 text-center font-semibold text-gray-700">Thu</div>
            <div class="p-4 text-center font-semibold text-gray-700">Fri</div>
            <div class="p-4 text-center font-semibold text-gray-700">Sat</div>
        </div>
        <div id="calendarGrid" class="calendar-grid"></div>
    </div>
    
    <!-- Week View -->
    <div id="weekView" class="hidden">
        <div id="weekGrid" class="week-view-grid"></div>
    </div>
    
    <!-- Day View -->
    <div id="dayView" class="hidden">
        <div id="dayGrid" class="day-view-grid"></div>
    </div>
</div>

<!-- Upcoming Events -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow">
        <div class="p-6 border-b bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-calendar-alt mr-2 text-purple-600"></i>
                Upcoming Important Dates
            </h3>
        </div>
        <div id="upcomingEvents" class="divide-y max-h-[400px] overflow-y-auto">
            <div class="p-8 text-center text-gray-500">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mb-4"></div>
                <p>Loading events...</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                Quick Actions
            </h3>
            <div class="space-y-3">
                <button onclick="openEventModal()" class="w-full bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700 transition flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i> Post Important Date
                </button>
                <button onclick="syncWithGoogle()" id="sync-all-btn" class="w-full bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition flex items-center justify-center hidden">
                    <i class="fas fa-sync-alt mr-2"></i> Sync All to Google
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">
                <i class="fab fa-google mr-2 text-red-500"></i>
                Google Calendar
            </h3>
            <p id="google-status-text" class="text-sm text-gray-600 mb-4">
                <i class="fas fa-circle text-gray-400 mr-1"></i> Checking status...
            </p>
            <div class="space-y-3">
                <button id="connect-google-btn" 
                        class="w-full bg-gray-800 text-white px-4 py-3 rounded-lg hover:bg-gray-900 transition flex items-center justify-center">
                    <i class="fab fa-google mr-2"></i> Connect Google Calendar
                </button>
                <button id="disconnect-google-btn" 
                        class="w-full bg-red-50 text-red-600 px-4 py-3 rounded-lg hover:bg-red-100 transition flex items-center justify-center text-sm hidden">
                    <i class="fas fa-unlink mr-2"></i> Disconnect Google Calendar
                </button>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">
                <i class="fas fa-tag mr-2 text-gray-500"></i>
                Event Types
            </h3>
            <div class="space-y-2">
                <div class="flex items-center"><div class="w-3 h-3 rounded-full bg-purple-500 mr-2"></div><span class="text-sm">Important Date</span></div>
                <div class="flex items-center"><div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div><span class="text-sm">Lecture</span></div>
                <div class="flex items-center"><div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div><span class="text-sm">Deadline</span></div>
                <div class="flex items-center"><div class="w-3 h-3 rounded-full bg-indigo-500 mr-2"></div><span class="text-sm">Exam</span></div>
                <div class="flex items-center"><div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div><span class="text-sm">Social Event</span></div>
                <div class="flex items-center"><div class="w-3 h-3 rounded-full bg-orange-500 mr-2"></div><span class="text-sm">Workshop</span></div>
            </div>
        </div>
    </div>
</div>

<!-- Event Modal -->
<div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl w-full max-w-md mx-4 p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-5 pb-3 border-b">
            <h3 class="text-xl font-bold text-gray-800" id="modalTitle">Post Important Date</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="eventForm">
            <input type="hidden" id="eventId" name="event_id">
            <input type="hidden" name="visibility" value="public">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" id="title" name="title" required 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                <input type="date" id="startDate" name="start_date" required 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="type" name="type" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500">
                    <option value="important">Important Date</option>
                    <option value="lecture">Lecture</option>
                    <option value="deadline">Deadline</option>
                    <option value="exam">Exam</option>
                    <option value="social">Social Event</option>
                    <option value="workshop">Workshop</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <input type="text" id="location" name="location" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description" name="description" rows="3" 
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <div class="mb-4" id="google-sync-option">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" id="event-sync-google" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span class="ml-2 text-sm text-gray-700">Sync to Google Calendar</span>
                </label>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl w-full max-w-md mx-4 p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Delete Event</h3>
            <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <p class="text-gray-600 mb-6">Are you sure you want to delete this event? This will remove it from ALL users' calendars.</p>
        <div class="flex justify-end space-x-3">
            <button onclick="closeDeleteModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
            <button onclick="confirmDelete()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Delete</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="toast" class="toast-notification hidden">
    <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
        <i class="fas fa-check-circle mr-2"></i>
        <span id="toastMessage"></span>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    let googleConnected = false;
    let currentDate = new Date();
    let allEvents = [];
    let deleteEventId = null;
    let currentView = 'month';
    let currentSearchTerm = '';
    let upcomingEventsList = [];
    
    function getFilteredEvents() {
        if (!currentSearchTerm) {
            return allEvents;
        }
        return allEvents.filter(event => {
            const title = event.title ? event.title.toLowerCase() : '';
            const desc = event.description ? event.description.toLowerCase() : '';
            const loc = event.location ? event.location.toLowerCase() : '';
            return title.includes(currentSearchTerm) || 
                   desc.includes(currentSearchTerm) || 
                   loc.includes(currentSearchTerm);
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        loadAllData();
        setupFormSubmit();
        checkGoogleStatus();
        updateYearDisplay();
        updateMonthDisplay();
        
        const searchInput = document.getElementById('calendar-search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                currentSearchTerm = this.value.toLowerCase();
                if (currentView === 'month') {
                    renderCalendar();
                } else if (currentView === 'week') {
                    renderWeekView();
                } else if (currentView === 'day') {
                    renderDayView();
                }
                renderUpcomingEventsList();
            });
        }
        
        document.getElementById('connect-google-btn')?.addEventListener('click', connectGoogle);
        document.getElementById('disconnect-google-btn')?.addEventListener('click', disconnectGoogle);
        
        const today = new Date().toISOString().split('T')[0];
        const startDateInput = document.getElementById('startDate');
        if (startDateInput) startDateInput.value = today;
        
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('monthDropdown');
            const btn = document.getElementById('monthDropdownBtn');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                if (!btn?.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            }
        });
        
        document.querySelectorAll('.month-option').forEach(option => {
            option.addEventListener('click', function() {
                const month = parseInt(this.dataset.month);
                currentDate.setMonth(month);
                updateMonthDisplay();
                updateYearDisplay();
                loadAllData();
                document.getElementById('monthDropdown')?.classList.add('hidden');
            });
        });
    });
    
    // Helper functions (keep all your existing helper functions here)
    function updateYearDisplay() {
        const yearSpan = document.getElementById('currentYear');
        if (yearSpan) yearSpan.textContent = currentDate.getFullYear();
    }
    
    function updateMonthDisplay() {
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        const monthSpan = document.getElementById('currentMonthName');
        if (monthSpan) monthSpan.textContent = monthNames[currentDate.getMonth()];
    }
    
    function toggleMonthDropdown() {
        const dropdown = document.getElementById('monthDropdown');
        if (dropdown) dropdown.classList.toggle('hidden');
    }
    
    function changeMonth(delta) {
        currentDate.setMonth(currentDate.getMonth() + delta);
        updateMonthDisplay();
        updateYearDisplay();
        loadAllData();
        const dropdown = document.getElementById('monthDropdown');
        if (dropdown) dropdown.classList.add('hidden');
    }
    
    function changeYear(delta) {
        currentDate.setFullYear(currentDate.getFullYear() + delta);
        updateYearDisplay();
        updateMonthDisplay();
        loadAllData();
    }
    
    function setView(view) {
        currentView = view;
        
        const monthBtn = document.getElementById('monthViewBtn');
        const weekBtn = document.getElementById('weekViewBtn');
        const dayBtn = document.getElementById('dayViewBtn');
        const monthView = document.getElementById('monthView');
        const weekView = document.getElementById('weekView');
        const dayView = document.getElementById('dayView');
        
        const activeClass = 'view-btn-active px-4 py-2 rounded-lg transition flex items-center gap-2';
        const inactiveClass = 'view-btn-inactive px-4 py-2 rounded-lg transition flex items-center gap-2';
        
        if (monthBtn) monthBtn.className = inactiveClass;
        if (weekBtn) weekBtn.className = inactiveClass;
        if (dayBtn) dayBtn.className = inactiveClass;
        
        if (monthView) monthView.classList.add('hidden');
        if (weekView) weekView.classList.add('hidden');
        if (dayView) dayView.classList.add('hidden');
        
        if (view === 'month') {
            if (monthBtn) monthBtn.className = activeClass;
            if (monthView) monthView.classList.remove('hidden');
            loadAllData();
        } else if (view === 'week') {
            if (weekBtn) weekBtn.className = activeClass;
            if (weekView) weekView.classList.remove('hidden');
            renderWeekView();
        } else if (view === 'day') {
            if (dayBtn) dayBtn.className = activeClass;
            if (dayView) dayView.classList.remove('hidden');
            renderDayView();
        }
    }

    function formatEventDate(dateStr) {
        if (!dateStr) return '';
        return dateStr.includes('T') ? dateStr.split('T')[0] : String(dateStr).substring(0, 10);
    }
    
    async function checkGoogleStatus() {
        try {
            const response = await fetch('/google-calendar/status');
            const data = await response.json();
            updateGoogleUI(data);
        } catch (error) {
            console.error('Status check error:', error);
        }
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
            if (syncCheckbox && !document.getElementById('eventId').value) syncCheckbox.checked = true;
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
                window.open(data.auth_url, 'GoogleAuth', `width=${width},height=${height},left=${left},top=${top}`);
                setTimeout(() => checkGoogleStatus(), 2000);
            } else {
                showToast(data.message || 'Failed to connect', true);
            }
        } catch (error) {
            showToast('Failed to connect to Google Calendar', true);
        }
    }
    
    async function disconnectGoogle() {
        if (!confirm('Disconnect Google Calendar? Your events will remain in the system.')) return;
        try {
            const response = await fetch('/google-calendar/disconnect', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            });
            const data = await response.json();
            if (data.success) {
                showToast('Google Calendar disconnected');
                checkGoogleStatus();
                loadAllData();
            }
        } catch (error) {
            showToast('Failed to disconnect', true);
        }
    }
    
    async function syncWithGoogle() {
        if (!googleConnected) {
            showToast('Connect Google Calendar first', true);
            return;
        }
        const btn = document.getElementById('sync-all-btn');
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Syncing...';
        try {
            const response = await fetch('/api/events/sync-all-google', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' },
            });
            const data = await response.json();
            showToast(data.message, !data.success);
            if (data.success) loadAllData();
        } catch (error) {
            showToast('Sync failed', true);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    }
    
    async function loadAllData() {
        await loadEvents();
        await loadUpcomingEvents();
    }
    
    async function loadEvents() {
        try {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth() + 1;
            
            const response = await fetch(`/api/events?year=${year}&month=${month}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            
            if (response.ok) {
                const data = await response.json();
                allEvents = (Array.isArray(data) ? data : (data.data || [])).filter(event => event.visibility === 'public');
                
                if (currentView === 'month') {
                    renderCalendar();
                } else if (currentView === 'week') {
                    renderWeekView();
                } else if (currentView === 'day') {
                    renderDayView();
                }
            }
        } catch (error) {
            console.error('Error loading events:', error);
            showToast('Error loading events', true);
        }
    }
    
    function renderCalendar() {
        const calendarGrid = document.getElementById('calendarGrid');
        if (!calendarGrid) return;
        
        calendarGrid.innerHTML = '';
        
        const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
        const totalDays = lastDay.getDate();
        const startingDay = firstDay.getDay();
        
        const prevMonthLastDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 0).getDate();
        for (let i = 0; i < startingDay; i++) {
            const day = prevMonthLastDay - startingDay + i + 1;
            calendarGrid.appendChild(createDayCell(day, 'other-month'));
        }
        
        const today = new Date();
        for (let day = 1; day <= totalDays; day++) {
            const isToday = today.getDate() === day && today.getMonth() === currentDate.getMonth() && today.getFullYear() === currentDate.getFullYear();
            const cell = createDayCell(day, isToday ? 'today' : '');
            
            const dayEvents = getFilteredEvents().filter(event => {
                if (!event.start_date) return false;
                const eventDate = new Date(event.start_date);
                return eventDate.getDate() === day && eventDate.getMonth() === currentDate.getMonth() && eventDate.getFullYear() === currentDate.getFullYear();
            });
            
            if (dayEvents.length > 0) {
                const eventsContainer = cell.querySelector('.events-container');
                dayEvents.slice(0, 3).forEach(event => {
                    const eventEl = document.createElement('div');
                    eventEl.className = `event-item event-${event.type || 'important'}`;
                    eventEl.innerHTML = escapeHtml(event.title.length > 25 ? event.title.substring(0, 25) + '...' : event.title);
                    eventEl.onclick = (e) => { e.stopPropagation(); showEventDetails(event); };
                    eventsContainer.appendChild(eventEl);
                });
            }
            
            calendarGrid.appendChild(cell);
        }
        
        const totalCells = 42;
        const remainingCells = totalCells - (startingDay + totalDays);
        for (let i = 1; i <= remainingCells; i++) {
            calendarGrid.appendChild(createDayCell(i, 'other-month'));
        }
    }
    
    function renderWeekView() {
        const weekContainer = document.getElementById('weekGrid');
        if (!weekContainer) return;
        
        const startOfWeek = new Date(currentDate);
        startOfWeek.setDate(currentDate.getDate() - currentDate.getDay());
        
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const shortDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        
        let html = `<div class="grid grid-cols-7 border-b bg-gray-50">`;
        
        for (let i = 0; i < 7; i++) {
            const date = new Date(startOfWeek);
            date.setDate(startOfWeek.getDate() + i);
            const isToday = new Date().toDateString() === date.toDateString();
            html += `<div class="p-4 text-center ${isToday ? 'bg-blue-50' : ''}">
                        <div class="font-semibold">${shortDays[i]}</div>
                        <div class="text-sm ${isToday ? 'text-blue-600 font-bold' : 'text-gray-600'}">${date.getDate()}</div>
                    </div>`;
        }
        html += `</div><div class="grid grid-cols-7 min-h-[500px]">`;
        
        for (let i = 0; i < 7; i++) {
            const currentDay = new Date(startOfWeek);
            currentDay.setDate(startOfWeek.getDate() + i);
            const dateStr = currentDay.toISOString().split('T')[0];
            
            const dayEvents = getFilteredEvents().filter(event => {
                if (!event.start_date) return false;
                return new Date(event.start_date).toISOString().split('T')[0] === dateStr;
            });
            
            html += `<div class="p-2 border-r min-h-[500px]">`;
            if (dayEvents.length > 0) {
                dayEvents.forEach(event => {
                    html += `<div class="week-event event-${event.type || 'important'} mb-2 p-2 rounded cursor-pointer" onclick="showEventDetails(${event.id})">
                                <div class="font-medium text-xs">${escapeHtml(event.title)}</div>
                            </div>`;
                });
            } else {
                html += `<div class="text-center text-gray-400 text-sm mt-4">No events</div>`;
            }
            html += `</div>`;
        }
        
        html += `</div>`;
        weekContainer.innerHTML = html;
    }
    
    function renderDayView() {
        const dayContainer = document.getElementById('dayGrid');
        if (!dayContainer) return;
        
        const hours = ['12:00 AM', '1:00 AM', '2:00 AM', '3:00 AM', '4:00 AM', '5:00 AM', 
                       '6:00 AM', '7:00 AM', '8:00 AM', '9:00 AM', '10:00 AM', '11:00 AM',
                       '12:00 PM', '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM',
                       '6:00 PM', '7:00 PM', '8:00 PM', '9:00 PM', '10:00 PM', '11:00 PM'];
        
        const dateStr = currentDate.toISOString().split('T')[0];
        const dayEvents = getFilteredEvents().filter(event => {
            if (!event.start_date) return false;
            return new Date(event.start_date).toISOString().split('T')[0] === dateStr;
        });
        
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        let html = `<div class="p-4 border-b bg-gray-50">
                        <h2 class="text-xl font-bold text-gray-800">
                            ${dayNames[currentDate.getDay()]}, ${monthNames[currentDate.getMonth()]} ${currentDate.getDate()}, ${currentDate.getFullYear()}
                        </h2>
                    </div><div class="divide-y">`;
        
        hours.forEach(hour => {
            html += `<div class="time-slot flex hover:bg-gray-50 transition cursor-pointer" onclick="openEventModalWithDate('${dateStr}')">
                        <div class="w-24 p-3 text-sm font-medium text-gray-600 border-r">${hour}</div>
                        <div class="flex-1 p-2 min-h-[60px]">`;
            
            const hourEvents = dayEvents.filter(event => {
                const eventHour = new Date(event.start_date).getHours();
                return eventHour === parseInt(hour) || (hour === '12:00 AM' && eventHour === 0);
            });
            
            hourEvents.forEach(event => {
                html += `<div class="day-event event-${event.type || 'important'} mb-2 p-2 rounded cursor-pointer" onclick="event.stopPropagation(); showEventDetails(${event.id})">
                            <div class="font-medium">${escapeHtml(event.title)}</div>
                        </div>`;
            });
            
            html += `</div></div>`;
        });
        
        html += `</div>`;
        dayContainer.innerHTML = html;
    }
    
    function openEventModalWithDate(date) {
        openEventModal();
        const startDateInput = document.getElementById('startDate');
        if (startDateInput) startDateInput.value = date;
    }
    
    function createDayCell(dayNumber, additionalClasses = '') {
        const cell = document.createElement('div');
        cell.className = `calendar-day ${additionalClasses}`;
        cell.innerHTML = `<div class="calendar-day-number">${dayNumber}</div><div class="events-container"></div>`;
        return cell;
    }
    
    async function loadUpcomingEvents() {
        try {
            const response = await fetch('/api/events/upcoming', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            
            const container = document.getElementById('upcomingEvents');
            if (!response.ok) {
                container.innerHTML = '<div class="p-8 text-center text-red-500">Error loading events</div>';
                return;
            }
            
            const data = await response.json();
            upcomingEventsList = Array.isArray(data) ? data : (data.data || []);
            renderUpcomingEventsList();
        } catch (error) {
            console.error('Error loading upcoming events:', error);
            const container = document.getElementById('upcomingEvents');
            if (container) container.innerHTML = '<div class="p-8 text-center text-red-500">Error loading events</div>';
        }
    }

    function renderUpcomingEventsList() {
        const container = document.getElementById('upcomingEvents');
        if (!container) return;
        
        const publicEvents = upcomingEventsList.filter(e => e.visibility === 'public');
        
        const filtered = currentSearchTerm ? publicEvents.filter(event => {
            const title = event.title ? event.title.toLowerCase() : '';
            const desc = event.description ? event.description.toLowerCase() : '';
            const loc = event.location ? event.location.toLowerCase() : '';
            return title.includes(currentSearchTerm) || 
                   desc.includes(currentSearchTerm) || 
                   loc.includes(currentSearchTerm);
        }) : publicEvents;
        
        if (filtered.length === 0) {
            container.innerHTML = '<div class="p-8 text-center text-gray-500"><i class="fas fa-calendar-alt text-3xl mb-2 text-gray-300"></i><p>No upcoming important dates</p></div>';
            return;
        }
        
        container.innerHTML = '';
        filtered.slice(0, 10).forEach(event => {
            const eventDate = new Date(event.start_date);
            const div = document.createElement('div');
            div.className = 'p-4 hover:bg-gray-50 cursor-pointer transition border-b';
            div.onclick = () => showEventDetails(event);
            div.innerHTML = `
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800">${escapeHtml(event.title)}</h4>
                        <p class="text-sm text-gray-600 mt-1">${eventDate.toLocaleDateString()}</p>
                        <span class="inline-block mt-2 text-xs text-purple-600"><i class="fas fa-globe mr-1"></i> Public</span>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="event.stopPropagation(); editEvent(${event.id})" class="text-blue-500 hover:text-blue-700 p-1"><i class="fas fa-edit"></i></button>
                        <button onclick="event.stopPropagation(); openDeleteModal(${event.id})" class="text-red-500 hover:text-red-700 p-1"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;
            container.appendChild(div);
        });
    }
    
    async function saveEvent(eventData, isEdit = false, eventId = null) {
        const url = isEdit ? `/api/events/${eventId}` : '/api/events';
        const response = await fetch(url, {
            method: isEdit ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(eventData)
        });
        
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Error saving event');
        return result;
    }
    
    async function deleteEvent(eventId) {
        const response = await fetch(`/api/events/${eventId}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        });
        
        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Error deleting event');
        }
        return true;
    }
    
    function showEventDetails(event) {
        const eventDate = new Date(event.start_date);
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-xl max-w-md w-full mx-4 p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">${escapeHtml(event.title)}</h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
                </div>
                <div class="space-y-3">
                    <p class="text-gray-600"><i class="far fa-calendar mr-2"></i>${eventDate.toLocaleDateString()}</p>
                    ${event.location ? `<p class="text-gray-600"><i class="fas fa-map-marker-alt mr-2"></i>${escapeHtml(event.location)}</p>` : ''}
                    ${event.description ? `<p class="text-gray-600">${escapeHtml(event.description)}</p>` : ''}
                </div>
                <div class="flex justify-end space-x-2 mt-6 pt-4 border-t">
                    <button onclick="editEvent(${event.id}); this.closest('.fixed').remove()" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Edit</button>
                    <button onclick="openDeleteModal(${event.id}); this.closest('.fixed').remove()" class="px-4 py-2 bg-red-600 text-white rounded-lg">Delete</button>
                    <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 border rounded-lg">Close</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    function openEventModal() {
        document.getElementById('modalTitle').textContent = 'Post Important Date';
        document.getElementById('eventId').value = '';
        document.getElementById('eventForm').reset();
        document.getElementById('startDate').value = new Date().toISOString().split('T')[0];
        document.getElementById('type').value = 'important';
        const syncCheckbox = document.getElementById('event-sync-google');
        if (syncCheckbox) syncCheckbox.checked = googleConnected;
        document.getElementById('eventModal').classList.remove('hidden');
    }
    
    function editEvent(eventId) {
        const event = allEvents.find(e => e.id === eventId);
        if (!event) return;
        
        document.getElementById('modalTitle').textContent = 'Edit Important Date';
        document.getElementById('eventId').value = event.id;
        document.getElementById('title').value = event.title;
        document.getElementById('startDate').value = formatEventDate(event.start_date);
        document.getElementById('type').value = event.type || 'important';
        document.getElementById('location').value = event.location || '';
        document.getElementById('description').value = event.description || '';
        const syncCheckbox = document.getElementById('event-sync-google');
        if (syncCheckbox) syncCheckbox.checked = event.synced_with_google || googleConnected;
        document.getElementById('eventModal').classList.remove('hidden');
    }
    
    function closeModal() {
        document.getElementById('eventModal').classList.add('hidden');
    }
    
    function openDeleteModal(eventId) {
        deleteEventId = eventId;
        document.getElementById('deleteModal').classList.remove('hidden');
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteEventId = null;
    }
    
    async function confirmDelete() {
        if (!deleteEventId) return;
        try {
            await deleteEvent(deleteEventId);
            closeDeleteModal();
            showToast('Event deleted successfully!');
            await loadAllData();
        } catch (error) {
            showToast(error.message, true);
        }
    }
    
    function setupFormSubmit() {
        document.getElementById('eventForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const eventId = document.getElementById('eventId')?.value;
            const isEdit = eventId && eventId !== '';
            
            const formData = {
                title: document.getElementById('title')?.value,
                start_date: document.getElementById('startDate')?.value,
                end_date: document.getElementById('startDate')?.value,
                type: document.getElementById('type')?.value,
                location: document.getElementById('location')?.value,
                description: document.getElementById('description')?.value,
                all_day: true,
                visibility: 'public',
                sync_to_google: googleConnected && (document.getElementById('event-sync-google')?.checked ?? false)
            };
            
            const submitBtn = e.target.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<div class="loading"></div> Saving...';
            }
            
            try {
                const result = await saveEvent(formData, isEdit, eventId);
                closeModal();
                showToast(result.message || (isEdit ? 'Event updated!' : 'Event created!'));
                await loadAllData();
            } catch (error) {
                showToast(error.message, true);
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Save';
                }
            }
        });
    }
    
    function goToToday() {
        currentDate = new Date();
        updateYearDisplay();
        updateMonthDisplay();
        if (currentView === 'month') {
            loadAllData();
        } else if (currentView === 'week') {
            renderWeekView();
        } else if (currentView === 'day') {
            renderDayView();
        }
        setView('month');
    }
    
    function showToast(message, isError = false) {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        const toastDiv = toast?.querySelector('div');
        
        if (!toast || !toastMessage || !toastDiv) return;
        
        toastMessage.textContent = message;
        toastDiv.className = isError ? 'bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg' : 'bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg';
        toastDiv.innerHTML = `<i class="fas ${isError ? 'fa-exclamation-circle' : 'fa-check-circle'} mr-2"></i><span id="toastMessage">${escapeHtml(message)}</span>`;
        
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 3000);
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
@endsection