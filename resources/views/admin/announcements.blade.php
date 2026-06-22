@extends('layouts.admin')

@section('title', 'Admin Announcements - UTHM Bulletin Board System')
@section('page_title', 'Announcements Management')
@section('page_subtitle', 'Manage, review and moderate announcements')

@section('styles')
<style>
    .table-row-hover:hover {
        background-color: #f9fafb;
    }
    
    .badge {
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 9999px;
    }
</style>
@endsection

@section('content')
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
                <option value="draft">Draft</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select id="categoryFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                <option value="general">General</option>
                <option value="academic">Academic</option>
                <option value="events">Events</option>
                <option value="club">Club</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button id="searchBtn" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-search mr-2"></i>Filter
            </button>
            <button id="createBtn" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-plus mr-2"></i>Create New
            </button>
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
                    <td colspan="6" class="text-center py-8 text-gray-500">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                        <p>Loading announcements...</p>
                    </td>
                </tr>
            </tbody>
        </table>
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
                        <option value="events">Events</option>
                        <option value="club">Club</option>
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
                <div class="flex items-center">
                    <input type="checkbox" id="isOfficial" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">Mark as official announcement</span>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Save</button>
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
        
        <div class="flex justify-end gap-3 pt-4 border-t mt-4">
            <button onclick="closeViewModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Close</button>
        </div>
    </div>
</div>

@include('announcements.partials.detailed-verify-modal')
@endsection

@section('scripts')
<script>
    // Load announcements on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadAnnouncements();
        document.getElementById('searchBtn').addEventListener('click', loadAnnouncements);
        document.getElementById('createBtn').addEventListener('click', openCreateModal);
        document.getElementById('announcementForm').addEventListener('submit', handleFormSubmit);
        
        // Add Enter key support for search
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                loadAnnouncements();
            }
        });
    });

    function loadAnnouncements() {
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;
        const category = document.getElementById('categoryFilter').value;

        // Show loading state
        const announcementsList = document.getElementById('announcementsList');
        announcementsList.innerHTML = '<tr><td colspan="6" class="text-center py-8"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div><p class="text-gray-500">Loading announcements...</p></td></tr>';

        fetch(`/admin/announcements/list?search=${encodeURIComponent(search)}&status=${status}&category=${category}`)
            .then(response => response.json())
            .then(data => {
                announcementsList.innerHTML = '';

                if (data.success && data.data.data && data.data.data.length > 0) {
                    document.getElementById('announcementCount').textContent = data.data.total || data.data.data.length;
                    
                    data.data.data.forEach(announcement => {
                        const authorName = (announcement.author && announcement.author.name) || 'Unknown';
                        
                        const statusColor = announcement.status === 'pending_verification' ? 'bg-yellow-100 text-yellow-800' :
                                           announcement.status === 'published' ? 'bg-green-100 text-green-800' :
                                           announcement.status === 'draft' ? 'bg-gray-100 text-gray-800' :
                                           announcement.status === 'rejected' ? 'bg-red-100 text-red-800' :
                                           'bg-gray-100 text-gray-800';
                        
                        const categoryColor = announcement.category === 'general' ? 'bg-blue-100 text-blue-800' :
                                             announcement.category === 'academic' ? 'bg-purple-100 text-purple-800' :
                                             announcement.category === 'events' ? 'bg-green-100 text-green-800' :
                                             announcement.category === 'club' ? 'bg-teal-100 text-teal-800' :
                                             'bg-gray-100 text-gray-800';

                        const row = `
                            <tr class="table-row-hover">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900 truncate">${escapeHtml(announcement.title)}</p>
                                    <p class="text-sm text-gray-500 truncate">${escapeHtml(announcement.content?.substring(0, 50) || '')}...</p>
                                 </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${escapeHtml(authorName)}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="badge ${categoryColor}">${announcement.category}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="badge ${statusColor}">${announcement.status.replace('_', ' ')}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${new Date(announcement.created_at).toLocaleDateString()}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <button onclick="viewAnnouncement(${announcement.id})" class="text-blue-600 hover:text-blue-800 transition" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editAnnouncement(${announcement.id})" class="text-green-600 hover:text-green-800 transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    ${announcement.status === 'pending_verification' ? `
                                        <button onclick="openDetailedVerifyModal(${announcement.id})" class="text-purple-600 hover:text-purple-800 transition" title="Verify & Moderate">
                                            <i class="fas fa-shield-alt"></i>
                                        </button>
                                    ` : ''}
                                    <button onclick="deleteAnnouncement(${announcement.id})" class="text-red-600 hover:text-red-800 transition" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        announcementsList.innerHTML += row;
                    });
                } else {
                    document.getElementById('announcementCount').textContent = 0;
                    announcementsList.innerHTML = '<tr><td colspan="6" class="text-center py-8"><i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i><p class="text-gray-500">No announcements found</p></td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading announcements:', error);
                announcementsList.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-red-500"><i class="fas fa-exclamation-circle text-3xl mb-2"></i><p>Error loading announcements. Please try again.</p></td></tr>';
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

        if (!title || !content || !category || !status) {
            showNotification('Please fill in all required fields', 'error');
            return;
        }

        const method = id ? 'PUT' : 'POST';
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
        .then(response => response.json())
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
            console.error('Error:', error);
            showNotification('Error saving announcement', 'error');
        });
    }

    function viewAnnouncement(id) {
        fetch(`/admin/announcements/data/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const announcement = data.data;
                    document.getElementById('viewTitle').textContent = announcement.title;
                    const categoryClass = announcement.category === 'general' ? 'bg-blue-100 text-blue-800' :
                                         announcement.category === 'academic' ? 'bg-purple-100 text-purple-800' :
                                         announcement.category === 'events' ? 'bg-green-100 text-green-800' :
                                         announcement.category === 'club' ? 'bg-teal-100 text-teal-800' :
                                         'bg-gray-100 text-gray-800';
                    document.getElementById('viewCategory').innerHTML = `<span class="badge ${categoryClass}">${announcement.category}</span>`;
                    const statusClass = announcement.status === 'published' ? 'bg-green-100 text-green-800' :
                                       announcement.status === 'pending_verification' ? 'bg-yellow-100 text-yellow-800' :
                                       announcement.status === 'draft' ? 'bg-gray-100 text-gray-800' :
                                       announcement.status === 'rejected' ? 'bg-red-100 text-red-800' :
                                       'bg-gray-100 text-gray-800';
                    document.getElementById('viewStatus').innerHTML = `<span class="badge ${statusClass}">${announcement.status.replace('_', ' ')}</span>`;
                    document.getElementById('viewAuthor').textContent = (announcement.author && announcement.author.name) || 'Unknown';
                    document.getElementById('viewDate').textContent = new Date(announcement.created_at).toLocaleString();
                    document.getElementById('viewContent').textContent = announcement.content;
                    document.getElementById('viewModal').classList.remove('hidden');
                } else {
                    showNotification('Error loading announcement details', 'error');
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
                    document.getElementById('isOfficial').checked = announcement.is_official || false;
                    document.getElementById('announcementModal').classList.remove('hidden');
                } else {
                    showNotification('Error loading announcement for editing', 'error');
                }
            })
            .catch(() => showNotification('Error loading announcement', 'error'));
    }

    function deleteAnnouncement(id) {
        if (confirm('Are you sure you want to delete this announcement? This action cannot be undone.')) {
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


    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 transition-opacity duration-300 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
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

    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('announcementModal');
        const viewModal = document.getElementById('viewModal');
        if (event.target === modal) closeModal();
        if (event.target === viewModal) closeViewModal();
    });
</script>
@endsection