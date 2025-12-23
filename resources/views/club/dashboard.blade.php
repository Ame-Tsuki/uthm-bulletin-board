<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Admin Dashboard - UTHM Bulletin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">UTHM Digital Bulletin Board</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="mr-4">Welcome, {{ auth()->user()->name }}</span>
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
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Club Admin Dashboard</h1>
                <p class="mt-2 text-gray-600">Manage your club announcements and activities</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- User Info Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-purple-50">
                        <h3 class="text-lg font-bold mb-4 text-purple-900">User Information</h3>
                        <div class="space-y-2 text-sm">
                            <p><strong>UTHM ID:</strong> {{ auth()->user()->uthm_id }}</p>
                            <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
                            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                            <p><strong>Role:</strong> <span class="px-2 py-1 bg-purple-200 text-purple-800 rounded">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</span></p>
                            @if(auth()->user()->faculty)
                                <p><strong>Faculty:</strong> {{ auth()->user()->faculty }}</p>
                            @endif
                            <p><strong>Status:</strong> 
                                @if(auth()->user()->is_verified)
                                    <span class="text-green-600 font-semibold">Verified</span>
                                @else
                                    <span class="text-yellow-600 font-semibold">Pending Verification</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-blue-50">
                        <h3 class="text-lg font-bold mb-4 text-blue-900">Quick Actions</h3>
                        <ul class="space-y-3">
                            <li>
                                <a href="#" class="block p-2 bg-white rounded hover:bg-blue-100 text-blue-700 transition">
                                    📢 Create Announcement
                                </a>
                            </li>
                            <li>
                                <a href="#" class="block p-2 bg-white rounded hover:bg-blue-100 text-blue-700 transition">
                                    📅 Manage Events
                                </a>
                            </li>
                            <li>
                                <a href="#" class="block p-2 bg-white rounded hover:bg-blue-100 text-blue-700 transition">
                                    👥 Manage Members
                                </a>
                            </li>
                            <li>
                                <a href="#" class="block p-2 bg-white rounded hover:bg-blue-100 text-blue-700 transition">
                                    📊 View Analytics
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-green-50">
                        <h3 class="text-lg font-bold mb-4 text-green-900">Club Statistics</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Total Announcements</span>
                                <span class="text-2xl font-bold text-green-700">0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Active Events</span>
                                <span class="text-2xl font-bold text-green-700">0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Club Members</span>
                                <span class="text-2xl font-bold text-green-700">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-bold mb-4">Recent Activity</h2>
                    <div class="border-t border-gray-200">
                        <div class="py-4 text-center text-gray-500">
                            <p>No recent activity to display.</p>
                            <p class="text-sm mt-2">Start by creating your first club announcement!</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4">Navigation</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('dashboard') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <span class="font-semibold">← Back to Main Dashboard</span>
                        </a>
                        <a href="#" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <span class="font-semibold">Club Settings →</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

