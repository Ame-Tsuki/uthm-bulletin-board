<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Hub - UTHM Bulletin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'uthm-blue': '#0056a6',
                        'uthm-blue-light': '#e6f0fa',
                        'uthm-green': '#6ea342',
                    }
                }
            }
        }
    </script>
    <style>
        .sidebar-collapsed { width: 80px !important; }
        .sidebar-expanded { width: 280px !important; }
        .content-collapsed { margin-left: 80px !important; }
        .content-expanded { margin-left: 280px !important; }
        .sidebar-transition { transition: width 0.3s ease; }
        .content-transition { transition: margin-left 0.3s ease; }
        .sidebar-text { transition: all 0.3s ease; overflow: hidden; white-space: nowrap; }
        .sidebar-collapsed .sidebar-text { opacity: 0; width: 0; }
        .sidebar-expanded .sidebar-text { opacity: 1; width: auto; }
        @media (max-width: 768px) {
            .sidebar-expanded, .sidebar-collapsed { width: 280px !important; transform: translateX(-100%); }
            .sidebar-expanded.mobile-open { transform: translateX(0); }
            .content-collapsed, .content-expanded { margin-left: 0 !important; }
        }
        .post-card { transition: all 0.3s ease; }
        .post-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .modal { transition: all 0.3s ease; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Mobile Menu Button -->
    <div class="md:hidden fixed top-4 left-4 z-50">
        <button id="mobile-menu-toggle" class="bg-uthm-blue text-white p-2 rounded-lg shadow-lg">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar-collapsed bg-white shadow-lg h-screen fixed left-0 top-0 overflow-y-auto z-40 sidebar-transition">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="bg-green-600 text-white p-2 rounded-lg shrink-0">
                        <i class="fas fa-user-graduate text-lg"></i>
                    </div>
                    <div class="sidebar-text">
                        <h2 class="font-bold text-gray-900">UTHM Bulletin</h2>
                        <p class="text-xs text-gray-500">Student Dashboard</p>
                    </div>
                </div>
                <button id="sidebar-toggle" class="hidden md:block text-gray-500 hover:text-uthm-blue">
                    <svg id="toggle-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                    </svg>
                </button>
            </div>
        </div>

        <a href="#" class="block hover:bg-gray-50">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-uthm-blue-light rounded-full flex items-center justify-center shrink-0">
                        <span class="font-bold text-uthm-blue">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</span>
                    </div>
                    <div class="sidebar-text">
                        <h3 class="font-medium text-gray-900">{{ Auth::user()->name ?? 'User' }}</h3>
                        <p class="text-xs text-gray-500">{{ Auth::user()->uthm_id ?? 'Student' }}</p>
                    </div>
                </div>
            </div>
        </a>

        <nav class="p-4">
            <ul class="space-y-2">
                <li><a href="{{ route('student.dashboard') }}" class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600"><i class="fas fa-home w-5 h-5"></i><span class="sidebar-text ml-3">Dashboard</span></a></li>
                <li><a href="#" class="flex items-center p-3 rounded-lg bg-uthm-blue-light text-uthm-blue"><i class="fas fa-users w-5 h-5"></i><span class="sidebar-text ml-3">Community Hub</span></a></li>
                <li><a href="#" class="flex items-center p-3 rounded-lg hover:bg-uthm-blue-light text-gray-600"><i class="fas fa-cog w-5 h-5"></i><span class="sidebar-text ml-3">Settings</span></a></li>
            </ul>
        </nav>

        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="flex items-center p-3 rounded-lg hover:bg-red-50 text-red-600 w-full">
                    <i class="fas fa-sign-out-alt w-5 h-5"></i>
                    <span class="sidebar-text ml-3">Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="content-collapsed min-h-screen content-transition">
        <nav class="bg-white shadow">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">Community Hub</h1>
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-600">Connect & Engage</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="openCreateGroupModal()" class="bg-uthm-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i> Create Group
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('info'))
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                        {{ session('info') }}
                    </div>
                @endif

                <!-- Welcome Section -->
                <div class="bg-white rounded-xl shadow p-6 mb-8">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Community Hub ✨</h2>
                            <p class="text-gray-600">Connect with fellow students, join clubs, and share your university experience.</p>
                        </div>
                    </div>
                    
                    <!-- Trending Topics -->
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-gray-100 rounded-full text-sm"><i class="fas fa-fire text-red-500"></i> #ExamTips</span>
                        <span class="px-3 py-1 bg-gray-100 rounded-full text-sm"><i class="fas fa-laptop-code"></i> #WebDev</span>
                        <span class="px-3 py-1 bg-gray-100 rounded-full text-sm"><i class="fas fa-futbol"></i> #SportsDay</span>
                        <span class="px-3 py-1 bg-gray-100 rounded-full text-sm"><i class="fas fa-music"></i> #CampusFest</span>
                        <span class="px-3 py-1 bg-gray-100 rounded-full text-sm"><i class="fas fa-book-open"></i> #StudyGroup</span>
                    </div>
                </div>

                <!-- My Groups Section -->
                @if($myGroups->count() > 0)
                <div class="mb-8">
                    <h3 class="text-lg font-bold mb-4">My Groups</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($myGroups as $group)
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="w-10 h-10 bg-uthm-blue-light rounded-lg flex items-center justify-center">
                                    <i class="fas fa-users text-uthm-blue"></i>
                                </div>
                                <span class="text-xs text-gray-500">{{ $group->members_count }} members</span>
                            </div>
                            <h4 class="font-bold">{{ $group->name }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($group->description, 60) }}</p>
                            <div class="mt-3 flex justify-between items-center">
                                <span class="text-xs px-2 py-1 bg-gray-100 rounded">{{ $group->category }}</span>
                                <form action="{{ route('student.community-hub.leave', $group->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-red-600 text-sm hover:text-red-700">Leave</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Pending Requests -->
                @if($pendingRequests->count() > 0)
                <div class="mb-8 bg-yellow-50 rounded-xl p-4">
                    <h3 class="font-bold mb-3">Pending Join Requests</h3>
                    @foreach($pendingRequests as $request)
                    <div class="flex justify-between items-center bg-white rounded-lg p-3 mb-2">
                        <div>
                            <p class="font-medium">{{ $request->group->name }}</p>
                            <p class="text-xs text-gray-500">Requested {{ $request->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="text-yellow-600 text-sm">Waiting for approval</span>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Feed and Groups Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Posts Feed -->
                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-bold mb-4">Latest Posts from Your Groups</h3>
                        
                        @if($posts->count() > 0)
                            @foreach($posts as $post)
                            <div class="bg-white rounded-xl shadow p-5 mb-4 post-card">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="font-bold text-purple-600">{{ strtoupper(substr($post->user->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-bold text-gray-900">{{ $post->user->name }}</h4>
                                                <p class="text-xs text-gray-500">
                                                    in <span class="font-medium text-uthm-blue">{{ $post->group->name }}</span> • {{ $post->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                        <p class="mt-2 text-gray-700">{{ $post->content }}</p>
                                        <div class="mt-3 flex items-center gap-4 text-gray-500 text-sm">
                                            <button onclick="likePost({{ $post->group_id }}, {{ $post->id }})" class="hover:text-red-500 transition">
                                                <i class="far fa-heart mr-1"></i> <span id="likes-{{ $post->id }}">{{ $post->likes_count }}</span> Likes
                                            </button>
                                            <button class="hover:text-uthm-blue transition"><i class="far fa-comment mr-1"></i> Comment</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="bg-white rounded-xl shadow p-8 text-center">
                                <i class="fas fa-newspaper text-gray-400 text-5xl mb-3"></i>
                                <p class="text-gray-500">No posts yet. Join groups to see updates!</p>
                                <a href="#discover" class="inline-block mt-3 text-uthm-blue hover:underline">Discover Groups ↓</a>
                            </div>
                        @endif
                    </div>

                    <!-- Right Column: Discover Groups -->
                    <div>
                        <div class="bg-white rounded-xl shadow p-5 mb-6">
                            <h3 class="font-bold text-lg mb-4">Discover Groups</h3>
                            <div class="space-y-4 max-h-96 overflow-y-auto">
                                @foreach($allGroups as $group)
                                <div class="border-b border-gray-100 pb-3">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-900">{{ $group->name }}</h4>
                                            <p class="text-xs text-gray-500">{{ $group->category }} • {{ $group->members_count }}/{{ $group->max_members ?? '∞' }} members</p>
                                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($group->description, 50) }}</p>
                                        </div>
                                        <form action="{{ route('student.community-hub.join', $group->id) }}" method="POST" class="ml-2">
                                            @csrf
                                            <button type="submit" class="bg-uthm-blue text-white px-3 py-1 rounded text-sm whitespace-nowrap hover:bg-blue-700 transition">
                                                Join
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                {{ $allGroups->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Group Modal -->
    <div id="createGroupModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Create New Group</h3>
                <button onclick="closeCreateGroupModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="{{ route('student.community-hub.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Group Name *</label>
                    <input type="text" name="name" required class="w-full border rounded-lg p-2 focus:outline-none focus:border-uthm-blue">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Description *</label>
                    <textarea name="description" rows="3" required class="w-full border rounded-lg p-2 focus:outline-none focus:border-uthm-blue"></textarea>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Category *</label>
                    <select name="category" required class="w-full border rounded-lg p-2">
                        <option value="Academic">📚 Academic</option>
                        <option value="Sports">⚽ Sports</option>
                        <option value="Arts">🎨 Arts</option>
                        <option value="Technology">💻 Technology</option>
                        <option value="Social">👥 Social</option>
                        <option value="Career">💼 Career</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium mb-1">Privacy *</label>
                    <select name="privacy" required class="w-full border rounded-lg p-2">
                        <option value="public">🌍 Public - Anyone can join</option>
                        <option value="by_approval">🔐 By Approval - Request to join</option>
                        <option value="private">🚪 Private - Invite only</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Max Members (Optional)</label>
                    <input type="number" name="max_members" class="w-full border rounded-lg p-2" placeholder="Leave empty for unlimited">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeCreateGroupModal()" class="flex-1 px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-uthm-blue text-white rounded-lg hover:bg-blue-700">Create Group</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Sidebar functionality
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const toggleIcon = document.getElementById('toggle-icon');
        const isExpanded = localStorage.getItem('sidebarExpanded') === 'true';
        
        if (isExpanded) expandSidebar();
        else collapseSidebar();
        
        document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
            if (sidebar.classList.contains('sidebar-expanded')) collapseSidebar();
            else expandSidebar();
        });
        
        function expandSidebar() {
            sidebar.classList.remove('sidebar-collapsed');
            sidebar.classList.add('sidebar-expanded');
            mainContent.classList.remove('content-collapsed');
            mainContent.classList.add('content-expanded');
            if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
            localStorage.setItem('sidebarExpanded', 'true');
        }
        
        function collapseSidebar() {
            sidebar.classList.remove('sidebar-expanded');
            sidebar.classList.add('sidebar-collapsed');
            mainContent.classList.remove('content-expanded');
            mainContent.classList.add('content-collapsed');
            if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
            localStorage.setItem('sidebarExpanded', 'false');
        }
        
        // Mobile menu
        document.getElementById('mobile-menu-toggle')?.addEventListener('click', () => {
            sidebar.classList.toggle('mobile-open');
        });
        
        // Modal functions
        function openCreateGroupModal() {
            document.getElementById('createGroupModal').classList.remove('hidden');
            document.getElementById('createGroupModal').classList.add('flex');
        }
        
        function closeCreateGroupModal() {
            document.getElementById('createGroupModal').classList.add('hidden');
            document.getElementById('createGroupModal').classList.remove('flex');
        }
        
        // Like post function (AJAX)
        function likePost(groupId, postId) {
            fetch(`/student/community-hub/${groupId}/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const likeSpan = document.getElementById(`likes-${postId}`);
                    if (likeSpan) likeSpan.textContent = data.likes;
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        // Close modal when clicking outside
        document.getElementById('createGroupModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeCreateGroupModal();
        });
    </script>
</body>
</html>