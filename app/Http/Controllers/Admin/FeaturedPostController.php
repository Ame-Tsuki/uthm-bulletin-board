<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeaturedPostController extends Controller
{
    private const MAX_FEATURED = 10;

    public function index()
    {
        // Added ->notBanned() to filter out banned announcements
        $announcements = Announcement::with('author')
            ->where('is_active', 1)
            ->notBanned() 
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Added ->notBanned() here too, so banned posts don't take up a slot in your max limit
        $featuredCount = Announcement::where('is_featured', 1)
            ->notBanned()
            ->count();
            
        $maxFeatured = self::MAX_FEATURED;
        
        return view('admin.featured-posts', compact('announcements', 'featuredCount', 'maxFeatured'));
    }
    
    public function toggle(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:announcements,id'
            ]);
            
            $announcement = Announcement::findOrFail($request->id);
            
            // If trying to feature, check limit
            if (!$announcement->is_featured) {
                $currentFeatured = Announcement::where('is_featured', 1)->count();
            
                if ($currentFeatured >= self::MAX_FEATURED) {
                    return response()->json([
                        'success' => false,
                        'message' => "Maximum " . self::MAX_FEATURED . " featured posts allowed. Remove some first."
                    ], 400);
                }
                
                // Set featured order - last position
                $maxOrder = Announcement::max('featured_order') ?? 0;
                $announcement->featured_order = $maxOrder + 1;
                $announcement->featured_at = now();
            } else {
                // Remove from featured
                $announcement->featured_order = 0;
                $announcement->featured_at = null;
            }
            
            $announcement->is_featured = !$announcement->is_featured;
            $announcement->save();
            
            // Reorder remaining featured posts
            if (!$announcement->is_featured) {
                $this->reorderFeaturedPosts();
            }
            
            return response()->json([
                'success' => true,
                'message' => $announcement->is_featured ? 'Post featured successfully' : 'Post removed from featured'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function reorder(Request $request)
    {
        try {
            $request->validate([
                'orders' => 'required|array',
                'orders.*.id' => 'required|exists:announcements,id',
                'orders.*.order' => 'required|integer|min:0'
            ]);
            
            // Wrap the loop in a database transaction
            DB::transaction(function () use ($request) {
                foreach ($request->orders as $item) {
                    Announcement::where('id', $item['id'])
                        ->update(['featured_order' => $item['order']]);
                }
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Featured posts reordered successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function reorderFeaturedPosts()
    {
        $featured = Announcement::where('is_featured', 1)
            ->orderBy('featured_order')
            ->get();
            
        foreach ($featured as $index => $item) {
            $item->featured_order = $index + 1;
            $item->save();
        }
    }
}