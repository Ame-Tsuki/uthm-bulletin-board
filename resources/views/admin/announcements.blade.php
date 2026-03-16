@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-3xl font-bold text-gray-900">Announcements Management</h1>
            <p class="text-gray-600 mt-2">Manage, review and moderate announcements</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" id="searchInput" placeholder="Search announcements..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="published">Published</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select id="categoryFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        <option value="general">General</option>
                        <option value="academic">Academic</option>
                        <option value="event">Event</option>
                        <option value="important">Important</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button id="searchBtn" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Filter</button>
                </div>
            </div>
        </div>

        <!-- Announcements Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div id="announcementsTableContainer" class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="announcementsList">
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-500">Loading announcements...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', loadAnnouncements);

    function loadAnnouncements() {
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;
        const category = document.getElementById('categoryFilter').value;

        fetch(`/api/admin/announcements?search=${search}&status=${status}&category=${category}`)
            .then(response => response.json())
            .then(data => {
                const announcementsList = document.getElementById('announcementsList');
                announcementsList.innerHTML = '';

                if (data.success && data.data.data && data.data.data.length > 0) {
                    data.data.data.forEach(announcement => {
                        const statusColor = announcement.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                           announcement.status === 'published' ? 'bg-green-100 text-green-800' :
                                           'bg-red-100 text-red-800';
                        
                        const categoryColor = announcement.category === 'general' ? 'bg-blue-100 text-blue-800' :
                                             announcement.category === 'academic' ? 'bg-purple-100 text-purple-800' :
                                             announcement.category === 'event' ? 'bg-green-100 text-green-800' :
                                             'bg-red-100 text-red-800';

                        const row = `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900 truncate">${announcement.title}</p>
                                    <p class="text-sm text-gray-500 truncate">${announcement.content.substring(0, 50)}...</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${announcement.author_name || 'Unknown'}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${categoryColor}">${announcement.category}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusColor}">${announcement.status}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${new Date(announcement.created_at).toLocaleDateString()}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button onclick="viewAnnouncement(${announcement.id})" class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                                    ${announcement.status === 'pending' ? `
                                        <button onclick="approveAnnouncement(${announcement.id})" class="text-green-600 hover:text-green-900 mr-3">Approve</button>
                                        <button onclick="rejectAnnouncement(${announcement.id})" class="text-red-600 hover:text-red-900">Reject</button>
                                    ` : ''}
                                </td>
                            </tr>
                        `;
                        announcementsList.innerHTML += row;
                    });
                } else {
                    announcementsList.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500">No announcements found</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading announcements:', error);
                document.getElementById('announcementsList').innerHTML = '<tr><td colspan="6" class="text-center py-8 text-red-500">Error loading announcements</td></tr>';
            });
    }

    document.getElementById('searchBtn').addEventListener('click', loadAnnouncements);

    function viewAnnouncement(announcementId) {
        // Implementation for viewing announcement
        alert('View announcement ' + announcementId);
    }

    function approveAnnouncement(announcementId) {
        fetch(`/api/admin/announcements/${announcementId}/approve`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Announcement approved');
                loadAnnouncements();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving announcement');
        });
    }

    function rejectAnnouncement(announcementId) {
        const reason = prompt('Enter rejection reason:');
        if (reason !== null) {
            fetch(`/api/admin/announcements/${announcementId}/reject`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Announcement rejected');
                    loadAnnouncements();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error rejecting announcement');
            });
        }
    }
</script>
@endsection
