@extends('layouts.community')

@section('title', $group->name)

@section('page-title', 'Community Hub')
@section('breadcrumb', $group->name)

@section('community-content')
    <!-- Back button -->
    <a href="{{ route('student.community-hub') }}" class="inline-flex items-center text-uthm-blue hover:text-blue-700 mb-4 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> Back to Community Hub
    </a>
    
    <!-- Group Header -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-uthm-blue to-blue-700 h-32"></div>
        <div class="px-6 py-4 relative">
            <div class="absolute -top-12 left-6">
                <div class="w-24 h-24 bg-white rounded-xl shadow-lg flex items-center justify-center">
                    <i class="fas fa-users text-uthm-blue text-4xl"></i>
                </div>
            </div>
            <div class="ml-32 mt-4">
                <div class="flex justify-between items-start flex-wrap gap-4">
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $group->name }}</h1>
                            @if($group->isCreator(Auth::id()))
                                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold">
                                    <i class="fas fa-crown mr-1"></i> Creator
                                </span>
                            @elseif($userMember && $userMember->role === 'admin')
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">
                                    <i class="fas fa-shield-alt mr-1"></i> Admin
                                </span>
                            @elseif($userMember)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">
                                    <i class="fas fa-check-circle mr-1"></i> Member
                                </span>
                            @endif
                        </div>
                        <p class="text-gray-600 mt-2">Created by <strong>{{ $group->creator->name }}</strong></p>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-2">
                        @if(!$userMember)
                            <form action="{{ route('student.community-hub.join', $group->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-uthm-blue text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-user-plus mr-2"></i> Join Group
                                </button>
                            </form>
                        @else
                            @if($userMember->role !== 'admin')
                                <form action="{{ route('student.community-hub.leave', $group->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to leave this group?')">
                                    @csrf
                                    <button type="submit" class="bg-red-100 text-red-700 px-6 py-2 rounded-lg hover:bg-red-200 transition">
                                        <i class="fas fa-sign-out-alt mr-2"></i> Leave
                                    </button>
                                </form>
                            @endif
                            
                            @if($group->isCreator(Auth::id()) || ($userMember && $userMember->role === 'admin'))
                                <button type="button" onclick="toggleEditModal()" class="bg-yellow-100 text-yellow-700 px-6 py-2 rounded-lg hover:bg-yellow-200 transition">
                                    <i class="fas fa-edit mr-2"></i> Edit
                                </button>
                                <form action="{{ route('student.community-hub.destroy', $group->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? This will permanently delete the group and all its content.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-100 text-red-700 px-6 py-2 rounded-lg hover:bg-red-200 transition">
                                        <i class="fas fa-trash mr-2"></i> Delete Group
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Group Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <i class="fas fa-users text-uthm-blue text-2xl mb-2"></i>
            <p class="text-2xl font-bold">{{ $group->member_count }}</p>
            <p class="text-gray-600">Members</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <i class="fas fa-lock text-uthm-blue text-2xl mb-2"></i>
            <p class="text-2xl font-bold">{{ ucfirst($group->privacy) }}</p>
            <p class="text-gray-600">Privacy</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <i class="fas fa-tag text-uthm-blue text-2xl mb-2"></i>
            <p class="text-2xl font-bold">{{ $group->category }}</p>
            <p class="text-gray-600">Category</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <i class="fas fa-comments text-uthm-blue text-2xl mb-2"></i>
            <p class="text-2xl font-bold">{{ $posts->count() }}</p>
            <p class="text-gray-600">Posts</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Group Description -->
            <div class="bg-white rounded-xl shadow p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-3">About this group</h3>
                <p class="text-gray-700">{{ $group->description }}</p>
                @if($group->max_members)
                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Capacity</span>
                            <span class="font-medium">{{ $group->member_count }}/{{ $group->max_members }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-uthm-blue h-2 rounded-full" style="width: {{ ($group->member_count / $group->max_members) * 100 }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Posts Section -->
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Posts ({{ $posts->count() }})</h3>
                </div>
                
                <!-- Create Post Form (if member) -->
                @if($userMember && $group->allow_posts)
                    <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
                        <form action="{{ route('student.community-hub.post.create', $group->id) }}" method="POST">
                            @csrf
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-uthm-blue-light rounded-full flex items-center justify-center shrink-0">
                                    <span class="font-bold text-uthm-blue">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                </div>
                                <div class="flex-1">
                                    <textarea name="content" placeholder="Share something with the group..." 
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue focus:border-transparent resize-none" 
                                              rows="3" required></textarea>
                                    <div class="mt-3 text-right">
                                        <button type="submit" class="bg-uthm-blue text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                                            <i class="fas fa-paper-plane mr-2"></i> Post
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
                
                <!-- Posts List -->
                @if($posts->count() > 0)
                    <div class="space-y-4">
                        @foreach($posts as $post)
<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
    <div class="flex items-start justify-between mb-3">
        <div class="flex items-start space-x-3">
            <div class="w-10 h-10 bg-uthm-blue-light rounded-full flex items-center justify-center shrink-0">
                <span class="font-bold text-uthm-blue">{{ strtoupper(substr($post->user->name, 0, 1)) }}</span>
            </div>
            <div>
                <p class="font-medium text-gray-900">{{ $post->user->name }}</p>
                <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
            </div>
        </div>
        
        <!-- Post Actions (existing code) -->
    </div>
    
    <p class="text-gray-700">{{ $post->content }}</p>
    
    <!-- Like Button -->
    <div class="mt-3 flex items-center gap-4 text-gray-500 text-sm border-t pt-3 pb-3">
        <button onclick="likePost({{ $group->id }}, {{ $post->id }})" 
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
                            @if(Auth::id() === $comment->user_id || ($userMember && $userMember->role === 'admin') || $group->isCreator(Auth::id()))
                                <button onclick="deleteComment({{ $group->id }}, {{ $post->id }}, {{ $comment->id }})" 
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
        
        <!-- Comment Form (if member) -->
        @if($userMember)
            <div class="border-t pt-3">
                <form onsubmit="addComment(event, {{ $group->id }}, {{ $post->id }})" class="flex gap-2">
                    <input type="text" placeholder="Add a comment..." 
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue text-sm"
                           id="comment-input-{{ $post->id }}" required>
                    <button type="submit" class="bg-uthm-blue text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-comments text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No posts yet. Be the first to share!</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Pending Join Requests (Admin Only) -->
            @if($userMember && $userMember->role === 'admin' && $pendingRequests->count() > 0)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h3 class="font-bold text-yellow-900 mb-3">
                        <i class="fas fa-hourglass-half mr-2"></i> Pending Requests ({{ $pendingRequests->count() }})
                    </h3>
                    <div class="space-y-3">
                        @foreach($pendingRequests as $request)
                            <div class="bg-white rounded p-3 border border-yellow-100">
                                <p class="font-medium text-sm text-gray-900">{{ $request->user->name }}</p>
                                <p class="text-xs text-gray-500 mb-2">{{ $request->user->uthm_id }}</p>
                                <div class="flex gap-2">
                                    <form action="{{ route('student.community-hub.join-request.approve', [$group->id, $request->id]) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full bg-green-100 text-green-700 text-xs px-2 py-1 rounded hover:bg-green-200">
                                            <i class="fas fa-check mr-1"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('student.community-hub.join-request.reject', [$group->id, $request->id]) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full bg-red-100 text-red-700 text-xs px-2 py-1 rounded hover:bg-red-200">
                                            <i class="fas fa-times mr-1"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- Members List -->
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="font-bold text-gray-900 mb-4">Members ({{ $members->count() }})</h3>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($members as $member)
                        <div class="flex items-start justify-between p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-start space-x-2 flex-1 min-w-0">
                                <div class="w-8 h-8 bg-uthm-blue-light rounded-full flex items-center justify-center shrink-0">
                                    <span class="font-bold text-uthm-blue text-xs">{{ strtoupper(substr($member->user->name, 0, 1)) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-sm text-gray-900 truncate">{{ $member->user->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        @if($group->isCreator($member->user_id))
                                            <span class="text-purple-700 font-bold">Creator</span>
                                        @else
                                            {{ ucfirst($member->role) }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            @if($userMember && $userMember->role === 'admin' && !$group->isCreator($member->user_id) && $member->user_id !== Auth::id())
                                <form action="{{ route('student.community-hub.member.remove', [$group->id, $member->user_id]) }}" method="POST" class="inline" onsubmit="return confirm('Remove this member?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-xs">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <!-- Edit Group Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Edit Group</h2>
                <button onclick="toggleEditModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('student.community-hub.update', $group->id) }}" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Group Name</label>
                    <input type="text" name="name" value="{{ $group->name }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue">{{ $group->description }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Privacy</label>
                    <select name="privacy" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue">
                        <option value="public" {{ $group->privacy === 'public' ? 'selected' : '' }}>Public</option>
                        <option value="private" {{ $group->privacy === 'private' ? 'selected' : '' }}>Private</option>
                        <option value="by_approval" {{ $group->privacy === 'by_approval' ? 'selected' : '' }}>By Approval</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Members</label>
                    <input type="number" name="max_members" value="{{ $group->max_members }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-uthm-blue">
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="toggleEditModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-uthm-blue text-white rounded-lg hover:bg-blue-700">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        function toggleEditModal() {
            document.getElementById('editModal').classList.toggle('hidden');
        }

        function toggleEditPostModal(postId) {
            // TODO: Implement edit post modal with AJAX
            alert('Edit post feature coming soon');
        }

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
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

        function toggleComments(postId) {
            const commentsDiv = document.getElementById(`comments-${postId}`);
            commentsDiv.classList.toggle('hidden');
        }

        async function addComment(event, groupId, postId) {
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
                    
                    // Update comment count
                    const countSpan = document.getElementById(`comments-count-${postId}`);
                    if (countSpan) {
                        countSpan.textContent = parseInt(countSpan.textContent) + 1;
                    }
                    
                    // Get or create comments container
                    const commentsDiv = document.getElementById(`comments-${postId}`);
                    const commentsList = commentsDiv.querySelector('.space-y-3') || createCommentsContainer(postId);
                    
                    // Add new comment to the list
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
                                <button onclick="deleteComment(${groupId}, ${postId}, ${data.comment.id})" 
                                        class="text-red-600 hover:text-red-800 text-xs focus:outline-none">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <p class="text-sm text-gray-700">${data.comment.content}</p>
                        </div>
                    `;
                    
                    if (commentsList) {
                        commentsList.insertAdjacentHTML('afterbegin', commentHTML);
                    }
                } else {
                    alert(data.message || 'Failed to add comment');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to add comment. Please try again.');
            }
        }

        function createCommentsContainer(postId) {
            const commentsDiv = document.getElementById(`comments-${postId}`);
            const container = document.createElement('div');
            container.className = 'space-y-3 mb-4 max-h-64 overflow-y-auto';
            commentsDiv.insertBefore(container, commentsDiv.firstChild);
            return container;
        }

        async function deleteComment(groupId, postId, commentId) {
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
                    // Remove comment from DOM
                    const commentDiv = document.getElementById(`comment-${commentId}`);
                    if (commentDiv) {
                        commentDiv.remove();
                    }
                    
                    // Update comment count
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

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                toggleEditModal();
            });
        @endif
    </script>
@endpush