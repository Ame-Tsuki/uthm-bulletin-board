<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - UTHM Bulletin Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" 
                                   class="inline-flex items-center px-5 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-home mr-2"></i>
                                    Back to Dashboard
                                </a>
                    </div>
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">My Profile</h1>
                    </div>
                    <div class="flex items-center">
                        <!-- Empty for alignment -->
                    </div>
                </div>
            </div>
        </nav>

        <!-- Profile Content -->
        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <!-- Profile Header -->
                    <div class="bg-uthm-blue text-white p-8">
                        <div class="flex items-center">
                            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mr-6">
                                <span class="font-bold text-2xl uthm-blue">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                                <p class="text-blue-100">{{ $user->uthm_id ?? 'UTHM Member' }}</p>
                                <p class="text-blue-100 mt-1">
                                    <i class="fas fa-user-tag mr-2"></i>
                                    {{ ucfirst($user->role) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Details -->
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Personal Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-user-circle mr-2 text-uthm-blue"></i>
                                    Personal Information
                                </h3>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-sm text-gray-500">Full Name</p>
                                        <p class="font-medium">{{ $user->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">UTHM ID</p>
                                        <p class="font-medium">{{ $user->uthm_id ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Email</p>
                                        <p class="font-medium">{{ $user->email ?? 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Information -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-cog mr-2 text-uthm-blue"></i>
                                    Account Information
                                </h3>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-sm text-gray-500">Role</p>
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Account Created</p>
                                        <p class="font-medium">{{ $user->created_at->format('F d, Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Last Login</p>
                                        <p class="font-medium">{{ now()->format('F d, Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <div class="flex flex-wrap gap-4">
                                
                                <button class="inline-flex items-center px-5 py-2 bg-uthm-blue text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-edit mr-2"></i>
                                    Edit Profile
                                </button>
                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-5 py-2 bg-red-50 text-red-700 font-medium rounded-lg hover:bg-red-100 transition-colors">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-bullhorn text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Announcements Posted</p>
                                <p class="text-2xl font-bold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex items-center">
                            <div class="bg-green-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-calendar-check text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Events Joined</p>
                                <p class="text-2xl font-bold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-users text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Clubs Joined</p>
                                <p class="text-2xl font-bold text-gray-900">0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>