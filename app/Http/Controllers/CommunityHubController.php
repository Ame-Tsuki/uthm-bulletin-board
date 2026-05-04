<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommunityGroup;
use App\Models\GroupMember;
use App\Models\GroupPost;
use App\Models\GroupJoinRequest;
use Illuminate\Support\Facades\DB;

class CommunityHubController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get all groups with member count
        $allGroups = CommunityGroup::withCount('members')
            ->orderBy('created_at', 'desc')
            ->paginate(9);
        
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
        
        return view('student.community-hub', compact('allGroups', 'myGroups', 'pendingRequests', 'posts'));
    }
    
    public function create()
    {
        return view('student.community-hub-create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:community_groups',
            'description' => 'required|string|max:1000',
            'category' => 'required|string',
            'privacy' => 'required|in:public,private,by_approval',
            'max_members' => 'nullable|integer|min:1|max:10000',
        ]);
        
        DB::beginTransaction();
        
        try {
            $group = CommunityGroup::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'privacy' => $validated['privacy'],
                'max_members' => $validated['max_members'],
                'created_by' => auth()->id(),
                'member_count' => 1,
                'allow_posts' => true,
                'allow_events' => true,
                'require_approval' => $validated['privacy'] === 'by_approval',
            ]);
            
            // Add creator as admin member
            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => auth()->id(),
                'role' => 'admin',
                'status' => 'approved',
                'joined_at' => now()
            ]);
            
            DB::commit();
            
            return redirect()->route('student.community-hub.show', $group->id)
                ->with('success', 'Group "' . $group->name . '" created successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create group. Please try again.')->withInput();
        }
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
                return redirect()->route('student.community-hub.show', $id)
                    ->with('success', 'You joined "' . $group->name . '"!');
            } else {
                // Private or approval required - send request
                GroupJoinRequest::create([
                    'group_id' => $id,
                    'user_id' => $user->id,
                    'status' => 'pending'
                ]);
                DB::commit();
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
            return redirect()->route('student.community-hub')->with('success', 'You have left the group.');
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
            ->with(['user', 'group', 'likes'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($post) use ($user) {
                $post->is_liked = $post->isLikedBy($user->id);
                return $post;
            });
        
        // Get pending join requests (for admin only)
        $pendingRequests = collect();
        if ($userMember && $userMember->role === 'admin' && $userMember->status === 'approved') {
            $pendingRequests = GroupJoinRequest::where('group_id', $id)
                ->where('status', 'pending')
                ->with('user')
                ->get();
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
            'privacy' => 'sometimes|in:public,private,by_approval',
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
            
            return redirect()->route('student.community-hub')
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
        
        // Check authorization - only admin
        $isAdmin = GroupMember::where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->where('status', 'approved')
            ->exists();
        
        if (!$isAdmin) {
            return back()->with('error', 'Only group admins can approve requests.');
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
            
            return back()->with('success', 'Join request approved!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to approve request. Please try again.');
        }
    }

    /**
     * Reject a join request (admin only)
     */
    public function rejectJoinRequest($groupId, $requestId)
    {
        $user = auth()->user();
        $request = GroupJoinRequest::findOrFail($requestId);
        
        // Verify request belongs to this group
        if ($request->group_id != $groupId) {
            return back()->with('error', 'Invalid request.');
        }
        
        // Check authorization - only admin
        $isAdmin = GroupMember::where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->where('role', 'admin')
            ->where('status', 'approved')
            ->exists();
        
        if (!$isAdmin) {
            return back()->with('error', 'Only group admins can reject requests.');
        }
        
        try {
            $request->update(['status' => 'rejected']);
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
}