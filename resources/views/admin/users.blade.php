@extends('layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
            <p class="text-gray-600 mt-2">Manage and monitor all system users</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
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
            </div>
            <button id="searchBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Search</button>
            <button id="createUserBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 ml-2">Create User</button>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div id="usersTableContainer" class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="usersList">
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-500">Loading users...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Load users on page load
    document.addEventListener('DOMContentLoaded', loadUsers);

    function loadUsers() {
        const search = document.getElementById('searchInput').value;
        const role = document.getElementById('roleFilter').value;
        const verified = document.getElementById('verificationFilter').value;

        fetch(`/api/admin/users?search=${search}&role=${role}&verified=${verified}`)
            .then(response => response.json())
            .then(data => {
                const usersList = document.getElementById('usersList');
                usersList.innerHTML = '';

                if (data.success && data.data.data && data.data.data.length > 0) {
                    data.data.data.forEach(user => {
                        const row = `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="font-bold text-blue-600">${user.name.charAt(0).toUpperCase()}</span>
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-medium text-gray-900">${user.name}</p>
                                            <p class="text-sm text-gray-500">${user.uthm_id || 'N/A'}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${user.email}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                        user.role === 'admin' ? 'bg-red-100 text-red-800' :
                                        user.role === 'staff' ? 'bg-blue-100 text-blue-800' :
                                        user.role === 'student' ? 'bg-green-100 text-green-800' :
                                        'bg-gray-100 text-gray-800'
                                    }">${user.role}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                        user.is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                                    }">${user.is_verified ? 'Verified' : 'Unverified'}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button onclick="editUser(${user.id})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                    <button onclick="toggleVerify(${user.id})" class="text-green-600 hover:text-green-900 mr-3">Toggle Verify</button>
                                    <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        `;
                        usersList.innerHTML += row;
                    });
                } else {
                    usersList.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-gray-500">No users found</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading users:', error);
                document.getElementById('usersList').innerHTML = '<tr><td colspan="5" class="text-center py-8 text-red-500">Error loading users</td></tr>';
            });
    }

    document.getElementById('searchBtn').addEventListener('click', loadUsers);

    function editUser(userId) {
        // Implementation for editing user
        alert('Edit user ' + userId);
    }

    function toggleVerify(userId) {
        fetch(`/api/admin/users/${userId}/toggle-verification`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User verification toggled');
                loadUsers();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            fetch(`/api/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User deleted successfully');
                    loadUsers();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
</script>
@endsection
