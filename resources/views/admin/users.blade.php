<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - UTHM Bulletin Board System</title>
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
                    <a href="{{ url('/admin/dashboard') }}" class="flex items-center sidebar-link p-3 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-tachometer-alt mr-3 text-gray-300"></i>
                        Dashboard
                    </a>
                    <a href="{{ url('/admin/users') }}" class="flex items-center sidebar-link active-link p-3 rounded-lg">
                        <i class="fas fa-users mr-3 text-gray-300"></i>
                        User Management
                     
                    </a>
                    <a href="{{ url('/admin/announcements') }}" class="flex items-center sidebar-link p-3 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-clipboard-list mr-3 text-gray-300"></i>
                        Posts & Content
                    </a>
                    <a href="{{ url('/admin/moderation') }}" class="flex items-center sidebar-link p-3 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-flag mr-3 text-gray-300"></i>
                        Moderation
                      
                    </a>
                    <a href="{{ url('/admin/calendar') }}" class="flex items-center sidebar-link p-3 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-calendar-alt mr-3 text-gray-300"></i>
                        Calendar
                    </a>
                    <a href="{{ route('admin.featured-posts') }}" class="flex items-center sidebar-link p-3 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-star mr-3 text-gray-300"></i>
                        Featured Posts
                    </a>
                    <a href="{{ url('/admin/analytics') }}" class="flex items-center sidebar-link p-3 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-chart-bar mr-3 text-gray-300"></i>
                        Analytics
                    </a>
                    <a href="{{ url('/admin/settings') }}" class="flex items-center sidebar-link p-3 rounded-lg hover:bg-gray-700">
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
                                <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
                                <p class="text-gray-600 text-sm">Manage and monitor all system users</p>
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
                            <a href="{{ url('/admin/dashboard') }}" class="flex items-center p-3 rounded-lg bg-gray-800">
                                <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                            </a>
                            <a href="{{ url('/admin/users') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800">
                                <i class="fas fa-users mr-3"></i>User Management
                            </a>
                            <a href="{{ url('/admin/announcements') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800">
                                <i class="fas fa-clipboard-list mr-3"></i>Posts & Content
                            </a>
                            <a href="{{ url('/admin/moderation') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800">
                                <i class="fas fa-flag mr-3"></i>Moderation
                            </a>
                            <a href="{{ url('/admin/calendar') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800">
                                <i class="fas fa-calendar-alt mr-3"></i>Calendar
                            </a>
                            <a href="{{ url('/admin/analytics') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800">
                                <i class="fas fa-chart-bar mr-3"></i>Analytics
                            </a>
                            <a href="{{ url('/admin/settings') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800">
                                <i class="fas fa-cog mr-3"></i>Settings
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <main class="p-6">
                <!-- Search and Filter -->
                <div class="bg-white rounded-lg shadow p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" id="searchInput" placeholder="Search by name, email, or ID" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                            <select id="roleFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">All Roles</option>
                                <option value="student">Student</option>
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                                <option value="club_admin">Club Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Verification</label>
                            <select id="verificationFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">All Users</option>
                                <option value="true">Verified</option>
                                <option value="false">Unverified</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account</label>
                            <select id="bannedFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">All Accounts</option>
                                <option value="false">Active</option>
                                <option value="true">Banned</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button id="searchBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Search</button>
                        <button id="createUserBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Create User</button>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold text-gray-800">All Users</h2>
                            <span class="text-sm text-gray-600">Total: <span id="userCount">0</span></span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="usersList">
                                <tr>
                                    <td colspan="4" class="text-center py-8">
                                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                                        <p class="text-gray-500">Loading users...</p>
                                    </td>
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
                        <span class="text-sm text-gray-600">Last updated: Today</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Create/Edit User Modal -->
    <div id="userModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalTitle" class="text-lg font-bold text-gray-900">Create User</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="userForm" class="space-y-4">
                <input type="hidden" id="userId">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" id="userName" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" id="userEmail" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">UTHM ID</label>
                    <input type="text" id="userUthmId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                        <select id="userRole" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="student">Student</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                            <option value="club_admin">Club Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="userStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="verified">Verified</option>
                            <option value="unverified">Unverified</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="userPassword" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Leave blank to keep current">
                </div>
                
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save User</button>
                </div>
            </form>
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

        // Load users on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
            document.getElementById('searchBtn')?.addEventListener('click', loadUsers);
            document.getElementById('createUserBtn')?.addEventListener('click', openCreateModal);
            document.getElementById('userForm')?.addEventListener('submit', handleUserSubmit);
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        function roleBadgeClass(role) {
            if (role === 'admin') return 'bg-purple-100 text-purple-800';
            if (role === 'staff') return 'bg-yellow-100 text-yellow-800';
            if (role === 'club_admin') return 'bg-indigo-100 text-indigo-800';
            return 'bg-blue-100 text-blue-800';
        }

        function formatRole(role) {
            if (!role) return '';
            return role.charAt(0).toUpperCase() + role.slice(1).replace('_', ' ');
        }

        function statusBadge(user) {
            if (user.is_banned) {
                return '<span class="badge bg-red-100 text-red-800"><i class="fas fa-ban mr-1"></i> Banned</span>';
            }
            if (user.is_verified) {
                return '<span class="badge bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i> Verified</span>';
            }
            return '<span class="badge bg-yellow-100 text-yellow-800"><i class="fas fa-clock mr-1"></i> Pending</span>';
        }

        function loadUsers() {
            const search = document.getElementById('searchInput')?.value || '';
            const role = document.getElementById('roleFilter')?.value || '';
            const verified = document.getElementById('verificationFilter')?.value || '';
            const banned = document.getElementById('bannedFilter')?.value || '';

            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (role) params.append('role', role);
            if (verified === 'true' || verified === 'false') params.append('verified', verified);
            if (banned === 'true' || banned === 'false') params.append('banned', banned);

            fetch(`/admin/users/list?${params.toString()}`, {
                headers: { 'Accept': 'application/json' },
            })
                .then(response => response.json())
                .then(data => {
                    const usersList = document.getElementById('usersList');
                    if (!usersList) return;
                    
                    usersList.innerHTML = '';

                    if (data.success && data.data && data.data.data && data.data.data.length > 0) {
                        document.getElementById('userCount').textContent = data.data.total ?? data.data.data.length;
                        
                        data.data.data.forEach(user => {
                            const roleClass = roleBadgeClass(user.role);
                            const banBtn = user.is_banned
                                ? `<button onclick="toggleBan(${user.id})" class="text-green-600 hover:text-green-900" title="Unban user"><i class="fas fa-user-check"></i></button>`
                                : `<button onclick="toggleBan(${user.id})" class="text-orange-600 hover:text-orange-900" title="Ban user"><i class="fas fa-user-slash"></i></button>`;
                            
                            const row = `
                                <tr class="table-row-hover">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold">${user.name.charAt(0).toUpperCase()}</span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">${escapeHtml(user.name)}</div>
                                                <div class="text-sm text-gray-500">${escapeHtml(user.email)}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="badge ${roleClass}">${escapeHtml(formatRole(user.role))}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        ${statusBadge(user)}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex space-x-2">
                                            <button onclick="editUser(${user.id})" class="text-green-600 hover:text-green-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            ${banBtn}
                                            <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-900" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            usersList.innerHTML += row;
                        });
                    } else {
                        document.getElementById('userCount').textContent = 0;
                        usersList.innerHTML = '<tr><td colspan="4" class="text-center py-8 text-gray-500"><i class="fas fa-users text-3xl mb-2 text-gray-300"></i><p>No users found</p></td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error loading users:', error);
                    document.getElementById('usersList').innerHTML = '<tr><td colspan="4" class="text-center py-8 text-red-500">Error loading users. Please try again.</td></tr>';
                });
        }

        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Create User';
            document.getElementById('userId').value = '';
            document.getElementById('userForm').reset();
            document.getElementById('userPassword').placeholder = 'Enter password for new user';
            document.getElementById('userModal').classList.remove('hidden');
        }

        function toggleBan(userId) {
            if (!confirm('Change ban status for this user?')) return;

            fetch(`/admin/users/${userId}/toggle-ban`, {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        loadUsers();
                    } else {
                        alert(data.message || 'Error updating ban status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating ban status');
                });
        }

        function editUser(userId) {
            fetch(`/admin/users/${userId}`, { headers: { 'Accept': 'application/json' } })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const user = data.data;
                        document.getElementById('modalTitle').textContent = 'Edit User';
                        document.getElementById('userId').value = user.id;
                        document.getElementById('userName').value = user.name;
                        document.getElementById('userEmail').value = user.email;
                        document.getElementById('userUthmId').value = user.uthm_id || '';
                        document.getElementById('userRole').value = user.role;
                        document.getElementById('userStatus').value = user.is_verified ? 'verified' : 'unverified';
                        document.getElementById('userPassword').value = '';
                        document.getElementById('userPassword').placeholder = 'Leave blank to keep current password';
                        document.getElementById('userModal').classList.remove('hidden');
                    }
                })
                .catch(error => console.error('Error loading user:', error));
        }

        function handleUserSubmit(e) {
            e.preventDefault();
            
            const userId = document.getElementById('userId').value;
            const isEdit = userId && userId !== '';
            
            const userData = {
                name: document.getElementById('userName').value,
                email: document.getElementById('userEmail').value,
                uthm_id: document.getElementById('userUthmId').value,
                role: document.getElementById('userRole').value,
                is_verified: document.getElementById('userStatus').value === 'verified'
            };
            
            const password = document.getElementById('userPassword').value;
            if (password) {
                userData.password = password;
            }
            
            const url = isEdit ? `/admin/users/${userId}` : '/admin/users/create';
            const method = isEdit ? 'PUT' : 'POST';
            
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(userData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    alert(isEdit ? 'User updated successfully' : 'User created successfully');
                    loadUsers();
                } else {
                    alert(data.message || 'Error saving user');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving user');
            });
        }

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                fetch(`/admin/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('User deleted successfully');
                        loadUsers();
                    } else {
                        alert(data.message || 'Error deleting user');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting user');
                });
            }
        }

        function closeModal() {
            document.getElementById('userModal').classList.add('hidden');
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