@extends('layouts.admin')

@section('title', 'Admin Dashboard - UTHM Bulletin Board System')

@section('page_title', 'Dashboard Overview')

@section('page_subtitle', 'Welcome back, ' . (Auth::user()->name ?? 'Admin') . '. Here\'s what\'s happening today.')

@section('styles')
<style>
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .badge {
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 9999px;
    }
    
    .table-row-hover:hover {
        background-color: #f9fafb;
    }
</style>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stat-card bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-blue-100">Total Users</p>
                <h3 class="text-3xl font-bold mt-2">{{ $stats['total_users'] ?? 0 }}</h3>
                <p class="text-blue-100 text-sm mt-2">
                    <i class="fas fa-arrow-up mr-1"></i>12% from last month
                </p>
            </div>
            <div class="bg-white bg-opacity-20 p-4 rounded-full">
                <i class="fas fa-users text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-xl shadow">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-green-100">Active Students</p>
                <h3 class="text-3xl font-bold mt-2">{{ $stats['students'] ?? 0 }}</h3>
                <p class="text-green-100 text-sm mt-2">
                    <i class="fas fa-user-graduate mr-1"></i>{{ round(($stats['students'] ?? 0) / ($stats['total_users'] ?? 1) * 100, 1) }}% of total
                </p>
            </div>
            <div class="bg-white bg-opacity-20 p-4 rounded-full">
                <i class="fas fa-user-graduate text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-xl shadow">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-orange-100">Staff Members</p>
                <h3 class="text-3xl font-bold mt-2">{{ $stats['staff'] ?? 0 }}</h3>
                <p class="text-orange-100 text-sm mt-2">
                    <i class="fas fa-user-tie mr-1"></i>{{ round(($stats['staff'] ?? 0) / ($stats['total_users'] ?? 1) * 100, 1) }}% of total
                </p>
            </div>
            <div class="bg-white bg-opacity-20 p-4 rounded-full">
                <i class="fas fa-user-tie text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-purple-100">Pending Verification</p>
                <h3 class="text-3xl font-bold mt-2" id="top-unverified-count">{{ $stats['unverified_users'] ?? 0 }}</h3>
                <p class="text-purple-100 text-sm mt-2">
                    <i class="fas fa-clock mr-1"></i>Requires attention
                </p>
            </div>
            <div class="bg-white bg-opacity-20 p-4 rounded-full">
                <i class="fas fa-user-clock text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-gradient-to-r from-red-500 to-red-600 text-white p-6 rounded-xl shadow">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-red-100">Total Announcements</p>
                <h3 class="text-3xl font-bold mt-2" id="total-announcements">0</h3>
                <p class="text-red-100 text-sm mt-2">
                    <i class="fas fa-megaphone mr-1"></i>Across platform
                </p>
            </div>
            <div class="bg-white bg-opacity-20 p-4 rounded-full">
                <i class="fas fa-megaphone text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-gradient-to-r from-indigo-500 to-indigo-600 text-white p-6 rounded-xl shadow">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-indigo-100">Total Events</p>
                <h3 class="text-3xl font-bold mt-2" id="total-events">0</h3>
                <p class="text-indigo-100 text-sm mt-2">
                    <i class="fas fa-calendar mr-1"></i>All activities
                </p>
            </div>
            <div class="bg-white bg-opacity-20 p-4 rounded-full">
                <i class="fas fa-calendar text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-6 mb-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Quick Actions</h2>
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">View All</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.users.index') }}" class="bg-blue-50 hover:bg-blue-100 border-l-4 border-blue-500 p-4 rounded-lg transition block">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-user-check text-blue-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Verify Users</h3>
                    <p class="text-sm text-gray-600" id="quick-action-unverified-text">{{ $stats['unverified_users'] ?? 0 }} pending</p>
                </div>
            </div>
        </a>
        
        <a href="{{ route('admin.moderation') }}" class="bg-green-50 hover:bg-green-100 border-l-4 border-green-500 p-4 rounded-lg transition block">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-flag text-green-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Moderate Content</h3>
                    <p class="text-sm text-gray-600" id="pending-reports-count">0 reports to review</p>
                </div>
            </div>
        </a>
        
        <a href="{{ route('admin.settings.index') }}" class="bg-purple-50 hover:bg-purple-100 border-l-4 border-purple-500 p-4 rounded-lg transition block">
            <div class="flex items-center">
                <div class="bg-purple-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-sliders-h text-purple-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">System Settings</h3>
                    <p class="text-sm text-gray-600">Configure parameters</p>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800">Recent Users</h2>
                    <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All Users</a>
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
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($stats['recent_users'] ?? [] as $user)
                        <tr class="table-row-hover">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold">{{ $user->name ? strtoupper(substr($user->name, 0, 1)) : 'U' }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="badge {{ $user->role == 'admin' ? 'bg-purple-100 text-purple-800' : ($user->role == 'staff' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_banned ?? false)
                                    <span class="badge bg-red-100 text-red-800">
                                        <i class="fas fa-ban mr-1"></i> Banned
                                    </span>
                                @elseif($user->is_verified)
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
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(Route::has('admin.users.edit'))
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="text-green-600 hover:text-green-900" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @else
                                        <button class="text-green-600 hover:text-green-900" onclick="editUser({{ $user->id }})" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif
                                    <button class="text-red-600 hover:text-red-900" onclick="confirmDelete({{ $user->id }})" title="Delete User">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-users text-3xl mb-2 text-gray-300"></i>
                                <p>No recent users found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">System Status</h2>
        
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full bg-green-500 mr-3"></div>
                    <span class="font-medium">Database</span>
                </div>
                <span class="text-green-600 font-bold">Online</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full bg-green-500 mr-3"></div>
                    <span class="font-medium">Mail Server</span>
                </div>
                <span class="text-green-600 font-bold">Online</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full bg-yellow-500 mr-3"></div>
                    <span class="font-medium">Storage</span>
                </div>
                <span class="text-yellow-600 font-bold">75% Used</span>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full bg-green-500 mr-3"></div>
                    <span class="font-medium">API Services</span>
                </div>
                <span class="text-green-600 font-bold">Online</span>
            </div>
        </div>
        
        <div class="mt-8">
            <h3 class="font-bold text-gray-700 mb-3">Recent Activity</h3>
            <div class="space-y-3">
                <div class="flex items-start">
                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-user-plus text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm">New user registration</p>
                        <p class="text-xs text-gray-500">2 minutes ago</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="bg-green-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm">System backup completed</p>
                        <p class="text-xs text-gray-500">1 hour ago</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Helper function to safely read metadata tags
    const getMetaToken = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    };

    // Mobile menu toggle
    if (document.getElementById('menuToggle')) {
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('mobileSidebar').classList.remove('hidden');
        });
    }

    if (document.getElementById('closeMenu')) {
        document.getElementById('closeMenu').addEventListener('click', function() {
            document.getElementById('mobileSidebar').classList.add('hidden');
        });
    }

    // User dropdown
    if (document.getElementById('userMenu')) {
        document.getElementById('userMenu').addEventListener('click', function() {
            document.getElementById('dropdownMenu').classList.toggle('hidden');
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdownMenu');
        const userMenu = document.getElementById('userMenu');
        
        if (dropdown && userMenu && !userMenu.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Auto-hide success messages
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-auto-hide');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    // Load content statistics
    async function loadContentStats() {
        try {
            const response = await fetch('{{ route("admin.content-stats") }}');
            const data = await response.json();
            
            if (data.success) {
                document.getElementById('total-announcements').textContent = data.data.total_announcements || 0;
                document.getElementById('total-events').textContent = data.data.total_events || 0;
            }
        } catch (error) {
            console.error('Error loading content stats:', error);
        }
    }
    
    // Load pending reports count & sync verification layout components
    async function loadPendingReports() {
        try {
            const response = await fetch('/api/admin/reports/statistics');
            const data = await response.json();
            
            if (data.success) {
                // Update text container in Quick Actions element row
                if (document.getElementById('pending-reports-count')) {
                    const pendingCount = data.data.pending || 0;
                    document.getElementById('pending-reports-count').textContent = `${pendingCount} report${pendingCount !== 1 ? 's' : ''} to review`;
                }

                // Synchronize dynamic counter badges across blocks if provided by API payload
                if (data.data.unverified_users !== undefined) {
                    if (document.getElementById('top-unverified-count')) {
                        document.getElementById('top-unverified-count').textContent = data.data.unverified_users;
                    }
                    if (document.getElementById('quick-action-unverified-text')) {
                        document.getElementById('quick-action-unverified-text').textContent = `${data.data.unverified_users} pending`;
                    }
                }
            }
        } catch (error) {
            console.error('Error loading pending reports:', error);
        }
    }

    // Confirm delete function
    function confirmDelete(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            const token = getMetaToken();
            if (!token) {
                console.error('CSRF token not found. Deletion halted.');
                alert('An authentication error occurred. Missing token protection.');
                return;
            }

            fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('An error occurred. Check backend network permissions.');
                }
            }).catch(error => console.error('Error:', error));
        }
    }
    
    // Edit user fallback function
    function editUser(userId) {
        window.location.href = `/admin/users/${userId}`;
    }

    // Load stats on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadContentStats();
        loadPendingReports();
    });

    // Refresh content stats on set loop windows
    setInterval(loadContentStats, 60000);
    setInterval(loadPendingReports, 30000);
</script>
@endsection