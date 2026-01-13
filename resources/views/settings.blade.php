<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - UTHM Bulletin Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom color to match your profile page styling */
        .bg-uthm-blue { background-color: #004c99; }
        .text-uthm-blue { color: #004c99; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600">
                            <i class="fas fa-arrow-left mr-2"></i> Back
                        </a>
                    </div>
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">Account Settings</h1>
                    </div>
                    <div class="flex items-center">
                        </div>
                </div>
            </div>
        </nav>

        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">User Preferences</h2>
                    
                    <div class="border-b border-gray-200 py-4 flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-700">Change Password</p>
                            <p class="text-sm text-gray-500">Update your account password for security.</p>
                        </div>
                        <a href="#" class="text-uthm-blue hover:text-blue-700 font-medium">Manage <i class="fas fa-chevron-right ml-1 text-xs"></i></a>
                    </div>

                    <div class="border-b border-gray-200 py-4 flex justify-between items-center">
                        <div>
                            <p class="font-medium text-gray-700">Email Notifications</p>
                            <p class="text-sm text-gray-500">Control which announcement updates you receive.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" value="" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-uthm-blue"></div>
                        </label>
                    </div>

                    <div class="pt-4 flex justify-between items-center">
                        <div>
                            <p class="font-medium text-red-600">Delete Account</p>
                            <p class="text-sm text-gray-500">Permanently remove your account and all data.</p>
                        </div>
                        <button class="px-4 py-2 bg-red-100 text-red-700 font-medium rounded-lg hover:bg-red-200 transition-colors">
                            <i class="fas fa-trash-alt mr-2"></i> Delete
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>