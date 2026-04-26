<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommunityGroup;
use App\Models\GroupMember;
use App\Models\GroupJoinRequest;

class CommunityHubController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get user's groups
        $myGroups = CommunityGroup::whereHas('members', function($q) use ($user) {
            $q->where('user_id', $user->id)->where('status', 'approved');
        })->withCount('members')->get();
        
        // Get pending join requests
        $pendingRequests = GroupJoinRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('group')
            ->get();
        
        // Get groups user manages (for approval)
        $manageRequests = GroupJoinRequest::whereHas('group.members', function($q) use ($user) {
            $q->where('user_id', $user->id)->whereIn('role', ['admin', 'moderator']);
        })->where('status', 'pending')->with(['group', 'user'])->get();
        
        // Get recommended groups
        $recommendedGroups = CommunityGroup::where('privacy', 'public')
            ->whereDoesntHave('members', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->withCount('members')
            ->latest()
            ->take(6)
            ->get();
        
        // Get all groups for discovery
        $allGroups = CommunityGroup::withCount('members')
            ->orderBy('member_count', 'desc')
            ->paginate(12);
        
        // Return your existing view - NOT community-hub.index
        return view('student.community-hub', compact('myGroups', 'pendingRequests', 'manageRequests', 'recommendedGroups', 'allGroups', 'user'));
    }
    
    public function show($id)
    {
        $group = CommunityGroup::with(['creator', 'members.user', 'posts.user'])
            ->withCount('members')
            ->findOrFail($id);
        
        $user = auth()->user();
        
        $isMember = GroupMember::where('group_id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->exists();
        
        $userRole = null;
        if ($isMember) {
            $member = GroupMember::where('group_id', $id)
                ->where('user_id', $user->id)
                ->first();
            $userRole = $member->role;
        }
        
        $pendingRequest = GroupJoinRequest::where('group_id', $id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();
        
        $members = GroupMember::with('user')
            ->where('group_id', $id)
            ->where('status', 'approved')
            ->paginate(20);
        
        $posts = GroupPost::with(['user', 'comments.user', 'likes'])
            ->where('group_id', $id)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $joinRequests = [];
        if (in_array($userRole, ['admin', 'moderator'])) {
            $joinRequests = GroupJoinRequest::with('user')
                ->where('group_id', $id)
                ->where('status', 'pending')
                ->get();
        }
        
        // Return your group detail view (you may need to create this)
        return view('student.community-hub-show', compact('group', 'isMember', 'userRole', 'pendingRequest', 'members', 'posts', 'joinRequests', 'user'));
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
            'allow_posts' => 'boolean',
            'allow_events' => 'boolean',
        ]);
        
        $validated['created_by'] = auth()->id();
        $validated['member_count'] = 1;
        
        $group = CommunityGroup::create($validated);
        
        // Add creator as admin member
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => auth()->id(),
            'role' => 'admin',
            'status' => 'approved',
            'joined_at' => now()
        ]);
        
        return redirect()->route('student.community-hub.show', $group->id)
            ->with('success', 'Group created successfully!');
    }
    
    public function join($id)
    {
        $group = CommunityGroup::findOrFail($id);
        $user = auth()->user();
        
        if ($group->isFull()) {
            return back()->with('error', 'This group is full!');
        }
        
        $existing = GroupMember::where('group_id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if ($existing) {
            return back()->with('error', 'You are already a member or have a pending request.');
        }
        
        if ($group->privacy === 'public') {
            GroupMember::create([
                'group_id' => $id,
                'user_id' => $user->id,
                'role' => 'member',
                'status' => 'approved',
                'joined_at' => now()
            ]);
            $group->increment('member_count');
            return back()->with('success', 'You have joined the group!');
        } else {
            GroupJoinRequest::create([
                'group_id' => $id,
                'user_id' => $user->id,
                'message' => $request->message ?? null
            ]);
            return back()->with('info', 'Join request sent! Waiting for approval.');
        }
    }
    
    public function leave($id)
    {
        $member = GroupMember::where('group_id', $id)
            ->where('user_id', auth()->id())
            ->first();
        
        if ($member && $member->role !== 'admin') {
            $member->delete();
            CommunityGroup::where('id', $id)->decrement('member_count');
            return redirect()->route('student.community-hub')->with('success', 'You have left the group.');
        }
        
        return back()->with('error', 'Group admin cannot leave. Transfer ownership first.');
    }
}