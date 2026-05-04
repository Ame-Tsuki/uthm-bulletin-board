@extends('layouts.community')

@section('title', 'Community Hub')

@section('page-title', 'Community Hub')
@section('breadcrumb', 'Connect & Engage')

@section('header-actions')
    <button onclick="openCreateGroupModal()" class="bg-uthm-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
        <i class="fas fa-plus-circle mr-2"></i> Create Group
    </button>
@endsection

@section('community-content')
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
        <a href="{{ route('student.community-hub.show', $group->id) }}" class="block group">
            <div class="bg-white rounded-lg shadow p-4 h-full flex flex-col hover:shadow-md transition-shadow duration-200">
                <!-- Group Header -->
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-uthm-blue-light rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-uthm-blue"></i>
                    </div>
                    <span class="text-xs text-gray-500">{{ $group->members_count }} members</span>
                </div>
                
                <!-- Group Name -->
                <h4 class="font-bold text-gray-900 group-hover:text-uthm-blue transition-colors">
                    {{ $group->name }}
                </h4>
                
                <!-- Description -->
                <p class="text-sm text-gray-600 mt-1 flex-grow line-clamp-2">
                    {{ Str::limit($group->description, 80) }}
                </p>
                
                <!-- Footer with Category and Leave Button -->
                <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-xs px-2 py-1 bg-gray-100 rounded-full font-medium">
                        {{ $group->category }}
                    </span>
                    <form action="{{ route('student.community-hub.leave', $group->id) }}" method="POST" onclick="event.stopPropagation()">
                        @csrf
                        <button type="submit" class="text-red-600 text-sm hover:text-red-700 font-medium transition-colors">
                            Leave
                        </button>
                    </form>
                </div>
            </div>
        </a>
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
                <button onclick="likePost({{ $post->group_id }}, {{ $post->id }})" 
                        class="hover:text-red-500 transition flex items-center gap-1 focus:outline-none">
                    <i id="heart-{{ $post->id }}" 
                       class="{{ $post->is_liked ? 'fas text-red-500' : 'far' }} fa-heart"></i> 
                    <span id="likes-{{ $post->id }}">{{ $post->likes_count }}</span> Likes
                </button>
                <button class="hover:text-uthm-blue transition flex items-center gap-1">
                    <i class="far fa-comment mr-1"></i> Comment
                </button>
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
            <div class="bg-white rounded-xl shadow p-5 mb-6" id="discover">
                <h3 class="font-bold text-lg mb-4">Discover Groups</h3>
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @foreach($allGroups as $group)
<div class="border-b border-gray-100 pb-3">
    <div class="flex justify-between items-start">
        <div class="flex-1">
            <a href="{{ route('student.community-hub.show', $group->id) }}" class="hover:text-uthm-blue">
                <h4 class="font-bold text-gray-900">{{ $group->name }}</h4>
            </a>
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
@endsection

@push('modals')
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
@endpush

@push('scripts')
    <script>
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
async function likePost(groupId, postId) {
    try {
        const response = await fetch(`/student/community-hub/${groupId}/posts/${postId}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update like count
            const likeSpan = document.getElementById(`likes-${postId}`);
            if (likeSpan) {
                likeSpan.textContent = data.likes;
            }
            
            // Toggle heart icon
            const heartIcon = document.getElementById(`heart-${postId}`);
            if (heartIcon) {
                if (data.liked) {
                    heartIcon.classList.remove('far');
                    heartIcon.classList.add('fas', 'text-red-500');
                } else {
                    heartIcon.classList.remove('fas', 'text-red-500');
                    heartIcon.classList.add('far');
                }
            }
        } else {
            alert(data.message || 'Failed to like post');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to process like. Please try again.');
    }
}
        
        // Close modal when clicking outside
        document.getElementById('createGroupModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeCreateGroupModal();
        });
    </script>
@endpush