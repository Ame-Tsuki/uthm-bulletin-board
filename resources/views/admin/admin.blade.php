<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - UTHM Bulletin Board System</title>
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
                    <a href="#" class="flex items-center sidebar-link active-link p-3 rounded-lg">
                        <i class="fas fa-tachometer-alt mr-3 text-gray-300"></i>
                        Dashboard
                    </a>
                    <a href="#" class="flex items-center sidebar-link p-3 rounded-lg">
                        <i class="fas fa-users mr-3 text-gray-300"></i>
                        User Management
                        <span class="ml-auto bg-red-500 text-xs px-2 py-1 rounded-full">{{ $stats['unverified_users'] ?? 0 }}</span>
                    </a>
                    <a href="#" class="flex items-center sidebar-link p-3 rounded-lg">
                        <i class="fas fa-clipboard-list mr-3 text-gray-300"></i>
                        Posts & Content
                    </a>
                    <a href="#" class="flex items-center sidebar-link p-3 rounded-lg">
                        <i class="fas fa-flag mr-3 text-gray-300"></i>
                        Moderation
                        <span class="ml-auto bg-yellow-500 text-xs px-2 py-1 rounded-full">12</span>
                    </a>
                    <a href="#" class="flex items-center sidebar-link p-3 rounded-lg">
                        <i class="fas fa-chart-bar mr-3 text-gray-300"></i>
                        Analytics
                    </a>
                    <a href="#" class="flex items-center sidebar-link p-3 rounded-lg">
                        <i class="fas fa-cog mr-3 text-gray-300"></i>
                        System Settings
                    </a>
                    <a href="#" class="flex items-center sidebar-link p-3 rounded-lg">
                        <i class="fas fa-bell mr-3 text-gray-300"></i>
                        Notifications
                    </a>
                </nav>
                
                <div class="mt-12 p-4 bg-gray-800 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="font-bold">A</span>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">Admin User</p>
                            <p class="text-sm text-gray-400">Super Administrator</p>
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
                                <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
                                <p class="text-gray-600 text-sm">Welcome back, Admin. Here's what's happening today.</p>
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
                                        <span class="text-white font-bold">A</span>
                                    </div>
                                    <span class="font-medium hidden md:inline">Administrator</span>
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
                            <a href="#" class="flex items-center p-3 rounded-lg bg-gray-800">
                                <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
                            </a>
                            <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-800">
                                <i class="fas fa-users mr-3"></i>User Management
                            </a>
                            <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-800">
                                <i class="fas fa-clipboard-list mr-3"></i>Posts & Content
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <main class="p-6">
                <!-- Stats Grid -->
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
                                <h3 class="text-3xl font-bold mt-2">{{ $stats['unverified_users'] ?? 0 }}</h3>
                                <p class="text-purple-100 text-sm mt-2">
                                    <i class="fas fa-clock mr-1"></i>Requires attention
                                </p>
                            </div>
                            <div class="bg-white bg-opacity-20 p-4 rounded-full">
                                <i class="fas fa-user-clock text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Quick Actions</h2>
                        <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">View All</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="#" class="bg-blue-50 hover:bg-blue-100 border-l-4 border-blue-500 p-4 rounded-lg transition">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-3 rounded-lg mr-4">
                                    <i class="fas fa-user-check text-blue-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">Verify Users</h3>
                                    <p class="text-sm text-gray-600">{{ $stats['unverified_users'] ?? 0 }} pending</p>
                                </div>
                            </div>
                        </a>
                        
                        <a href="#" class="bg-green-50 hover:bg-green-100 border-l-4 border-green-500 p-4 rounded-lg transition">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-3 rounded-lg mr-4">
                                    <i class="fas fa-flag text-green-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">Moderate Content</h3>
                                    <p class="text-sm text-gray-600">12 reports to review</p>
                                </div>
                            </div>
                        </a>
                        
                        <a href="#" class="bg-purple-50 hover:bg-purple-100 border-l-4 border-purple-500 p-4 rounded-lg transition">
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-3 rounded-lg mr-4">
                                    <i class="fas fa-chart-line text-purple-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">View Analytics</h3>
                                    <p class="text-sm text-gray-600">System performance</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Recent Users & Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Recent Users Table -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b">
                                <div class="flex justify-between items-center">
                                    <h2 class="text-xl font-bold text-gray-800">Recent Users</h2>
                                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All Users</a>
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
                                                        <span class="text-white font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
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

                    <!-- System Status -->
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

    <script>
        // Mobile menu toggle
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('mobileSidebar').classList.remove('hidden');
        });

        document.getElementById('closeMenu').addEventListener('click', function() {
            document.getElementById('mobileSidebar').classList.add('hidden');
        });

        // User dropdown
        document.getElementById('userMenu').addEventListener('click', function() {
            document.getElementById('dropdownMenu').classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdownMenu');
            const userMenu = document.getElementById('userMenu');
            
            if (!userMenu.contains(event.target) && !dropdown.contains(event.target)) {
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

        // Real-time data updates (simulated)
        function updateStats() {
            const stats = ['total_users', 'students', 'staff', 'unverified_users'];
            stats.forEach(stat => {
                const element = document.querySelector(`[data-stat="${stat}"]`);
                if (element) {
                    const current = parseInt(element.textContent);
                    const change = Math.floor(Math.random() * 3) - 1; // -1, 0, or 1
                    if (current + change >= 0) {
                        element.textContent = current + change;
                    }
                }
            });
        }

        // Update stats every 30 seconds
        // setInterval(updateStats, 30000);
    </script>
</body>
</html>