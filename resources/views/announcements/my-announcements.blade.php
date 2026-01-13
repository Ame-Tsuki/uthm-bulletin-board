<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Announcements - UTHM Bulletin Board</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom colors */
        .uthm-blue { color: #0056a6; }
        .bg-uthm-blue { background-color: #0056a6; }
        .bg-uthm-blue-light { background-color: #e6f0fa; }
        
        /* Badge styles */
        .badge-official {
            background-color: #dcfce7;
            color: #166534;
        }
        .badge-unofficial {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-urgent {
            background-color: #fee2e2;
            color: #dc2626;
        }
        .badge-important {
            background-color: #fef3c7;
            color: #d97706;
        }
        .badge-academic {
            background-color: #dbeafe;
            color: #1d4ed8;
        }
        .badge-events {
            background-color: #f3e8ff;
            color: #7c3aed;
        }
        .badge-general {
            background-color: #f0f9ff;
            color: #0369a1;
        }
        
        /* Status badges */
        .status-published {
            background-color: #10b981;
            color: white;
        }
        .status-pending {
            background-color: #f59e0b;
            color: white;
        }
        .status-draft {
            background-color: #6b7280;
            color: white;
        }
        
        /* Action buttons */
        .btn-edit {
            background-color: #3b82f6;
            color: white;
        }
        .btn-delete {
            background-color: #ef4444;
            color: white;
        }
        .btn-view {
            background-color: #10b981;
            color: white;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Top Navigation Bar -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('announcements.index') }}" class="flex items-center mr-8">
                            <i class="fas fa-arrow-left text-gray-600 mr-2"></i>
                            <span class="text-gray-700">Back to Announcements</span>
                        </a>
                        <h1 class="text-xl font-bold text-gray-900">My Announcements</h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('announcements.create') }}" 
                           class="bg-uthm-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            New Announcement
                        </a>
                        
                        <div class="relative">
                            <button id="user-menu-button" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100">
                                <div class="w-8 h-8 bg-uthm-blue-light rounded-full flex items-center justify-center">
                                    <span class="font-bold uthm-blue">{{ strtoupper(substr($user?->name ?? 'G', 0, 1)) }}</span>
                                </div>
                                <span class="hidden md:block">{{ $user?->name ?? 'User' }}</span>
                                <i class="fas fa-chevron-down text-gray-500"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Manage Your Announcements</h2>
                    <p class="mt-2 text-gray-600">View, edit, and manage all announcements you've created</p>
                    
                    <!-- Stats -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-3 rounded-lg mr-4">
                                    <i class="fas fa-bullhorn text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Total Announcements</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $announcements->total() }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-3 rounded-lg mr-4">
                                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Official Announcements</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $announcements->where('is_official', true)->count() }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-3 rounded-lg mr-4">
                                    <i class="fas fa-eye text-purple-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Total Views</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $totalViews ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="mb-6 bg-white rounded-lg shadow p-4">
                    <div class="flex space-x-4">
                        <a href="{{ route('announcements.my-announcements') }}?status=all" 
                           class="px-4 py-2 rounded-lg {{ request('status', 'all') == 'all' ? 'bg-uthm-blue text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            All
                        </a>
                        <a href="{{ route('announcements.my-announcements') }}?status=published" 
                           class="px-4 py-2 rounded-lg {{ request('status') == 'published' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            <i class="fas fa-check-circle mr-2"></i>Published
                        </a>
                        <a href="{{ route('announcements.my-announcements') }}?status=draft" 
                           class="px-4 py-2 rounded-lg {{ request('status') == 'draft' ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            <i class="fas fa-edit mr-2"></i>Drafts
                        </a>
                    </div>
                </div>

                <!-- Announcements List -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    @if($announcements->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Title
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Category
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Created
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($announcements as $announcement)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $announcement->title }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            @if($announcement->is_official)
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs badge-official">
                                                                    <i class="fas fa-check-circle mr-1"></i> Official
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs badge-unofficial">
                                                                    <i class="fas fa-users mr-1"></i> Unofficial
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($announcement->status == 'published')
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full status-published">
                                                        <i class="fas fa-check mr-1"></i> Published
                                                    </span>
                                                @elseif($announcement->status == 'draft')
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full status-draft">
                                                        <i class="fas fa-edit mr-1"></i> Draft
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full status-pending">
                                                        <i class="fas fa-clock mr-1"></i> Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full badge-{{ $announcement->category }}">
                                                    {{ ucfirst($announcement->category) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $announcement->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('announcements.show', $announcement) }}" 
                                                       class="inline-flex items-center px-3 py-1 border border-transparent text-xs rounded btn-view hover:bg-green-700">
                                                        <i class="fas fa-eye mr-1"></i> View
                                                    </a>
                                                    <a href="{{ route('announcements.edit', $announcement) }}" 
                                                       class="inline-flex items-center px-3 py-1 border border-transparent text-xs rounded btn-edit hover:bg-blue-700">
                                                        <i class="fas fa-edit mr-1"></i> Edit
                                                    </a>
                                                    <form action="{{ route('announcements.destroy', $announcement) }}" 
                                                          method="POST" 
                                                          class="inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this announcement?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="inline-flex items-center px-3 py-1 border border-transparent text-xs rounded btn-delete hover:bg-red-700">
                                                            <i class="fas fa-trash mr-1"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($announcements->hasPages())
                            <div class="px-6 py-4 border-t border-gray-200">
                                {{ $announcements->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <div class="inline-block p-6 bg-gray-100 rounded-full mb-4">
                                <i class="fas fa-file-alt text-gray-400 text-4xl"></i>
                            </div>
                            <h3 class="text-xl font-medium text-gray-900 mb-2">No announcements yet</h3>
                            <p class="text-gray-600 mb-6">
                                @if(request('status') == 'published')
                                    You haven't published any announcements yet.
                                @elseif(request('status') == 'draft')
                                    You don't have any draft announcements.
                                @else
                                    You haven't created any announcements yet.
                                @endif
                            </p>
                            <a href="{{ route('announcements.create') }}" 
                               class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-plus-circle mr-2 text-lg"></i>
                                Create Your First Announcement
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h3 class="font-bold text-gray-900 text-lg mb-2">Need help?</h3>
                        <p class="text-gray-600 text-sm mb-4">Learn how to create effective announcements that reach your audience.</p>
                        <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                            <i class="fas fa-book mr-2"></i>
                            View Guidelines
                        </a>
                    </div>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                        <h3 class="font-bold text-gray-900 text-lg mb-2">Make it official</h3>
                        <p class="text-gray-600 text-sm mb-4">Contact admin/staff to verify your announcement and make it official.</p>
                        <a href="mailto:admin@uthm.edu.my" class="inline-flex items-center text-green-600 hover:text-green-800">
                            <i class="fas fa-envelope mr-2"></i>
                            Request Verification
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-6 mt-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-500 text-sm">
                    <p>UTHM Digital Bulletin Board &copy; {{ date('Y') }}</p>
                    <p class="mt-1">Manage your announcements and stay connected with the community</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Simple confirmation for delete actions
            document.querySelectorAll('form[onsubmit]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Are you sure you want to delete this announcement?')) {
                        e.preventDefault();
                    }
                });
            });
            
            // Status filter functionality
            const statusTabs = document.querySelectorAll('a[href*="status="]');
            statusTabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    // Remove active class from all tabs
                    statusTabs.forEach(t => {
                        t.classList.remove('bg-uthm-blue', 'text-white', 'bg-green-600', 'bg-gray-600');
                        t.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    });
                    
                    // Add appropriate active class
                    const status = this.getAttribute('href').includes('status=published') ? 'published' :
                                  this.getAttribute('href').includes('status=draft') ? 'draft' : 'all';
                    
                    if (status === 'published') {
                        this.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        this.classList.add('bg-green-600', 'text-white');
                    } else if (status === 'draft') {
                        this.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        this.classList.add('bg-gray-600', 'text-white');
                    } else {
                        this.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        this.classList.add('bg-uthm-blue', 'text-white');
                    }
                });
            });
        });
    </script>
</body>
</html>