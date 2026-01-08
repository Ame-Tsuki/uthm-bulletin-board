<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - UTHM Bulletin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-gray-800 text-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex items-center space-x-3">
                        <div class="bg-red-600 p-2 rounded-lg">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">Administrator Dashboard</h1>
                            <p class="text-sm text-gray-300">Full System Control</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-blue-300 hover:text-white">Main Dashboard</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Admin Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">{{ $stats['total_users'] ?? 0 }}</h3>
                            <p class="text-gray-600">Total Users</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-user-graduate text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">{{ $stats['students'] ?? 0 }}</h3>
                            <p class="text-gray-600">Students</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-user-tie text-yellow-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">{{ $stats['staff'] ?? 0 }}</h3>
                            <p class="text-gray-600">Staff</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-user-clock text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">{{ $stats['unverified_users'] ?? 0 }}</h3>
                            <p class="text-gray-600">Pending Verifications</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- User Management -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-6">User Management</h2>
                    <div class="space-y-4">
                        <a href="#" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-user-check text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold">Verify New Users</h3>
                                <p class="text-sm text-gray-600">{{ $stats['unverified_users'] ?? 0 }} users pending verification</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                        
                        <a href="#" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="bg-green-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-user-cog text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold">Role Management</h3>
                                <p class="text-sm text-gray-600">Manage user roles and permissions</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                    </div>
                </div>

                <!-- System Management -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-6">System Management</h2>
                    <div class="space-y-4">
                        <a href="#" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="bg-yellow-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-flag text-yellow-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold">Content Moderation</h3>
                                <p class="text-sm text-gray-600">12 reports pending review</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                        
                        <a href="#" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                <i class="fas fa-chart-line text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold">System Analytics</h3>
                                <p class="text-sm text-gray-600">View usage statistics and reports</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-6">Recent Users</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($stats['recent_users'] ?? [] as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-800 font-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->is_verified)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Verified
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Unverified
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No recent users</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>