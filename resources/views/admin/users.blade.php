@extends('layouts.admin')

@section('title', 'User Management')
@section('page_title', 'User Management')
@section('page_subtitle', 'Manage and monitor all system users')

@section('content')
<div class="grid grid-cols-1">
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
            <div class="px-6 py-4 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800">All Users</h2>
                    <span class="text-sm text-gray-600">Total: <span id="userCount">{{ count($users ?? []) }}</span></span>
                </div>
            </div>
            <div id="usersTableContainer" class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="usersList">
                        @forelse($users ?? [] as $user)
                        <tr class="table-row-hover">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->uthm_id ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role == 'admin' ? 'bg-red-100 text-red-800' : ($user->role == 'staff' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_verified)
                                    <span class="badge bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Verified
                                    </span>
                                @else
                                    <span class="badge bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex space-x-2">
                                    <button class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-users text-3xl mb-2 text-gray-300"></i>
                                <p>No users found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
</div>

@endsection

@section('scripts')
<script>
    // Load users on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadUsers();
        document.getElementById('searchBtn').addEventListener('click', loadUsers);
    });

    function loadUsers() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const role = document.getElementById('roleFilter').value;
        const verified = document.getElementById('verificationFilter').value;

        // Try to fetch from API first, fallback to page refresh if needed
        const url = new URL(`/api/admin/users`, window.location.origin);
        if (search) url.searchParams.append('search', search);
        if (role) url.searchParams.append('role', role);
        if (verified) url.searchParams.append('verified', verified);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const usersList = document.getElementById('usersList');
                usersList.innerHTML = '';

                if (data.success && data.data.data && data.data.data.length > 0) {
                    data.data.data.forEach(user => {
                        const row = `
                            <tr class="table-row-hover">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                            <span class="text-white font-bold">${user.name.charAt(0).toUpperCase()}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">${user.name}</div>
                                            <div class="text-sm text-gray-500">${user.uthm_id || 'N/A'}</div>
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
                                    <span class="badge ${user.is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                        ${user.is_verified ? '<i class="fas fa-check-circle mr-1"></i> Verified' : '<i class="fas fa-clock mr-1"></i> Pending'}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="text-green-600 hover:text-green-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        usersList.innerHTML += row;
                    });
                    document.getElementById('userCount').textContent = data.data.data.length;
                } else {
                    usersList.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-gray-500"><i class="fas fa-users text-3xl mb-2 text-gray-300"></i><p>No users found</p></td></tr>';
                }
            })
            .catch(error => {
                console.log('API not available, using page data');
                // If API fails, just keep the current page data
            });
    }

    document.getElementById('createUserBtn').addEventListener('click', function() {
        alert('Create user functionality would open a form');
    });
</script>
@endsection
