<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - UTHM Bulletin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-600 text-white p-2 rounded-lg">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">UTHM Staff Dashboard</h1>
                            <p class="text-sm text-gray-600">Welcome, {{ Auth::user()->name }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline">Main Dashboard</a>
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
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-bullhorn text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">24</h3>
                            <p class="text-gray-600">Announcements Posted</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-calendar-check text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">8</h3>
                            <p class="text-gray-600">Upcoming Events</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-comments text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold">156</h3>
                            <p class="text-gray-600">Total Comments</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <h2 class="text-2xl font-bold mb-6">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="#" class="bg-blue-50 p-4 rounded-lg hover:bg-blue-100 transition border border-blue-200">
                        <i class="fas fa-plus-circle text-blue-600 text-2xl mb-2"></i>
                        <h3 class="font-bold">Create Announcement</h3>
                        <p class="text-sm text-gray-600">Post new university notice</p>
                    </a>
                    <a href="#" class="bg-green-50 p-4 rounded-lg hover:bg-green-100 transition border border-green-200">
                        <i class="fas fa-calendar-plus text-green-600 text-2xl mb-2"></i>
                        <h3 class="font-bold">Schedule Event</h3>
                        <p class="text-sm text-gray-600">Add upcoming events</p>
                    </a>
                    <a href="#" class="bg-yellow-50 p-4 rounded-lg hover:bg-yellow-100 transition border border-yellow-200">
                        <i class="fas fa-chart-bar text-yellow-600 text-2xl mb-2"></i>
                        <h3 class="font-bold">View Analytics</h3>
                        <p class="text-sm text-gray-600">Check announcement reach</p>
                    </a>
                    <a href="#" class="bg-purple-50 p-4 rounded-lg hover:bg-purple-100 transition border border-purple-200">
                        <i class="fas fa-users text-purple-600 text-2xl mb-2"></i>
                        <h3 class="font-bold">Manage Users</h3>
                        <p class="text-sm text-gray-600">View student activities</p>
                    </a>
                </div>
            </div>

            <!-- Recent Announcements -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Recent Announcements</h2>
                    <a href="#" class="text-blue-600 hover:underline">View All</a>
                </div>
                
                <div class="space-y-4">
                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                        <div class="flex justify-between">
                            <h3 class="font-bold">Mid-Term Examination Schedule</h3>
                            <span class="text-sm text-gray-500">2 hours ago</span>
                        </div>
                        <p class="text-gray-600">Faculty of Computer Science exam dates released</p>
                        <div class="flex items-center mt-2">
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2">FCSIT</span>
                            <span class="text-sm text-gray-500">245 views • 12 comments</span>
                        </div>
                    </div>
                    
                    <div class="border-l-4 border-green-500 pl-4 py-2">
                        <div class="flex justify-between">
                            <h3 class="font-bold">Career Fair 2024</h3>
                            <span class="text-sm text-gray-500">1 day ago</span>
                        </div>
                        <p class="text-gray-600">Annual career fair registration now open</p>
                        <div class="flex items-center mt-2">
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded mr-2">Event</span>
                            <span class="text-sm text-gray-500">512 views • 34 comments</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>