<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with statistics
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'students' => User::where('role', 'student')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'club_admins' => User::where('role', 'club_admin')->count(),
            'verified_users' => User::where('is_verified', true)->count(),
            'unverified_users' => User::where('is_verified', false)->count(),
            'recent_users' => User::latest()->take(10)->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get all users with pagination
     */
    public function getUsers(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search', '');
        $role = $request->get('role', '');
        $verified = $request->get('verified', '');

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('uthm_id', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        if ($verified !== '') {
            $query->where('is_verified', $verified === 'true' || $verified === '1');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Get single user details
     */
    public function getUser($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Create a new user
     */
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'uthm_id' => 'required|string|unique:users,uthm_id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['student', 'staff', 'admin', 'club_admin'])],
            'faculty' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
            'is_verified' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_verified'] = $validated['is_verified'] ?? false;

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Update user information
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'uthm_id' => ['sometimes', 'string', Rule::unique('users')->ignore($user->id)],
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'role' => ['sometimes', Rule::in(['student', 'staff', 'admin', 'club_admin'])],
            'faculty' => 'nullable|string|max:255',
            'password' => 'sometimes|string|min:8',
            'is_verified' => 'boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user->fresh()
        ]);
    }

    /**
     * Delete a user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        if ($user->id == optional(auth()->guard('web')->user())->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account'
            ], 403);
        }
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    
    }

    /**
     * Verify/Unverify a user
     */
    public function toggleUserVerification($id)
    {
        $user = User::findOrFail($id);
        $user->is_verified = !$user->is_verified;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => $user->is_verified ? 'User verified successfully' : 'User unverified successfully',
            'data' => $user
        ]);
    }

    /**
     * Bulk operations on users
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['verify', 'unverify', 'delete', 'change_role'])],
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role' => 'required_if:action,change_role|string',
        ]);

        $userIds = $validated['user_ids'];
        $action = $validated['action'];

        // Prevent deleting yourself
        if ($action === 'delete' && in_array(optional(auth()->guard('web')->user())->id, $userIds)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account'
            ], 403);
        }

        $users = User::whereIn('id', $userIds);

        switch ($action) {
            case 'verify':
                $users->update(['is_verified' => true]);
                $message = 'Users verified successfully';
                break;
            case 'unverify':
                $users->update(['is_verified' => false]);
                $message = 'Users unverified successfully';
                break;
            case 'delete':
                $users->delete();
                $message = 'Users deleted successfully';
                break;
            case 'change_role':
                $users->update(['role' => $validated['role']]);
                $message = 'User roles updated successfully';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'affected_count' => count($userIds)
        ]);
    }

    /**
     * Get user statistics by role
     */
    public function getUserStatistics()
    {
        $stats = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get()
            ->keyBy('role');

        $verifiedStats = [
            'verified' => User::where('is_verified', true)->count(),
            'unverified' => User::where('is_verified', false)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'by_role' => $stats,
                'by_verification' => $verifiedStats,
            ]
        ]);
    }

    /**
     * Get recent activity (recent users)
     */
    public function getRecentActivity()
    {
        $recentUsers = User::orderBy('created_at', 'desc')->take(20)->get([
            'id', 'name', 'email', 'role', 'is_verified', 'created_at'
        ]);

        return response()->json([
            'success' => true,
            'data' => $recentUsers
        ]);
    }

}