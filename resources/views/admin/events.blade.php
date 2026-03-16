@extends('layouts.admin')

@section('title', 'Events Management')
@section('page_title', 'Events Management')
@section('page_subtitle', 'Manage and monitor all system events')

@section('content')
<div class="grid grid-cols-1">
        <!-- Filters and Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" id="searchInput" placeholder="Search events..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Events</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button id="searchBtn" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Search</button>
                </div>
            </div>
            <button id="createEventBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Create Event</button>
        </div>

        <!-- Events Grid -->
        <div id="eventsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500">Loading events...</p>
            </div>
        </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', loadEvents);

    function loadEvents() {
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;

        fetch(`/api/admin/events?search=${search}&status=${status}`)
            .then(response => response.json())
            .then(data => {
                const eventsContainer = document.getElementById('eventsContainer');
                eventsContainer.innerHTML = '';

                if (data.success && data.data.data && data.data.data.length > 0) {
                    data.data.data.forEach(event => {
                        const eventDate = new Date(event.start_date);
                        const isUpcoming = eventDate > new Date();
                        
                        const card = `
                            <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow">
                                ${event.image_url ? `<img src="${event.image_url}" alt="${event.title}" class="w-full h-48 object-cover">` : '<div class="w-full h-48 bg-gradient-to-br from-blue-500 to-purple-600"></div>'}
                                <div class="p-6">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">${event.title}</h3>
                                    <p class="text-sm text-gray-600 mb-4">${event.description.substring(0, 100)}...</p>
                                    
                                    <div class="mb-4">
                                        <div class="flex items-center text-sm text-gray-600 mb-2">
                                            <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                                            ${eventDate.toLocaleDateString()}
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600 mb-2">
                                            <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                                            ${event.location || 'TBD'}
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-users mr-2 text-blue-600"></i>
                                            ${event.attendees_count || 0} attendees
                                        </div>
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <button onclick="editEvent(${event.id})" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">Edit</button>
                                        <button onclick="deleteEvent(${event.id})" class="flex-1 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm">Delete</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        eventsContainer.innerHTML += card;
                    });
                } else {
                    eventsContainer.innerHTML = '<div class="col-span-full text-center py-12"><p class="text-gray-500">No events found</p></div>';
                }
            })
            .catch(error => {
                console.error('Error loading events:', error);
                document.getElementById('eventsContainer').innerHTML = '<div class="col-span-full text-center py-12"><p class="text-red-500">Error loading events</p></div>';
            });
    }

    document.getElementById('searchBtn').addEventListener('click', loadEvents);
    
    document.getElementById('createEventBtn').addEventListener('click', function() {
        alert('Create event functionality would open a form');
    });

    function editEvent(eventId) {
        alert('Edit event ' + eventId);
    }

    function deleteEvent(eventId) {
        if (confirm('Are you sure you want to delete this event?')) {
            fetch(`/api/admin/events/${eventId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Event deleted successfully');
                    loadEvents();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting event');
            });
        }
    }
</script>
@endsection
