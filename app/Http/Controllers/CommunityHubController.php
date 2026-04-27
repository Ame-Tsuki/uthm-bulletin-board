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
            
        $posts = GroupPost::with(['user', 'group'])
            ->whereIn('group_id', $groupIds)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();
        
        return view('student.community-hub', compact('allGroups', 'myGroups', 'pendingRequests', 'posts', 'user'));
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
                'require_approval' => false,
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
            
            return redirect()->route('student.community-hub')
                ->with('success', 'Group "' . $group->name . '" created successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create group. Please try again.');
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
            return back()->with('error', 'You are already a member or have a pending request.');
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
                return back()->with('success', 'You joined "' . $group->name . '"!');
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
        
        if ($member->role === 'admin') {
            return back()->with('error', 'Group admins cannot leave. Transfer ownership or delete the group first.');
        }
        
        DB::beginTransaction();
        
        try {
            $member->delete();
            CommunityGroup::where('id', $id)->decrement('member_count');
            DB::commit();
            return redirect()->route('student.community-hub')->with('success', 'You have left the group.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to leave group.');
        }
    }
    
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
            'is_pinned' => false
        ]);
        
        return back()->with('success', 'Your post has been published!');
    }
    
    public function deletePost($groupId, $postId)
    {
        $post = GroupPost::where('group_id', $groupId)->findOrFail($postId);
        
        // Check if user is admin of group or post owner
        $isAdmin = GroupMember::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('role', 'admin')
            ->exists();
            
        if ($post->user_id === auth()->id() || $isAdmin) {
            $post->delete();
            return back()->with('success', 'Post deleted.');
        }
        
        return back()->with('error', 'Unauthorized to delete this post.');
    }
    
    public function likePost($groupId, $postId)
    {
        $post = GroupPost::findOrFail($postId);
        
        // Check if user is member
        $isMember = GroupMember::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->exists();
            
        if (!$isMember) {
            return response()->json(['error' => 'You must be a member to like posts'], 403);
        }
        
        // Check if already liked (using a likes table - simplified, just increment count for demo)
        // For now, just increment/decrement like count
        $post->increment('likes_count');
        
        return response()->json(['success' => true, 'likes' => $post->likes_count]);
    }
}