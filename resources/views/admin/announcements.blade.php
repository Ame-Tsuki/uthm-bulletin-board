<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Announcements - UTHM Bulletin Board System</title>
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
                    <a href="{{ route('admin.announcements.index') }}" class="flex items-center sidebar-link active-link p-3 rounded-lg ">
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
                    <a href="{{ route('admin.analytics') }}" class="flex items-center sidebar-link p-3 rounded-lg hover:bg-gray-700">
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
                                <h1 class="text-2xl font-bold text-gray-800">Announcements Management</h1>
                                <p class="text-gray-600 text-sm">Manage, review and moderate announcements</p>
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
                    <div class="flex items-center">
                        <input type="checkbox" id="isOfficial" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Mark as official announcement</span>
                    </div>
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
                                        <p class="font-medium text-gray-900 truncate">${escapeHtml(announcement.title)}</p>
                                        <p class="text-sm text-gray-500 truncate">${escapeHtml(announcement.content.substring(0, 50))}...</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${escapeHtml(authorName)}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${categoryColor}">${announcement.category}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${statusColor}">${announcement.status}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${new Date(announcement.created_at).toLocaleDateString()}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                        <button onclick="viewAnnouncement(${announcement.id})" class="text-blue-600 hover:text-blue-800" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editAnnouncement(${announcement.id})" class="text-green-600 hover:text-green-800" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        ${announcement.status === 'pending_verification' ? `
                                            <button onclick="approveAnnouncement(${announcement.id})" class="text-purple-600 hover:text-purple-800" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button onclick="rejectAnnouncement(${announcement.id})" class="text-orange-600 hover:text-orange-800" title="Reject">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        ` : ''}
                                        <button onclick="deleteAnnouncement(${announcement.id})" class="text-red-600 hover:text-red-800" title="Delete">
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
            if (reason && reason.trim() !== '') {
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

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
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
</body>
</html>