<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - UTHM Bulletin Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('layouts.partials.portal-head')
</head>
<body class="portal-body">
    <div class="min-h-screen">
        <nav class="portal-topbar">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-600 hover:text-uthm-blue hover:bg-uthm-blue-light rounded-xl transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                        </a>
                    </div>
                    <div class="flex items-center">
                        <h1 class="text-lg font-bold text-gray-900">Account Settings</h1>
                    </div>
                    <div class="w-32"></div>
                </div>
            </div>
        </nav>

        <div class="portal-main">
            <div class="portal-container max-w-4xl mx-auto">
                <div class="portal-card p-6 sm:p-8">
                    <div class="mb-6">
                        <h2 class="portal-section-title text-xl">User Preferences</h2>
                        <p class="text-sm text-gray-500 mt-1">Manage your account settings and notifications.</p>
                    </div>

                    <div class="divide-y divide-gray-100">
                        <div class="py-5 flex justify-between items-center gap-4">
                            <div>
                                <p class="font-semibold text-gray-800">Change Password</p>
                                <p class="text-sm text-gray-500 mt-0.5">Update your account password for security.</p>
                            </div>
                            <a href="#" class="text-uthm-blue hover:text-blue-800 font-semibold text-sm whitespace-nowrap">
                                Manage <i class="fas fa-chevron-right ml-1 text-xs"></i>
                            </a>
                        </div>

                        <div class="py-5 flex justify-between items-center gap-4">
                            <div>
                                <p class="font-semibold text-gray-800">Email Notifications</p>
                                <p class="text-sm text-gray-500 mt-0.5">Control which announcement updates you receive.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" value="" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-uthm-blue"></div>
                            </label>
                        </div>

                        <div class="py-5 flex justify-between items-center gap-4">
                            <div>
                                <p class="font-semibold text-red-600">Delete Account</p>
                                <p class="text-sm text-gray-500 mt-0.5">Permanently remove your account and all data.</p>
                            </div>
                            <button type="button" class="px-4 py-2 bg-red-50 text-red-700 font-semibold rounded-xl hover:bg-red-100 transition-colors text-sm whitespace-nowrap">
                                <i class="fas fa-trash-alt mr-2"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 portal-card p-6">
                    <h3 class="portal-section-title text-base mb-4">Quick Links</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a href="{{ route('profile') }}" class="portal-quick-link bg-uthm-blue-light hover:bg-blue-100 flex items-center gap-3 text-left">
                            <i class="fas fa-user text-uthm-blue"></i>
                            <span class="text-sm font-semibold text-gray-800">View Profile</span>
                        </a>
                        <a href="{{ route('announcements.my-announcements') }}" class="portal-quick-link bg-green-50 hover:bg-green-100 flex items-center gap-3 text-left">
                            <i class="fas fa-file-alt text-green-600"></i>
                            <span class="text-sm font-semibold text-gray-800">My Announcements</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
