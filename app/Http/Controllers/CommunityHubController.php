<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommunityGroup;
use App\Models\GroupMember;
use App\Models\GroupPost;
use App\Models\GroupPostComment;
use App\Models\GroupJoinRequest;
use App\Models\User; // <-- Added for Notification targeting
use App\Notifications\CommunityNotification; // <-- Added for notification dispatching
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification; // <-- Added for multi-user notifications
use Illuminate\Validation\Rule;

class CommunityHubController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Support searching groups via ?q=term (name, category, description)
        $searchTerm = trim($request->input('q', ''));

        $groupsQuery = CommunityGroup::withCount('members');

        if (!empty($searchTerm)) {
            $groupsQuery->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('category', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Get all groups with member count (paginated)
        $allGroups = $groupsQuery->orderBy('created_at', 'desc')
            ->paginate(9)
            ->withQueryString();
        
        // Get user's joined groups
        $myGroups = CommunityGroup::whereHas('members', function($q) use ($user) {
            $q->where('user_id', $user->id)->where('status', 'approved');
        })->withCount('members')->get();
        
        // Get user's pending join requests
        $pendingRequests = GroupJoinRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('group')
            ->get();

        // Get all posts from groups user is member of
        $groupIds = GroupMember::where('user_id', $user->id)
            ->where('status', 'approved')
            ->pluck('group_id');
            
         $posts = GroupPost::with(['user', 'group', 'likes'])
            ->whereIn('group_id', $groupIds)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->map(function($post) use ($user) {
                $post->is_liked = $post->isLikedBy($user->id);
                return $post;
            });
        
        // Fetch recommended groups based on user interests
        $recommendedGroups = collect();
        if ($user->interests && count($user->interests) > 0) {
            $recQuery = CommunityGroup::withCount('members')
                ->where('created_by', '!=', $user->id)
                ->whereNotExists(function($query) use ($user) {
                    $query->select(DB::raw(1))
                        ->from('group_members')
                        ->whereColumn('group_members.group_id', 'community_groups.id')
                        ->where('group_members.user_id', $user->id)
                        ->where('group_members.status', 'approved');
                });
                
            $recQuery->where(function($q) use ($user) {
                $q->whereIn('category', $user->interests);
                foreach ($user->interests as $interest) {
                    $q->orWhere('tags', 'like', '%"' . $interest . '"%');
                }
            });
            
            $recommendedGroups = $recQuery->take(6)->get();
        }
        
        return view('student.community-hub', compact('allGroups', 'myGroups', 'pendingRequests', 'posts', 'recommendedGroups'));
    }
    
    public function create()
    {
        return view('student.community-hub-create');
    }
    
    public function store(Request $request)
{
    // Manual validation check
    $existingGroup = CommunityGroup::where('name', $request->name)->first();
    
    if ($existingGroup) {
        return redirect()->back()
            ->with('error', 'A group named "' . $request->name . '" already exists. Please choose a different name.')
            ->withInput();
    }

    $user = auth()->user();
    
    // Define the maximum number of groups a user can create
    $maxGroupsPerUser = 5; // Change this number as needed
    
    // Check how many groups the user has already created
    $userGroupsCount = CommunityGroup::where('created_by', $user->id)->count();
    
    if ($userGroupsCount >= $maxGroupsPerUser) {
        return back()->with('error', "You have reached the maximum limit of {$maxGroupsPerUser} groups. You cannot create more groups.")
                     ->withInput();
    }

    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:100',
            Rule::unique('community_groups', 'name')
        ],
        'description' => 'required|string|max:1000',
        'category' => 'required|string',
        'privacy' => 'required|in:public,by_approval',
        'max_members' => 'nullable|integer|min:1|max:10000',
        'tags' => 'nullable|array',
    ], [
        'name.unique' => 'A group with this name already exists. Please choose a different name.',
        'name.required' => 'Group name is required.',
        'name.max' => 'Group name cannot exceed 100 characters.',
        'description.required' => 'Description is required.',
        'category.required' => 'Category is required.',
        'privacy.required' => 'Privacy setting is required.',
    ]);
    
    DB::beginTransaction();
    
    try {
        $group = CommunityGroup::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'privacy' => $validated['privacy'],
            'max_members' => $validated['max_members'] ?? null,
            'created_by' => auth()->id(),
            'member_count' => 1,
            'allow_posts' => true,
            'allow_events' => true,
            'require_approval' => $validated['privacy'] === 'by_approval',
            'tags' => $request->input('tags', []),
        ]);
        
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => auth()->id(),
            'role' => 'admin',
            'status' => 'approved',
            'joined_at' => now()
        ]);
        
        DB::commit();
        
        return redirect()->route($this->getRoutePrefix() . '.community-hub.show', $group->id)
            ->with('success', 'Group "' . $group->name . '" created successfully!');
            
    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Group creation failed: ' . $e->getMessage());
        
        // IMPORTANT: Stay on the create page with error
        return redirect()->back()
            ->with('error', 'Failed to create group. Please try again.')
            ->withInput();
    }
}
    
    public function checkGroupName(Request $request)
{
    $request->validate([
        'name' => 'required|string|min:3|max:255'
    ]);
    
    // FIXED: Use CommunityGroup instead of Group
    $exists = CommunityGroup::where('name', $request->name)->exists();
    
    if ($exists) {
        return response()->json([
            'exists' => true,
            'message' => 'This group name is already taken. Please choose another name.'
        ]);
    }
    
    return response()->json([
        'exists' => false,
        'message' => 'Group name is available!'
    ]);
}
    
    public function join($id)
    {
        $group = CommunityGroup::findOrFail($id);
        $user = auth()->user();
        
        // Check if already a member
        $existing = GroupMember::where('group_id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if ($existing) {
            if ($existing->status === 'approved') {
                return back()->with('error', 'You are already a member of this group.');
            }
            return back()->with('error', 'You already have a pending request for this group.');
        }
        
        // Check member limit
        if ($group->max_members && $group->member_count >= $group->max_members) {
            return back()->with('error', 'This group has reached its maximum member limit.');
        }
        
        DB::beginTransaction();
        
        try {
            if ($group->privacy === 'public') {
                // Public group - join immediately
                GroupMember::create([
                    'group_id' => $id,
                    'user_id' => $user->id,
                    'role' => 'member',
                    'status' => 'approved',
                    'joined_at' => now()
                ]);
                $group->increment('member_count');
                DB::commit();
                return redirect()->route($this->getRoutePrefix() . '.community-hub.show', $id)
                    ->with('success', 'You joined "' . $group->name . '"!');
            } else {
                // Private or approval required - send request
                GroupJoinRequest::create([
                    'group_id' => $id,
                    'user_id' => $user->id,
                    'status' => 'pending'
                ]);
                DB::commit();

                // 🔔 NOTIFICATION 1: Join Request Sent -> Alert Group Admins & Creator
                $groupAdmins = User::whereHas('groupMemberships', function($q) use ($id) {
                    $q->where('group_id', $id)->where('role', 'admin')->where('status', 'approved');
                })->get();

                $title = "📥 New Group Join Request";
                $message = "{$user->name} has requested to join your group '{$group->name}'.";
                $url = route('community-hub.view', $group->id);

                Notification::send($groupAdmins, new CommunityNotification($title, $message, $url));

                return back()->with('info', 'Join request sent to "' . $group->name . '". Admin will review it.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to join group. Please try again.');
        }
    }
    
    public function leave($id)
    {
        $member = GroupMember::where('group_id', $id)
            ->where('user_id', auth()->id())
            ->first();
        
        if (!$member) {
            return back()->with('error', 'You are not a member of this group.');
        }
        
        // Check if user is the creator
        $group = CommunityGroup::findOrFail($id);
        if ($group->created_by === auth()->id()) {
            return back()->with('error', 'Group creators cannot leave. Transfer ownership or delete the group first.');
        }
        
        if ($member->role === 'admin') {
            return back()->with('error', 'Group admins cannot leave. Ask the creator to remove you first.');
        }
        
        DB::beginTransaction();
        
        try {
            $member->delete();
            $group->decrement('member_count');
            DB::commit();
            return redirect()->route($this->getRoutePrefix() . '.community-hub')->with('success', 'You have left the group.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to leave group.');
        }
    }

    /**
     * Show group details with posts and members
     */
    public function show($id)
    {
        $group = CommunityGroup::with(['creator', 'members.user', 'posts.user', 'posts.likes'])
            ->findOrFail($id);
        $user = auth()->user();
        
        // Check if user is a member
        $userMember = GroupMember::where('group_id', $id)
            ->where('user_id', $user->id)
            ->first();
        
        // Get posts with like status
        $posts = GroupPost::where('group_id', $id)
            ->with(['user', 'group', 'likes', 'comments.user'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($post) use ($user) {
                $post->is_liked = $post->isLikedBy($user->id);
                return $post;
            });
        
        // Get pending join requests (for creator and admins)
        $pendingRequests = collect();
        $isCreator = $group->created_by === $user->id;
        if ($userMember && ($userMember->role === 'admin' || $isCreator) && $userMember->status === 'approved') {
            $pendingRequests = GroupJoinRequest::where('group_id', $id)
                ->where('status', 'pending')
                ->with('user')
                ->get();

            // 💡 OPTIONAL: Automatically flush matching request items when viewing the queue page
            $user->unreadNotifications()
                ->where('data->title', '📥 New Group Join Request')
                ->where(function($q) use ($group) {
                    $q->where('data->url', 'like', '%' . route('student.community-hub.show', $group->id) . '%')
                      ->orWhere('data->url', 'like', '%' . route('community-hub.view', $group->id) . '%');
                })
                ->get()
                ->markAsRead();
        }
        
        // Get group members
        $members = GroupMember::where('group_id', $id)
            ->where('status', 'approved')
            ->with('user')
            ->get();
        
        return view('student.community-hub-show', compact('group', 'userMember', 'posts', 'members', 'pendingRequests'));
    }

    /**
     * Create a post in a group
     */
    public function createPost(Request $request, $groupId)
    {
        $request->validate([
            'content' => 'required|string|max:5000'
        ]);
        
        // Check if user is a member of the group
        $isMember = GroupMember::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->exists();
            
        if (!$isMember) {
            return back()->with('error', 'You must be a member to post in this group.');
        }
        
        $post = GroupPost::create([
            'group_id' => $groupId,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'media' => null,
            'is_pinned' => false,
            'likes_count' => 0
        ]);

        // 🔔 NOTIFICATION 2: Post Created -> Notify Everyone In Group (excluding the author)
        $group = CommunityGroup::find($groupId);
        $groupMembers = User::whereHas('groupMemberships', function($q) use ($groupId) {
            $q->where('group_id', $groupId)->where('status', 'approved');
        })->where('id', '!=', auth()->id())->get();

        $title = "✍️ New Post in {$group->name}";
        $message = auth()->user()->name . " started a new conversation thread.";
        $url = route('community-hub.view', $groupId);

        Notification::send($groupMembers, new CommunityNotification($title, $message, $url));
        
        return back()->with('success', 'Your post has been published!');
    }
    
    /**
     * Delete a post
     */
    public function deletePost($groupId, $postId)
    {
        $post = GroupPost::where('group_id', $groupId)->findOrFail($postId);
        
        // Check if user is admin of group or post owner
        $isAdmin = GroupMember::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('role', 'admin')
            ->where('status', 'approved')
            ->exists();
            
        if ($post->user_id === auth()->id() || $isAdmin) {

            // 🔔 NOTIFICATION 3: Admin Deletes Post -> Inform Post Owner
            if ($post->user_id !== auth()->id() && $isAdmin) {
                $postOwner = User::find($post->user_id);
                if ($postOwner) {
                    $title = "🗑️ Post Removed by Admin";
                    $message = "Your post inside the group has been deleted by a group administrator.";
                    $url = route('community-hub.view', $groupId);
                    
                    $postOwner->notify(new CommunityNotification($title, $message, $url));
                }
            }

            $post->delete();
            return back()->with('success', 'Post deleted.');
        }
        
        return back()->with('error', 'Unauthorized to delete this post.');
    }
    
    /**
     * Like/Unlike a post
     */
    public function likePost($groupId, $postId)
    {
        try {
            $post = GroupPost::findOrFail($postId);
            $userId = auth()->id();
            
            // Check if user is member of the group
            $isMember = GroupMember::where('group_id', $groupId)
                ->where('user_id', $userId)
                ->where('status', 'approved')
                ->exists();
                
            if (!$isMember) {
                return response()->json([
                    'success' => false, 
                    'message' => 'You must be a member to like posts'
                ], 403);
            }
            
            // Toggle like using the model method
            $liked = $post->toggleLike($userId);
            
            // Refresh the post to get updated likes count
            $post->refresh();

            // 🔔 NOTIFICATION 4: Post Liked -> Inform Thread Owner (Ignore if self-like)
            if ($liked && $post->user_id !== $userId) {
                $postOwner = User::find($post->user_id);
                if ($postOwner) {
                    $title = "❤️ Post Liked";
                    $message = auth()->user()->name . " liked your community post thread.";
                    $url = route('community-hub.view', $groupId);

                    $postOwner->notify(new CommunityNotification($title, $message, $url));
                }
            }
            
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes' => $post->likes_count,
                'message' => $liked ? 'Post liked!' : 'Post unliked!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Like error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process like. Please try again.'
            ], 500);
        }
    }    
    /**
     * Create a comment on a post
     */
    public function createComment(Request $request, $groupId, $postId)
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string|max:1000'
            ]);
            
            $userId = auth()->id();
            
            // Check if user is member of the group
            $isMember = GroupMember::where('group_id', $groupId)
                ->where('user_id', $userId)
                ->where('status', 'approved')
                ->exists();
                
            if (!$isMember) {
                return response()->json([
                    'success' => false, 
                    'message' => 'You must be a member to comment'
                ], 403);
            }
            
            $post = GroupPost::findOrFail($postId);
            
            if ($post->group_id != $groupId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }
            
            $comment = $post->comments()->create([
                'user_id' => $userId,
                'content' => $validated['content']
            ]);
            
            // Load the user relationship
            $comment->load('user');

            // 🔔 NOTIFICATION 5: New Comment -> Inform Thread Owner (Ignore if self-comment)
            if ($post->user_id !== $userId) {
                $postOwner = User::find($post->user_id);
                if ($postOwner) {
                    $title = "💬 New Comment Received";
                    $message = auth()->user()->name . " commented: \"" . \Str::limit($comment->content, 40) . "\"";
                    $url = route('community-hub.view', $groupId);

                    $postOwner->notify(new CommunityNotification($title, $message, $url));
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user_name' => $comment->user->name,
                    'user_initial' => strtoupper(substr($comment->user->name, 0, 1)),
                    'created_at' => $comment->created_at->diffForHumans()
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Comment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Delete a comment
     */
    public function deleteComment($groupId, $postId, $commentId)
    {
        try {
            $userId = auth()->id();
            
            $comment = GroupPostComment::findOrFail($commentId);
            
            // Check if user is the comment author or group admin
            $isMember = GroupMember::where('group_id', $groupId)
                ->where('user_id', $userId)
                ->where('status', 'approved')
                ->first();
            
            if (!$isMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be a member to delete comments'
                ], 403);
            }
            
            $post = GroupPost::findOrFail($postId);
            
            if ($post->group_id != $groupId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }
            
            // Check authorization - only comment author or group admin can delete
            if ($comment->user_id !== $userId && $isMember->role !== 'admin' && !$post->group->isCreator($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own comments'
                ], 403);
            }
            
            $comment->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Delete comment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment. Please try again.'
            ], 500);
        }
    }    /**
     * Update group (admin/creator only)
     */
    public function update(Request $request, $id)
    {
        $group = CommunityGroup::findOrFail($id);
        $user = auth()->user();
        
        // Check authorization - admin or creator
        $isCreator = $group->created_by == $user->id;
        $isAdmin = GroupMember::where('group_id', $id)
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->where('status', 'approved')
            ->exists();
        
        if (!$isCreator && !$isAdmin) {
            return back()->with('error', 'Only group admins can update the group.');
        }
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100|unique:community_groups,name,' . $id,
            'description' => 'sometimes|string|max:1000',
            'privacy' => 'sometimes|in:public,by_approval',
            'max_members' => 'sometimes|nullable|integer|min:1|max:10000',
            'category' => 'sometimes|string',
        ]);
        
        try {
            // If privacy changed to by_approval, update require_approval
            if (isset($validated['privacy'])) {
                $validated['require_approval'] = $validated['privacy'] === 'by_approval';
            }
            
            $group->update($validated);
            return back()->with('success', 'Group updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update group. Please try again.');
        }
    }

    /**
     * Delete group (creator only)
     */
    public function destroy($id)
    {
        $group = CommunityGroup::findOrFail($id);
        $user = auth()->user();
        
        // Check authorization - only creator
        if ($group->created_by !== $user->id) {
            return back()->with('error', 'Only the group creator can delete the group.');
        }
        
        DB::beginTransaction();
        
        try {
            // Delete all posts
            GroupPost::where('group_id', $id)->delete();
            
            // Delete all members
            GroupMember::where('group_id', $id)->delete();
            
            // Delete all join requests
            GroupJoinRequest::where('group_id', $id)->delete();
            
            // Delete group
            $group->delete();
            
            DB::commit();
            
            return redirect()->route($this->getRoutePrefix() . '.community-hub')
                ->with('success', 'Group deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to delete group. Please try again.');
        }
    }

    /**
     * Remove a member from the group (admin only)
     */
    public function removeMember($groupId, $userId)
    {
        $group = CommunityGroup::findOrFail($groupId);
        $user = auth()->user();
        
        // Check authorization - only admin
        $isAdmin = GroupMember::where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->where('status', 'approved')
            ->exists();
        
        if (!$isAdmin) {
            return back()->with('error', 'Only group admins can remove members.');
        }
        
        // Prevent removing the group creator
        if ($group->created_by == $userId) {
            return back()->with('error', 'Cannot remove the group creator.');
        }
        
        DB::beginTransaction();
        
        try {
            $member = GroupMember::where('group_id', $groupId)
                ->where('user_id', $userId)
                ->firstOrFail();
            
            $member->delete();
            $group->decrement('member_count');
            DB::commit();
            
            return back()->with('success', 'Member removed from the group.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to remove member. Please try again.');
        }
    }

    /**
     * Approve a join request (admin only)
     */
    public function approveJoinRequest($groupId, $requestId)
    {
        $group = CommunityGroup::findOrFail($groupId);
        $user = auth()->user();
        $request = GroupJoinRequest::findOrFail($requestId);
        
        // Verify request belongs to this group
        if ($request->group_id != $groupId) {
            return back()->with('error', 'Invalid request.');
        }
        
        // Check authorization - admin or group creator
        $isAuthorized = GroupMember::where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->where('status', 'approved')
            ->exists();
        
        $isCreator = $group->created_by === $user->id;
        
        if (!$isAuthorized && !$isCreator) {
            return back()->with('error', 'Only group creator or admins can approve requests.');
        }
        
        // Check member limit
        if ($group->max_members && $group->member_count >= $group->max_members) {
            return back()->with('error', 'Group has reached maximum member limit.');
        }
        
        DB::beginTransaction();
        
        try {
            // Create approved member
            GroupMember::create([
                'group_id' => $groupId,
                'user_id' => $request->user_id,
                'role' => 'member',
                'status' => 'approved',
                'joined_at' => now()
            ]);
            
            // Update request status
            $request->update(['status' => 'approved']);
            
            // Increment member count
            $group->increment('member_count');
            
            DB::commit();

            // 🔔 NOTIFICATION 6: Join Request Approved -> Inform Requesting User
            $targetUser = User::find($request->user_id);
            if ($targetUser) {
                $title = "🎉 Request Approved!";
                $message = "Your request to join the group '{$group->name}' has been approved.";
                $url = route('community-hub.view', $groupId);

                $targetUser->notify(new CommunityNotification($title, $message, $url));
            }
            
            return back()->with('success', 'Join request approved!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to approve request. Please try again.');
        }
    }

    /**
     * Reject a join request (admin or creator only)
     */
    public function rejectJoinRequest($groupId, $requestId)
    {
        $group = CommunityGroup::findOrFail($groupId);
        $user = auth()->user();
        $request = GroupJoinRequest::findOrFail($requestId);
        
        // Verify request belongs to this group
        if ($request->group_id != $groupId) {
            return back()->with('error', 'Invalid request.');
        }
        
        // Check authorization - admin or group creator
        $isAuthorized = GroupMember::where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->where('status', 'approved')
            ->exists();
        
        $isCreator = $group->created_by === $user->id;
        
        if (!$isAuthorized && !$isCreator) {
            return back()->with('error', 'Only group creator or admins can reject requests.');
        }
        
        try {
            $request->update(['status' => 'rejected']);

            // 🔔 NOTIFICATION 7: Join Request Rejected -> Inform Requesting User
            $targetUser = User::find($request->user_id);
            if ($targetUser) {
                $title = "❌ Join Request Declined";
                $message = "Your request to join '{$group->name}' was declined by the administration.";
                $url = route('community-hub');

                $targetUser->notify(new CommunityNotification($title, $message, $url));
            }
            
            return back()->with('success', 'Join request rejected.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject request. Please try again.');
        }
    }

    /**
     * Update a post (author or admin only)
     */
    public function editPost(Request $request, $groupId, $postId)
    {
        $post = GroupPost::where('group_id', $groupId)->findOrFail($postId);
        $user = auth()->user();
        
        // Check authorization - post owner or admin
        $isAdmin = GroupMember::where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->where('status', 'approved')
            ->exists();
        
        if ($post->user_id !== $user->id && !$isAdmin) {
            return back()->with('error', 'You cannot edit this post.');
        }
        
        $validated = $request->validate([
            'content' => 'required|string|max:5000'
        ]);
        
        try {
            $post->update($validated);
            return back()->with('success', 'Post updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update post. Please try again.');
        }
    }

    /**
     * Pin a post (admin only)
     */
    public function pinPost($groupId, $postId)
    {
        $group = CommunityGroup::findOrFail($groupId);
        $post = GroupPost::where('group_id', $groupId)->findOrFail($postId);
        $user = auth()->user();
        
        // Check authorization - only admin
        $isAdmin = GroupMember::where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->where('status', 'approved')
            ->exists();
        
        if (!$isAdmin) {
            return back()->with('error', 'Only group admins can pin posts.');
        }
        
        try {
            $post->update(['is_pinned' => !$post->is_pinned]);
            $action = $post->is_pinned ? 'pinned' : 'unpinned';
            return back()->with('success', "Post {$action} successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to pin/unpin post. Please try again.');
        }
    }

    private function getRoutePrefix()
    {
        $role = auth()->user()->role;
        return in_array($role, ['admin', 'staff', 'student']) ? $role : 'student';
    }
}