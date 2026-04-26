<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Calendar - UTHM Bulletin Board System</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
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
            font-size: 1rem;
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
            opacity: 0.8;
            transform: translateX(2px);
        }
        
        .event-important { background-color: #f3e8ff; color: #5b21b6; border-left: 3px solid #8b5cf6; }
        .event-lecture { background-color: #dbeafe; color: #0c4a6e; border-left: 3px solid #0056a6; }
        .event-deadline { background-color: #fee2e2; color: #7f1d1d; border-left: 3px solid #dc3545; }
        .event-exam { background-color: #ede9fe; color: #4c1d95; border-left: 3px solid #6f42c1; }
        .event-social { background-color: #dcfce7; color: #166534; border-left: 3px solid #6ea342; }
        .event-workshop { background-color: #fef3c7; color: #78350f; border-left: 3px solid #ffc107; }
        
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
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-900 text-white">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 rounded-xl">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">Admin Panel</h2>
                        <p class="text-gray-400 text-sm">UTHM Bulletin System</p>
                    </div>
                </div>
                
                <nav class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800">
                        <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.calendar') }}" class="flex items-center p-3 rounded-lg bg-gray-800">
                        <i class="fas fa-calendar-alt mr-3"></i> Calendar
                    </a>
                </nav>
                
                <div class="mt-12 p-4 bg-gray-800 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">{{ $user->name }}</p>
                            <p class="text-sm text-gray-400">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <header class="bg-white shadow-sm border-b px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Calendar Management</h1>
                        <p class="text-gray-600 text-sm">Manage important dates visible to all users</p>
                    </div>
                    <button onclick="openEventModal()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                        <i class="fas fa-plus mr-2"></i> Post Important Date
                    </button>
                </div>
            </header>

            <main class="p-6">
                <!-- Calendar -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b">
                        <div class="flex justify-between items-center">
                            <h2 id="currentMonthYear" class="text-2xl font-bold text-gray-800">Loading...</h2>
                            <div class="flex space-x-2">
                                <button onclick="prevMonth()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">← Prev</button>
                                <button onclick="goToToday()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Today</button>
                                <button onclick="nextMonth()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Next →</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Calendar Grid -->
                    <div class="grid grid-cols-7 bg-gray-50 border-b">
                        <div class="p-4 text-center font-semibold">Sunday</div>
                        <div class="p-4 text-center font-semibold">Monday</div>
                        <div class="p-4 text-center font-semibold">Tuesday</div>
                        <div class="p-4 text-center font-semibold">Wednesday</div>
                        <div class="p-4 text-center font-semibold">Thursday</div>
                        <div class="p-4 text-center font-semibold">Friday</div>
                        <div class="p-4 text-center font-semibold">Saturday</div>
                    </div>
                    <div id="calendarGrid" class="calendar-grid"></div>
                </div>

                <!-- Upcoming Events -->
                <div class="mt-6 bg-white rounded-lg shadow">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-bold">Upcoming Important Dates</h3>
                    </div>
                    <div id="upcomingEvents" class="divide-y">
                        <div class="p-8 text-center text-gray-500">Loading...</div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Event Modal -->
    <div id="eventModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg w-full max-w-md mx-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold" id="modalTitle">Post Important Date</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="eventForm">
                <input type="hidden" id="eventId" name="event_id">
                <input type="hidden" name="visibility" value="public">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Title *</label>
                    <input type="text" id="title" name="title" required class="w-full border rounded-lg px-3 py-2">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Start Date *</label>
                    <input type="date" id="startDate" name="start_date" required class="w-full border rounded-lg px-3 py-2">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Type</label>
                    <select id="type" name="type" class="w-full border rounded-lg px-3 py-2">
                        <option value="important">Important Date</option>
                        <option value="lecture">Lecture</option>
                        <option value="deadline">Deadline</option>
                        <option value="exam">Exam</option>
                        <option value="social">Social Event</option>
                        <option value="workshop">Workshop</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Location</label>
                    <input type="text" id="location" name="location" class="w-full border rounded-lg px-3 py-2">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea id="description" name="description" rows="3" class="w-full border rounded-lg px-3 py-2"></textarea>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg w-full max-w-md mx-4 p-6">
            <h3 class="text-xl font-bold mb-4">Delete Event</h3>
            <p class="mb-6">Are you sure you want to delete this event? This will remove it from ALL users' calendars.</p>
            <div class="flex justify-end space-x-2">
                <button onclick="closeDeleteModal()" class="px-4 py-2 border rounded-lg">Cancel</button>
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

    <script>
        let currentDate = new Date();
        let allEvents = [];
        let deleteEventId = null;
        
        // Load everything on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadAllData();
            setupFormSubmit();
            
            // Set default date for modal
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('startDate').value = today;
        });
        
        // Load all data
        async function loadAllData() {
            await loadEvents();
            await loadUpcomingEvents();
        }
        
        // Load events from API
        async function loadEvents() {
            try {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth() + 1;
                
                console.log('Loading events for:', year, month);
                
                // Use the regular events endpoint and filter for public events
                const response = await fetch(`/api/events?year=${year}&month=${month}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('API Response for calendar:', data);
                    
                    // Filter to only show public events
                    if (Array.isArray(data)) {
                        allEvents = data.filter(event => event.visibility === 'public');
                    } else if (data.data && Array.isArray(data.data)) {
                        allEvents = data.data.filter(event => event.visibility === 'public');
                    } else {
                        allEvents = [];
                    }
                    
                    console.log('Public events for calendar:', allEvents.length);
                    renderCalendar();
                } else {
                    console.error('Failed to load events:', response.status);
                }
            } catch (error) {
                console.error('Error loading events:', error);
                showToast('Error loading events', true);
            }
        }
        
        // Render calendar grid
        function renderCalendar() {
            const calendarGrid = document.getElementById('calendarGrid');
            const monthYearDisplay = document.getElementById('currentMonthYear');
            
            if (!calendarGrid) return;
            
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                               'July', 'August', 'September', 'October', 'November', 'December'];
            monthYearDisplay.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
            
            calendarGrid.innerHTML = '';
            
            const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
            const totalDays = lastDay.getDate();
            const startingDay = firstDay.getDay();
            
            // Previous month days
            const prevMonthLastDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 0).getDate();
            for (let i = 0; i < startingDay; i++) {
                const day = prevMonthLastDay - startingDay + i + 1;
                calendarGrid.appendChild(createDayCell(day, 'other-month'));
            }
            
            // Current month days
            const today = new Date();
            for (let day = 1; day <= totalDays; day++) {
                const isToday = today.getDate() === day && 
                               today.getMonth() === currentDate.getMonth() && 
                               today.getFullYear() === currentDate.getFullYear();
                const cell = createDayCell(day, isToday ? 'today' : '');
                
                // Add events for this day
                const dayEvents = allEvents.filter(event => {
                    if (!event.start_date) return false;
                    const eventDate = new Date(event.start_date);
                    return eventDate.getDate() === day && 
                           eventDate.getMonth() === currentDate.getMonth() && 
                           eventDate.getFullYear() === currentDate.getFullYear();
                });
                
                if (dayEvents.length > 0) {
                    const eventsContainer = cell.querySelector('.events-container');
                    dayEvents.forEach(event => {
                        const eventEl = document.createElement('div');
                        eventEl.className = `event-item event-${event.type}`;
                        eventEl.textContent = event.title.length > 30 ? event.title.substring(0, 30) + '...' : event.title;
                        eventEl.onclick = () => showEventDetails(event);
                        eventsContainer.appendChild(eventEl);
                    });
                }
                
                calendarGrid.appendChild(cell);
            }
            
            // Next month days
            const totalCells = 42;
            const remainingCells = totalCells - (startingDay + totalDays);
            for (let i = 1; i <= remainingCells; i++) {
                calendarGrid.appendChild(createDayCell(i, 'other-month'));
            }
        }
        
        function createDayCell(dayNumber, additionalClasses = '') {
            const cell = document.createElement('div');
            cell.className = `calendar-day ${additionalClasses}`;
            cell.innerHTML = `
                <div class="calendar-day-number">${dayNumber}</div>
                <div class="events-container"></div>
            `;
            return cell;
        }
        
        // Load upcoming events - FIXED to properly refresh
        async function loadUpcomingEvents() {
            try {
                console.log('Loading upcoming events...');
                
                const response = await fetch('/api/events/upcoming', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const container = document.getElementById('upcomingEvents');
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('Upcoming events API response:', data);
                    
                    let eventsArray = [];
                    
                    if (Array.isArray(data)) {
                        eventsArray = data;
                    } else if (data.data && Array.isArray(data.data)) {
                        eventsArray = data.data;
                    }
                    
                    // Filter to only public events
                    const publicEvents = eventsArray.filter(e => e.visibility === 'public');
                    
                    console.log('Public upcoming events count:', publicEvents.length);
                    
                    if (publicEvents.length === 0) {
                        container.innerHTML = '<div class="p-8 text-center text-gray-500">No upcoming important dates</div>';
                        return;
                    }
                    
                    container.innerHTML = '';
                    publicEvents.forEach(event => {
                        const eventDate = new Date(event.start_date);
                        const div = document.createElement('div');
                        div.className = 'p-4 hover:bg-gray-50 cursor-pointer border-b';
                        div.onclick = () => showEventDetails(event);
                        div.innerHTML = `
                            <div class="flex justify-between items-center">
                                <div class="flex-1">
                                    <h4 class="font-semibold">${escapeHtml(event.title)}</h4>
                                    <p class="text-sm text-gray-600">${eventDate.toLocaleDateString()}</p>
                                    ${event.location ? `<p class="text-xs text-gray-500"><i class="fas fa-map-marker-alt mr-1"></i>${escapeHtml(event.location)}</p>` : ''}
                                    <span class="inline-block mt-1 text-xs text-purple-600"><i class="fas fa-globe mr-1"></i> Visible to all users</span>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="event.stopPropagation(); editEvent(${event.id})" class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="event.stopPropagation(); openDeleteModal(${event.id})" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                        container.appendChild(div);
                    });
                } else {
                    console.error('Failed to load upcoming events:', response.status);
                }
            } catch (error) {
                console.error('Error loading upcoming events:', error);
            }
        }
        
        // Save event (Create or Update)
        async function saveEvent(eventData, isEdit = false, eventId = null) {
            const url = isEdit ? `/api/events/${eventId}` : '/api/events';
            const method = isEdit ? 'PUT' : 'POST';
            
            console.log('Saving event:', { url, method, eventData });
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(eventData)
            });
            
            const result = await response.json();
            
            if (!response.ok) {
                throw new Error(result.message || result.error || 'Error saving event');
            }
            
            console.log('Save response:', result);
            return result;
        }
        
        // Delete event
        async function deleteEvent(eventId) {
            const response = await fetch(`/api/events/${eventId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Error deleting event');
            }
            
            return true;
        }
        
        function showEventDetails(event) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg max-w-md w-full mx-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center">
                    <h3 class="text-xl font-bold">${escapeHtml(event.title)}</h3>
                    <span class="ml-2 px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700">
                        <i class="fas fa-globe mr-1"></i> Public
                    </span>
                </div>
                <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="space-y-2">
                <p><i class="far fa-calendar mr-2"></i> ${new Date(event.start_date).toLocaleDateString()}</p>
                ${event.location ? `<p><i class="fas fa-map-marker-alt mr-2"></i> ${escapeHtml(event.location)}</p>` : ''}
                ${event.description ? `<p class="mt-4 pt-4 border-t">${escapeHtml(event.description)}</p>` : ''}
                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm text-gray-500">
                        <i class="fas fa-user-circle mr-2"></i> 
                        Created by: <span class="font-medium text-gray-700">${event.created_by?.name || 'Unknown'}</span>
                        <span class="ml-1 text-xs">(${event.created_by?.role || 'User'})</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Created on: ${new Date(event.created_at).toLocaleString()}</p>
                </div>
            </div>
            <div class="flex justify-end space-x-2 mt-6 pt-4 border-t">
                <button onclick="editEvent(${event.id}); this.closest('.fixed').remove()" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Edit</button>
                <button onclick="openDeleteModal(${event.id}); this.closest('.fixed').remove()" class="px-4 py-2 bg-red-500 text-white rounded-lg">Delete</button>
                <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 border rounded-lg">Close</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}
        
        // Open modal for new event
        function openEventModal() {
            document.getElementById('modalTitle').textContent = 'Post Important Date';
            document.getElementById('eventId').value = '';
            document.getElementById('title').value = '';
            document.getElementById('location').value = '';
            document.getElementById('description').value = '';
            document.getElementById('type').value = 'important';
            
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('startDate').value = today;
            
            document.getElementById('eventModal').classList.remove('hidden');
        }
        
        // Edit existing event
        function editEvent(eventId) {
            const event = allEvents.find(e => e.id === eventId);
            if (!event) {
                console.error('Event not found:', eventId);
                showToast('Event not found', true);
                return;
            }
            
            document.getElementById('modalTitle').textContent = 'Edit Important Date';
            document.getElementById('eventId').value = event.id;
            document.getElementById('title').value = event.title;
            document.getElementById('startDate').value = event.start_date;
            document.getElementById('type').value = event.type || 'important';
            document.getElementById('location').value = event.location || '';
            document.getElementById('description').value = event.description || '';
            
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
            
            const confirmBtn = document.querySelector('#deleteModal button:last-child');
            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<div class="loading"></div> Deleting...';
            }
            
            try {
                await deleteEvent(deleteEventId);
                closeDeleteModal();
                showToast('Event deleted successfully!');
                // Refresh all data after delete
                await loadAllData();
            } catch (error) {
                console.error('Delete error:', error);
                showToast(error.message, true);
            } finally {
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = 'Delete';
                }
            }
        }
        
        // Form submission - FIXED to refresh both calendar and upcoming events
        function setupFormSubmit() {
            const form = document.getElementById('eventForm');
            if (!form) return;
            
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const eventId = document.getElementById('eventId').value;
                const isEdit = eventId && eventId !== '';
                
                const formData = {
                    title: document.getElementById('title').value,
                    start_date: document.getElementById('startDate').value,
                    end_date: document.getElementById('startDate').value,
                    type: document.getElementById('type').value,
                    location: document.getElementById('location').value,
                    description: document.getElementById('description').value,
                    all_day: true,
                    visibility: 'public'  // Explicitly send public visibility
                };
                
                console.log('Submitting event data:', formData);
                
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<div class="loading"></div> Saving...';
                }
                
                try {
                    const result = await saveEvent(formData, isEdit, eventId);
                    console.log('Save result:', result);
                    closeModal();
                    
                    const successMessage = isEdit ? 'Event updated successfully! Visible to all users.' : 'Event posted to all users successfully!';
                    showToast(successMessage);
                    
                    // IMPORTANT: Refresh both calendar and upcoming events
                    await loadAllData();
                    
                } catch (error) {
                    console.error('Save error:', error);
                    showToast(error.message, true);
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Save';
                    }
                }
            });
        }
        
        // Navigation
        function prevMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            loadAllData();
        }
        
        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            loadAllData();
        }
        
        function goToToday() {
            currentDate = new Date();
            loadAllData();
        }
        
        // UI Helpers
        function showToast(message, isError = false) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            const toastDiv = toast.querySelector('div');
            
            toastMessage.textContent = message;
            if (isError) {
                toastDiv.classList.remove('bg-green-500');
                toastDiv.classList.add('bg-red-500');
                toastDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i><span id="toastMessage"></span>';
                document.getElementById('toastMessage').textContent = message;
            } else {
                toastDiv.classList.remove('bg-red-500');
                toastDiv.classList.add('bg-green-500');
                toastDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i><span id="toastMessage"></span>';
                document.getElementById('toastMessage').textContent = message;
            }
            
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
</body>
</html>