@extends('layouts.community')

@section('title', 'Community Hub')

@section('page-title', 'Community Hub')
@section('breadcrumb', 'Connect & Engage')

@section('header-actions')
    @php
        $userGroupsCount = App\Models\CommunityGroup::where('created_by', Auth::id())->count();
        $maxGroupsPerUser = 5; // Change this value as needed
        $remainingSlots = max(0, $maxGroupsPerUser - $userGroupsCount);
        $hasReachedLimit = $remainingSlots == 0;
    @endphp
    
    <div class="flex items-center gap-3">
        @if(!$hasReachedLimit)
            <button onclick="openCreateGroupModal()" class="bg-uthm-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-plus-circle mr-2"></i> Create Group
            </button>
        @else
            <button disabled class="bg-gray-400 text-white px-4 py-2 rounded-lg cursor-not-allowed flex items-center opacity-50" title="You have reached the maximum group limit">
                <i class="fas fa-plus-circle mr-2"></i> Create Group
            </button>
        @endif
        
        <!-- Group Creation Limit Indicator -->
        <div class="relative group">
            <div class="bg-gray-100 rounded-lg px-3 py-2 flex items-center gap-2 cursor-help">
                <i class="fas fa-chart-line text-uthm-blue text-sm"></i>
                <span class="text-sm font-medium {{ $hasReachedLimit ? 'text-red-600' : 'text-gray-700' }}">
                    {{ $userGroupsCount }}/{{ $maxGroupsPerUser }}
                </span>
                <i class="fas fa-info-circle text-gray-400 text-xs"></i>
            </div>
            
            <!-- Tooltip -->
            <div class="absolute right-0 mt-2 w-64 bg-gray-800 text-white text-sm rounded-lg shadow-lg p-3 hidden group-hover:block z-10">
                <div class="absolute -top-2 right-3 w-3 h-3 bg-gray-800 transform rotate-45"></div>
                <p class="mb-1">
                    <i class="fas fa-users mr-1"></i> 
                    <strong>Group Creation Limit</strong>
                </p>
                <p class="text-xs text-gray-300">
                    You have created <strong class="text-white">{{ $userGroupsCount }}</strong> out of <strong class="text-white">{{ $maxGroupsPerUser }}</strong> allowed groups.
                </p>
                @if($hasReachedLimit)
                    <p class="text-xs text-red-300 mt-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i> 
                        You have reached the maximum limit. Delete some groups to create new ones.
                    </p>
                @else
                    <p class="text-xs text-green-300 mt-2">
                        <i class="fas fa-check-circle mr-1"></i> 
                        You can create {{ $remainingSlots }} more {{ Str::plural('group', $remainingSlots) }}.
                    </p>
                @endif
            </div>
        </div>
    </div>
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
        
        <!-- Show limit warning if reached -->
        @if($hasReachedLimit)
            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    <span class="text-sm text-yellow-800">
                        You have reached the maximum limit of {{ $maxGroupsPerUser }} groups. 
                        Please delete some groups to create new ones.
                    </span>
                </div>
            </div>
        @endif
    </div>

    <!-- My Groups Section -->
    @if($myGroups->count() > 0)
    <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">My Groups</h3>
            <span class="text-xs text-gray-500">{{ $myGroups->count() }} total groups</span>
        </div>
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
                            
                            <!-- Like and Comment Buttons -->
                            <div class="mt-3 flex items-center gap-4 text-gray-500 text-sm border-t pt-3">
                                <button onclick="likePost({{ $post->group_id }}, {{ $post->id }})" 
                                        class="hover:text-red-500 transition flex items-center gap-1 focus:outline-none">
                                    <i id="heart-{{ $post->id }}" 
                                       class="{{ $post->is_liked ? 'fas text-red-500' : 'far' }} fa-heart"></i> 
                                    <span id="likes-{{ $post->id }}">{{ $post->likes_count }}</span> Likes
                                </button>
                                <button onclick="toggleComments({{ $post->id }})" class="hover:text-uthm-blue transition flex items-center gap-1 focus:outline-none">
                                    <i class="fas fa-comment"></i>
                                    <span id="comments-count-{{ $post->id }}">{{ $post->comments->count() }}</span> Comments
                                </button>
                            </div>
                            
                            <!-- Comments Section -->
                            <div id="comments-{{ $post->id }}" class="hidden border-t pt-3 mt-3">
                                <!-- Display Comments -->
                                @if($post->comments->count() > 0)
                                    <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                                        @foreach($post->comments as $comment)
                                            <div id="comment-{{ $comment->id }}" class="bg-gray-50 rounded p-3">
                                                <div class="flex items-start justify-between mb-1">
                                                    <div class="flex items-start space-x-2">
                                                        <div class="w-8 h-8 bg-uthm-blue-light rounded-full flex items-center justify-center shrink-0">
                                                            <span class="font-bold text-uthm-blue text-xs">{{ strtoupper(substr($comment->user->name, 0, 1)) }}</span>
                                                        </div>
                                                        <div>
                                                            <p class="font-medium text-sm text-gray-900">{{ $comment->user->name }}</p>
                                                            <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                                        </div>
                                                    </div>
                                                    @if(Auth::id() === $comment->user_id)
                                                        <button onclick="deleteCommentFromFeed({{ $post->group_id }}, {{ $post->id }}, {{ $comment->id }})" 
                                                                class="text-red-600 hover:text-red-800 text-xs focus:outline-none">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-700">{{ $comment->content }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <!-- Comment Form -->
                                <div class="border-t pt-3">
                                    <form onsubmit="addCommentToFeed(event, {{ $post->group_id }}, {{ $post->id }})" class="flex gap-2">
                                        <input type="text" placeholder="Add a comment..." 
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue text-sm"
                                               id="comment-input-{{ $post->id }}" required>
                                        <button type="submit" class="bg-uthm-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </form>
                                </div>
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
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-lg">Discover Groups</h3>
                    <form action="{{ route('student.community-hub') }}" method="GET" class="flex items-center gap-2">
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search groups by name, category, or description..."
                               class="px-3 py-2 border rounded-lg text-sm w-56 focus:ring-2 focus:ring-uthm-blue" />
                        <button type="submit" class="bg-uthm-blue text-white px-3 py-2 rounded-lg text-sm hover:bg-blue-700">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('q'))
                            <a href="{{ route('student.community-hub') }}" class="ml-2 text-sm text-gray-500 hover:underline">Clear</a>
                        @endif
                    </form>
                </div>

                @if(request('q'))
                    <p class="text-sm text-gray-600 mb-3">Showing <strong>{{ $allGroups->total() }}</strong> results for "<strong>{{ request('q') }}</strong>"</p>
                @endif

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
                    {{ $allGroups->appends(request()->only('q'))->links() }}
                </div>
            </div>
            
            <!-- Group Creation Info Card -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow p-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-uthm-blue rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-chart-line text-white text-sm"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Group Creation Limit</h4>
                        <div class="mt-2">
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>Used slots</span>
                                <span>{{ $userGroupsCount }}/{{ $maxGroupsPerUser }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-uthm-blue h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ ($userGroupsCount / $maxGroupsPerUser) * 100 }}%"></div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">
                            @if($hasReachedLimit)
                                <span class="text-red-600">
                                    <i class="fas fa-ban mr-1"></i> Limit reached. Delete a group to create more.
                                </span>
                            @else
                                <span class="text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i> 
                                    You can create {{ $remainingSlots }} more {{ Str::plural('group', $remainingSlots) }}.
                                </span>
                            @endif
                        </p>
                    </div>
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
            
            @php
                $remainingSlotsModal = max(0, $maxGroupsPerUser - $userGroupsCount);
            @endphp
            
            @if($remainingSlotsModal > 0)
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-info-circle text-green-600"></i>
                        <span class="text-sm text-green-700">
                            You have <strong>{{ $remainingSlotsModal }}</strong> remaining group {{ Str::plural('slot', $remainingSlotsModal) }}.
                        </span>
                    </div>
                </div>
            @endif
            
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
            @php
                $hasReachedLimitCheck = $hasReachedLimit ?? false;
            @endphp
            
            @if($hasReachedLimitCheck)
                alert('You have reached the maximum limit of {{ $maxGroupsPerUser }} groups. Please delete some groups to create new ones.');
                return;
            @endif
            
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
                    const likeSpan = document.getElementById(`likes-${postId}`);
                    if (likeSpan) {
                        likeSpan.textContent = data.likes;
                    }
                    
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
        
        // Toggle comments visibility
        function toggleComments(postId) {
            const commentsDiv = document.getElementById(`comments-${postId}`);
            if (commentsDiv) {
                commentsDiv.classList.toggle('hidden');
            }
        }
        
        // Add comment to feed
        async function addCommentToFeed(event, groupId, postId) {
            event.preventDefault();
            
            const input = document.getElementById(`comment-input-${postId}`);
            const content = input.value.trim();
            
            if (!content) {
                alert('Please enter a comment');
                return;
            }
            
            try {
                const response = await fetch(`/student/community-hub/${groupId}/posts/${postId}/comments`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ content: content })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    input.value = '';
                    
                    const countSpan = document.getElementById(`comments-count-${postId}`);
                    if (countSpan) {
                        countSpan.textContent = parseInt(countSpan.textContent) + 1;
                    }
                    
                    const commentsDiv = document.getElementById(`comments-${postId}`);
                    let commentsList = commentsDiv.querySelector('.space-y-3');
                    
                    if (!commentsList) {
                        commentsList = document.createElement('div');
                        commentsList.className = 'space-y-3 mb-4 max-h-64 overflow-y-auto';
                        commentsDiv.insertBefore(commentsList, commentsDiv.firstChild);
                    }
                    
                    const commentHTML = `
                        <div id="comment-${data.comment.id}" class="bg-gray-50 rounded p-3">
                            <div class="flex items-start justify-between mb-1">
                                <div class="flex items-start space-x-2">
                                    <div class="w-8 h-8 bg-uthm-blue-light rounded-full flex items-center justify-center shrink-0">
                                        <span class="font-bold text-uthm-blue text-xs">${data.comment.user_initial}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-sm text-gray-900">${data.comment.user_name}</p>
                                        <p class="text-xs text-gray-500">just now</p>
                                    </div>
                                </div>
                                <button onclick="deleteCommentFromFeed(${groupId}, ${postId}, ${data.comment.id})" 
                                        class="text-red-600 hover:text-red-800 text-xs focus:outline-none">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <p class="text-sm text-gray-700">${data.comment.content}</p>
                        </div>
                    `;
                    
                    commentsList.insertAdjacentHTML('afterbegin', commentHTML);
                    commentsDiv.classList.remove('hidden');
                } else {
                    alert(data.message || 'Failed to add comment');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to add comment. Please try again.');
            }
        }
        
        // Delete comment from feed
        async function deleteCommentFromFeed(groupId, postId, commentId) {
            if (!confirm('Delete this comment?')) return;
            
            try {
                const response = await fetch(`/student/community-hub/${groupId}/posts/${postId}/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const commentDiv = document.getElementById(`comment-${commentId}`);
                    if (commentDiv) {
                        commentDiv.remove();
                    }
                    
                    const countSpan = document.getElementById(`comments-count-${postId}`);
                    if (countSpan) {
                        countSpan.textContent = Math.max(0, parseInt(countSpan.textContent) - 1);
                    }
                } else {
                    alert(data.message || 'Failed to delete comment');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to delete comment. Please try again.');
            }
        }
        
        // Close modal when clicking outside
        document.getElementById('createGroupModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeCreateGroupModal();
        });
    </script>
@endpush