@extends('layouts.admin')

@section('title', 'Announcements Management')
@section('page_title', 'Announcements Management')
@section('page_subtitle', 'Manage, review and moderate announcements')

@section('content')
<div class="grid grid-cols-1">
        <!-- Filters and Create Button -->
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
                        <option value="pending_verification">Pending Verification</option>
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
                        <option value="events">Events</option>
                        <option value="important">Important</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button id="searchBtn" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Filter</button>
                    <button id="createBtn" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Create New</button>
                </div>
            </div>
        </div>

        <!-- Announcements Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800">All Announcements</h2>
                    <span class="text-sm text-gray-600">Total: <span id="announcementCount">0</span></span>
                </div>
            </div>
            <div id="announcementsTableContainer" class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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

<!-- Create/Edit Modal -->
<div id="announcementModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-lg font-bold text-gray-900">Create Announcement</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="announcementForm" class="space-y-4">
            <input type="hidden" id="announcementId">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input type="text" id="title" placeholder="Enter announcement title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select id="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Category</option>
                        <option value="general">General</option>
                        <option value="academic">Academic</option>
                        <option value="event">Event</option>
                        <option value="important">Important</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Status</option>
                        <option value="draft">Draft</option>
                        <option value="pending_verification">Pending Verification</option>
                        <option value="published">Published</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                <textarea id="content" placeholder="Enter announcement content" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Is Official</label>
                <input type="checkbox" id="isOfficial" class="w-4 h-4 text-blue-600">
                <span class="ml-2 text-sm text-gray-600">Mark as official announcement</span>
            </div>
            
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">View Announcement</h3>
            <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="space-y-4">
            <div>
                <h4 class="text-sm font-medium text-gray-500">Title</h4>
                <p id="viewTitle" class="text-lg font-bold text-gray-900 mt-1"></p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Category</h4>
                    <p id="viewCategory" class="mt-1"></p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Status</h4>
                    <p id="viewStatus" class="mt-1"></p>
                </div>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-500">Author</h4>
                <p id="viewAuthor" class="text-gray-600 mt-1"></p>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-500">Date</h4>
                <p id="viewDate" class="text-gray-600 mt-1"></p>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-500">Content</h4>
                <p id="viewContent" class="text-gray-600 mt-2 whitespace-pre-wrap"></p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Load announcements on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadAnnouncements();
        document.getElementById('searchBtn').addEventListener('click', loadAnnouncements);
        document.getElementById('createBtn').addEventListener('click', openCreateModal);
        document.getElementById('announcementForm').addEventListener('submit', handleFormSubmit);
    });

    function loadAnnouncements() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const category = document.getElementById('categoryFilter').value;

    fetch(`/admin/announcements/list?search=${search}&status=${status}&category=${category}`)
        .then(response => response.json())
        .then(data => {
            const announcementsList = document.getElementById('announcementsList');
            announcementsList.innerHTML = '';

            if (data.success && data.data.data && data.data.data.length > 0) {
                document.getElementById('announcementCount').textContent = data.data.data.length;
                
                data.data.data.forEach(announcement => {
                    // Make sure we have the author name
                    const authorName = (announcement.author && announcement.author.name) || 'Unknown';
                    
                    const statusColor = announcement.status === 'pending_verification' ? 'bg-yellow-100 text-yellow-800' :
                                       announcement.status === 'published' ? 'bg-green-100 text-green-800' :
                                       announcement.status === 'draft' ? 'bg-gray-100 text-gray-800' :
                                       announcement.status === 'rejected' ? 'bg-red-100 text-red-800' :
                                       'bg-gray-100 text-gray-800';
                    
                    const categoryColor = announcement.category === 'general' ? 'bg-blue-100 text-blue-800' :
                                         announcement.category === 'academic' ? 'bg-purple-100 text-purple-800' :
                                         announcement.category === 'events' ? 'bg-green-100 text-green-800' :
                                         announcement.category === 'important' ? 'bg-red-100 text-red-800' :
                                         'bg-gray-100 text-gray-800';

                    const row = `
                        <tr class="table-row-hover">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900 truncate">${announcement.title}</p>
                                <p class="text-sm text-gray-500 truncate">${announcement.content.substring(0, 50)}...</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${authorName}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${categoryColor}">${announcement.category}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusColor}">${announcement.status}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${new Date(announcement.created_at).toLocaleDateString()}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                <button onclick="viewAnnouncement(${announcement.id})" class="inline-flex items-center justify-center px-3 py-2 text-blue-600 hover:bg-blue-50 rounded-md font-medium transition" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="editAnnouncement(${announcement.id})" class="inline-flex items-center justify-center px-3 py-2 text-green-600 hover:bg-green-50 rounded-md font-medium transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ${announcement.status === 'pending_verification' ? `
                                    <button onclick="approveAnnouncement(${announcement.id})" class="inline-flex items-center justify-center px-3 py-2 text-purple-600 hover:bg-purple-50 rounded-md font-medium transition" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="rejectAnnouncement(${announcement.id})" class="inline-flex items-center justify-center px-3 py-2 text-orange-600 hover:bg-orange-50 rounded-md font-medium transition" title="Reject">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                ` : ''}
                                <button onclick="deleteAnnouncement(${announcement.id})" class="inline-flex items-center justify-center px-3 py-2 text-red-600 hover:bg-red-50 rounded-md font-medium transition" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    announcementsList.innerHTML += row;
                });
            } else {
                document.getElementById('announcementCount').textContent = 0;
                announcementsList.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500"><i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i><p>No announcements found</p></td></tr>';
            }
        })
        .catch(error => {
            console.error('Error loading announcements:', error);
            showNotification('Error loading announcements', 'error');
        });
}

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Create Announcement';
        document.getElementById('announcementId').value = '';
        document.getElementById('announcementForm').reset();
        document.getElementById('announcementModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('announcementModal').classList.add('hidden');
    }

    function closeViewModal() {
        document.getElementById('viewModal').classList.add('hidden');
    }

    function handleFormSubmit(e) {
        e.preventDefault();
        
        const id = document.getElementById('announcementId').value;
        const title = document.getElementById('title').value;
        const content = document.getElementById('content').value;
        const category = document.getElementById('category').value;
        const status = document.getElementById('status').value;
        const isOfficial = document.getElementById('isOfficial').checked;

        const method = id ? 'PATCH' : 'POST';
        const url = id ? `/admin/announcements/${id}` : '/admin/announcements';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                title: title,
                content: content,
                category: category,
                status: status,
                is_official: isOfficial
            })
        })
        .then(response => {
            const contentType = response.headers.get('content-type');
            
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Response status:', response.status);
                    console.error('Response text:', text);
                    throw new Error(`HTTP ${response.status}: ${text.substring(0, 100)}`);
                });
            }
            
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                return response.text().then(text => {
                    console.error('Non-JSON response:', text);
                    throw new Error('Server returned non-JSON response');
                });
            }
        })
        .then(data => {
            if (data.success) {
                closeModal();
                loadAnnouncements();
                showNotification(id ? 'Announcement updated successfully' : 'Announcement created successfully', 'success');
            } else {
                showNotification(data.message || 'Error saving announcement', 'error');
            }
        })
        .catch(error => {
            let errorMsg = error.message || 'Error saving announcement';
            console.error('Form submission error:', error);
            showNotification(errorMsg, 'error');
        });
    }

    function viewAnnouncement(id) {
        fetch(`/admin/announcements/data/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const announcement = data.data;
                    document.getElementById('viewTitle').textContent = announcement.title;
                    document.getElementById('viewCategory').innerHTML = `<span class="badge bg-blue-100 text-blue-800">${announcement.category}</span>`;
                    const statusClass = announcement.status === 'published' ? 'bg-green-100 text-green-800' :
                                       announcement.status === 'pending_verification' ? 'bg-yellow-100 text-yellow-800' :
                                       announcement.status === 'draft' ? 'bg-gray-100 text-gray-800' :
                                       announcement.status === 'rejected' ? 'bg-red-100 text-red-800' :
                                       'bg-gray-100 text-gray-800';
                    document.getElementById('viewStatus').innerHTML = `<span class="badge ${statusClass}">${announcement.status}</span>`;
                    document.getElementById('viewAuthor').textContent = (announcement.author && announcement.author.name) || 'Unknown';
                    document.getElementById('viewDate').textContent = new Date(announcement.created_at).toLocaleDateString();
                    document.getElementById('viewContent').textContent = announcement.content;
                    document.getElementById('viewModal').classList.remove('hidden');
                } else {
                    showNotification('Error loading announcement', 'error');
                }
            })
            .catch(() => showNotification('Error loading announcement', 'error'));
    }

    function editAnnouncement(id) {
        fetch(`/admin/announcements/data/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const announcement = data.data;
                    document.getElementById('modalTitle').textContent = 'Edit Announcement';
                    document.getElementById('announcementId').value = announcement.id;
                    document.getElementById('title').value = announcement.title;
                    document.getElementById('content').value = announcement.content;
                    document.getElementById('category').value = announcement.category;
                    document.getElementById('status').value = announcement.status;
                    document.getElementById('isOfficial').checked = announcement.is_official;
                    document.getElementById('announcementModal').classList.remove('hidden');
                } else {
                    showNotification('Error loading announcement', 'error');
                }
            })
            .catch(() => showNotification('Error loading announcement', 'error'));
    }

    function deleteAnnouncement(id) {
        if (confirm('Are you sure you want to delete this announcement?')) {
            fetch(`/admin/announcements/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadAnnouncements();
                    showNotification('Announcement deleted successfully', 'success');
                } else {
                    showNotification(data.message || 'Error deleting announcement', 'error');
                }
            })
            .catch(() => showNotification('Error deleting announcement', 'error'));
        }
    }

    function approveAnnouncement(id) {
        if (confirm('Are you sure you want to approve this announcement?')) {
            fetch(`/admin/announcements/${id}/approve`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadAnnouncements();
                    showNotification('Announcement approved successfully', 'success');
                } else {
                    showNotification(data.message || 'Error approving announcement', 'error');
                }
            })
            .catch(() => showNotification('Error approving announcement', 'error'));
        }
    }

    function rejectAnnouncement(id) {
        const reason = prompt('Enter rejection reason:');
        if (reason !== null && reason.trim() !== '') {
            fetch(`/admin/announcements/${id}/reject`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadAnnouncements();
                    showNotification('Announcement rejected successfully', 'success');
                } else {
                    showNotification(data.message || 'Error rejecting announcement', 'error');
                }
            })
            .catch(() => showNotification('Error rejecting announcement', 'error'));
        }
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${
            type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('announcementModal');
        const viewModal = document.getElementById('viewModal');
        
        if (event.target === modal) {
            closeModal();
        }
        if (event.target === viewModal) {
            closeViewModal();
        }
    });
</script>
@endsection
