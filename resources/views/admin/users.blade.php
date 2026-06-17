@extends('layouts.admin')

@section('title', 'User Management - UTHM Bulletin Board System')
@section('page_title', 'User Management')
@section('page_subtitle', 'Manage and monitor all system users')

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
    
    /* Modal animation */
    .modal-enter {
        animation: fadeIn 0.3s ease-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>
@endsection

@section('content')
<!-- Search and Filter -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input type="text" id="searchInput" placeholder="Search by name, email, or ID" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
            <select id="roleFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Roles</option>
                <option value="student">Student</option>
                <option value="staff">Staff</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Verification</label>
            <select id="verificationFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Users</option>
                <option value="true">Verified</option>
                <option value="false">Unverified</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Account</label>
            <select id="bannedFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Accounts</option>
                <option value="false">Active</option>
                <option value="true">Banned</option>
            </select>
        </div>
    </div>
    <div class="flex gap-3">
        <button id="searchBtn" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm">
            <i class="fas fa-search mr-2"></i>Search
        </button>
        <button id="createUserBtn" class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition shadow-sm">
            <i class="fas fa-plus mr-2"></i>Create User
        </button>
    </div>
</div>

<!-- Users Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">All Users</h2>
            <span class="text-sm text-gray-600 bg-white px-3 py-1 rounded-full shadow-sm">Total: <span id="userCount" class="font-semibold text-blue-600">0</span></span>
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
                    <td colspan="4" class="text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mb-4"></div>
                        <p class="text-gray-500">Loading users...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Create/Edit User Modal -->
<div id="userModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-2xl shadow-xl rounded-xl bg-white modal-enter">
        <div class="flex justify-between items-center mb-5 pb-3 border-b">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Create User</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="userForm" class="space-y-4">
            <input type="hidden" id="userId">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                    <input type="text" id="userName" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" id="userEmail" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">UTHM ID</label>
                    <input type="text" id="userUthmId" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-400 mt-1">Optional - Will be auto-generated if empty</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                    <select id="userRole" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="student">Student</option>
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="userStatus" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="verified">Verified</option>
                        <option value="unverified">Unverified</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="userPassword" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="Leave blank to keep current">
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-5 mt-2 border-t">
                <button type="button" onclick="closeModal()" 
                        class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-sm">
                    <i class="fas fa-save mr-2"></i>Save User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Load users on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadUsers();
        
        // Add event listeners
        const searchBtn = document.getElementById('searchBtn');
        const createBtn = document.getElementById('createUserBtn');
        const searchInput = document.getElementById('searchInput');
        const roleFilter = document.getElementById('roleFilter');
        const verificationFilter = document.getElementById('verificationFilter');
        const bannedFilter = document.getElementById('bannedFilter');
        
        if (searchBtn) searchBtn.addEventListener('click', loadUsers);
        if (createBtn) createBtn.addEventListener('click', openCreateModal);
        if (searchInput) searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') loadUsers();
        });
        
        // Add filter change listeners for auto-search
        if (roleFilter) roleFilter.addEventListener('change', loadUsers);
        if (verificationFilter) verificationFilter.addEventListener('change', loadUsers);
        if (bannedFilter) bannedFilter.addEventListener('change', loadUsers);
        
        // Form submission
        const userForm = document.getElementById('userForm');
        if (userForm) userForm.addEventListener('submit', handleUserSubmit);
    });

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function roleBadgeClass(role) {
        if (role === 'admin') return 'bg-purple-100 text-purple-800';
        if (role === 'staff') return 'bg-yellow-100 text-yellow-800';
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

        // Show loading state
        const usersList = document.getElementById('usersList');
        if (usersList) {
            usersList.innerHTML = '<tr><td colspan="4" class="text-center py-12"><div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 mb-4"></div><p class="text-gray-500">Loading users...</p></td></tr>';
        }

        fetch(`/admin/users/list?${params.toString()}`, {
            headers: { 'Accept': 'application/json' },
        })
            .then(response => response.json())
            .then(data => {
                if (!usersList) return;
                
                usersList.innerHTML = '';

                if (data.success && data.data && data.data.data && data.data.data.length > 0) {
                    const userCount = document.getElementById('userCount');
                    if (userCount) userCount.textContent = data.data.total ?? data.data.data.length;
                    
                    data.data.data.forEach(user => {
                        const roleClass = roleBadgeClass(user.role);
                        const banBtn = user.is_banned
                            ? `<button onclick="toggleBan(${user.id})" class="text-green-600 hover:text-green-900 transition" title="Unban user"><i class="fas fa-user-check"></i></button>`
                            : `<button onclick="toggleBan(${user.id})" class="text-orange-600 hover:text-orange-900 transition" title="Ban user"><i class="fas fa-user-slash"></i></button>`;
                        
                        const row = `
                            <tr class="table-row-hover transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-md">
                                            <span class="text-white font-bold text-sm">${escapeHtml(user.name.charAt(0).toUpperCase())}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">${escapeHtml(user.name)}</div>
                                            <div class="text-sm text-gray-500">${escapeHtml(user.email)}</div>
                                            ${user.uthm_id ? `<div class="text-xs text-gray-400">ID: ${escapeHtml(user.uthm_id)}</div>` : ''}
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
                                    <div class="flex space-x-3">
                                        <button onclick="editUser(${user.id})" class="text-green-600 hover:text-green-800 transition" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        ${banBtn}
                                        <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-800 transition" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                 </td>
                            </tr>
                        `;
                        usersList.innerHTML += row;
                    });
                } else {
                    const userCount = document.getElementById('userCount');
                    if (userCount) userCount.textContent = 0;
                    usersList.innerHTML = '<tr><td colspan="4" class="text-center py-12"><i class="fas fa-users text-4xl mb-3 text-gray-300"></i><p class="text-gray-500">No users found</p></td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading users:', error);
                if (usersList) {
                    usersList.innerHTML = '<tr><td colspan="4" class="text-center py-12 text-red-500"><i class="fas fa-exclamation-circle text-3xl mb-2"></i><p>Error loading users. Please try again.</p></td></tr>';
                }
                showNotification('Error loading users', 'error');
            });
    }

    function openCreateModal() {
        const modalTitle = document.getElementById('modalTitle');
        const userId = document.getElementById('userId');
        const userForm = document.getElementById('userForm');
        const userPassword = document.getElementById('userPassword');
        
        if (modalTitle) modalTitle.textContent = 'Create User';
        if (userId) userId.value = '';
        if (userForm) userForm.reset();
        if (userPassword) userPassword.placeholder = 'Enter password for new user';
        
        const modal = document.getElementById('userModal');
        if (modal) modal.classList.remove('hidden');
    }

    function toggleBan(userId) {
        if (!confirm('Are you sure you want to change the ban status for this user?')) return;

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
                    showNotification(data.message, 'success');
                    loadUsers();
                } else {
                    showNotification(data.message || 'Error updating ban status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error updating ban status', 'error');
            });
    }

    function editUser(userId) {
        fetch(`/admin/users/${userId}`, { headers: { 'Accept': 'application/json' } })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.data;
                    const modalTitle = document.getElementById('modalTitle');
                    const userIdInput = document.getElementById('userId');
                    const userName = document.getElementById('userName');
                    const userEmail = document.getElementById('userEmail');
                    const userUthmId = document.getElementById('userUthmId');
                    const userRole = document.getElementById('userRole');
                    const userStatus = document.getElementById('userStatus');
                    const userPassword = document.getElementById('userPassword');
                    
                    if (modalTitle) modalTitle.textContent = 'Edit User';
                    if (userIdInput) userIdInput.value = user.id;
                    if (userName) userName.value = user.name;
                    if (userEmail) userEmail.value = user.email;
                    if (userUthmId) userUthmId.value = user.uthm_id || '';
                    if (userRole) userRole.value = user.role;
                    if (userStatus) userStatus.value = user.is_verified ? 'verified' : 'unverified';
                    if (userPassword) {
                        userPassword.value = '';
                        userPassword.placeholder = 'Leave blank to keep current password';
                    }
                    
                    const modal = document.getElementById('userModal');
                    if (modal) modal.classList.remove('hidden');
                } else {
                    showNotification('Error loading user data', 'error');
                }
            })
            .catch(error => {
                console.error('Error loading user:', error);
                showNotification('Error loading user data', 'error');
            });
    }

    function handleUserSubmit(e) {
        e.preventDefault();
        
        const userId = document.getElementById('userId')?.value;
        const isEdit = userId && userId !== '';
        
        const userData = {
            name: document.getElementById('userName')?.value,
            email: document.getElementById('userEmail')?.value,
            uthm_id: document.getElementById('userUthmId')?.value,
            role: document.getElementById('userRole')?.value,
            is_verified: document.getElementById('userStatus')?.value === 'verified'
        };
        
        const password = document.getElementById('userPassword')?.value;
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
                showNotification(isEdit ? 'User updated successfully' : 'User created successfully', 'success');
                loadUsers();
            } else {
                showNotification(data.message || 'Error saving user', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error saving user', 'error');
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
                    showNotification('User deleted successfully', 'success');
                    loadUsers();
                } else {
                    showNotification(data.message || 'Error deleting user', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error deleting user', 'error');
            });
        }
    }

    function closeModal() {
        const modal = document.getElementById('userModal');
        if (modal) modal.classList.add('hidden');
    }

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg text-white z-50 transition-opacity duration-300 shadow-lg ${
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

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('userModal');
        if (event.target === modal) {
            closeModal();
        }
    });
</script>
@endsection