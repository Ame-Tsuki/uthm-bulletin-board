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
@php
    // Safely define base counts
    $totalUsers = $stats['total_users'] ?? 0;
    $students = $stats['students'] ?? 0;
    $staff = $stats['staff'] ?? 0;
    
    // Calculate percentages (rounded to 1 decimal place), defaulting to 0 if no users exist
    $studentPercentage = $totalUsers > 0 ? round(($students / $totalUsers) * 100, 1) : 0;
    $staffPercentage = $totalUsers > 0 ? round(($staff / $totalUsers) * 100, 1) : 0;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stat-card bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-xl shadow">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-blue-100">Total Users</p>
                <h3 class="text-3xl font-bold mt-2">{{ $totalUsers }}</h3>
                <p class="text-blue-100 text-sm mt-2">
                    <i class="fas fa-arrow-up mr-1"></i>{{ $stats['user_growth_percentage'] ?? '0' }}% from last month
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
                <h3 class="text-3xl font-bold mt-2">{{ $students }}</h3>
                <p class="text-green-100 text-sm mt-2">
                    <i class="fas fa-user-graduate mr-1"></i>{{ $studentPercentage }}% of total
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
                <h3 class="text-3xl font-bold mt-2">{{ $staff }}</h3>
                <p class="text-orange-100 text-sm mt-2">
                    <i class="fas fa-user-tie mr-1"></i>{{ $staffPercentage }}% of total
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
                <p class="text-purple-100">Pending Reports</p>
                <h3 class="text-3xl font-bold mt-2" id="pending-reports-count-stat">{{ $stats['pending_reports'] ?? 0 }}</h3>
                <p class="text-purple-100 text-sm mt-2">
                    <i class="fas fa-flag mr-1"></i>{{ $stats['pending_verification_text'] ?? 'Requires review' }}
                </p>
            </div>
            <div class="bg-white bg-opacity-20 p-4 rounded-full">
                <i class="fas fa-flag text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-6 mb-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Quick Actions</h2>
        <a href="{{ route('admin.moderation') }}" class="text-blue-600 hover:text-blue-800 font-medium">View All Reports</a>
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
                    <p class="text-sm text-gray-600" id="pending-reports-count">{{ $stats['pending_reports'] ?? 0 }} report(s) to review</p>
                </div>
            </div>
        </a>
        
        <a href="{{ route('admin.announcements.index') }}" class="bg-yellow-50 hover:bg-yellow-100 border-l-4 border-yellow-500 p-4 rounded-lg transition block">
            <div class="flex items-center">
                <div class="bg-yellow-100 p-3 rounded-lg mr-4">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Pending Verification</h3>
                    <p class="text-sm text-gray-600" id="pending-verification-count">{{ $stats['pending_verification_announcements'] ?? 0 }} announcement(s) waiting</p>
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
                                <p>{{ $stats['no_users_message'] ?? 'No recent users found' }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        
        
        <div class="mt-8">
            <h3 class="font-bold text-gray-700 mb-3">Recent Activity</h3>
            <div class="space-y-3">
                @forelse($stats['recent_activities'] ?? [] as $activity)
                <div class="flex items-start">
                    <div class="{{ $activity['icon_bg'] ?? 'bg-blue-100' }} p-2 rounded-lg mr-3">
                        <i class="{{ $activity['icon'] ?? 'fas fa-info-circle' }} {{ $activity['icon_color'] ?? 'text-blue-600' }}"></i>
                    </div>
                    <div>
                        <p class="text-sm">{{ $activity['message'] }}</p>
                        <p class="text-xs text-gray-500">{{ $activity['time_ago'] }}</p>
                    </div>
                </div>
                @empty
                <div class="flex items-start">
                    <div class="bg-gray-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-info-circle text-gray-600"></i>
                    </div>
                    <div>
                        <p class="text-sm">No recent activities</p>
                        <p class="text-xs text-gray-500">Activities will appear here</p>
                    </div>
                </div>
                @endforelse
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

    // Load moderation statistics (reports)
    async function loadModerationStats() {
        try {
            const response = await fetch('/api/admin/reports/statistics');
            const data = await response.json();
            
            if (data.success) {
                const pendingCount = data.data.pending || 0;
                const resolvedCount = data.data.resolved || 0;
                const dismissedCount = data.data.dismissed || 0;
                const totalCount = data.data.total || 0;
                
                // Update pending reports count in stats card
                if (document.getElementById('pending-reports-count-stat')) {
                    document.getElementById('pending-reports-count-stat').textContent = pendingCount;
                }
                
                // Update quick action text
                if (document.getElementById('pending-reports-count')) {
                    document.getElementById('pending-reports-count').textContent = `${pendingCount} report(s) to review`;
                }
                
                // Optionally, you can update other report-related stats
                console.log(`Moderation stats loaded: ${pendingCount} pending, ${resolvedCount} resolved, ${dismissedCount} dismissed, ${totalCount} total`);
            }
        } catch (error) {
            console.error('Error loading moderation statistics:', error);
        }
    }
    
    // Load pending verification announcements count
    async function loadPendingVerificationCount() {
        try {
            const response = await fetch('/announcements/pending-count');
            const data = await response.json();
            
            if (data.count !== undefined) {
                if (document.getElementById('pending-verification-count')) {
                    document.getElementById('pending-verification-count').textContent = `${data.count} announcement(s) waiting`;
                }
            }
        } catch (error) {
            console.error('Error loading pending verification count:', error);
            // Fallback to Laravel passed data if API fails
            const fallbackCount = {{ $stats['pending_verification_announcements'] ?? 0 }};
            if (document.getElementById('pending-verification-count')) {
                document.getElementById('pending-verification-count').textContent = `${fallbackCount} announcement(s) waiting`;
            }
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

    // Load all stats on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadModerationStats();
        loadPendingVerificationCount();
    });

    // Refresh stats periodically
    setInterval(loadModerationStats, 30000);  // Refresh every 30 seconds
    setInterval(loadPendingVerificationCount, 60000);  // Refresh every minute
</script>
@endsection