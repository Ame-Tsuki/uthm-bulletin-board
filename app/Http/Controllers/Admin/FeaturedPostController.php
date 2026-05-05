<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeaturedPostController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('author')
            ->where('is_active', 1)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $featuredCount = Announcement::where('is_featured', 1)->count();
        $maxFeatured = 10; // Maximum featured posts allowed
        
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
                $maxFeatured = 10;
                
                if ($currentFeatured >= $maxFeatured) {
                    return response()->json([
                        'success' => false,
                        'message' => "Maximum {$maxFeatured} featured posts allowed. Remove some first."
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
    
   public function updateImage(Request $request)
{
    try {
        $request->validate([
            'id' => 'required|exists:announcements,id',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $announcement = Announcement::findOrFail($request->id);

        // DELETE IMAGE
        if ($request->has('remove_image')) {
            if ($announcement->featured_image) {
                $oldPath = str_replace(asset('storage/'), '', $announcement->featured_image);
                \Storage::disk('public')->delete($oldPath);
            }

            $announcement->featured_image = null;
        }

        // UPLOAD NEW IMAGE
        if ($request->hasFile('featured_image')) {
            if ($announcement->featured_image) {
                $oldPath = str_replace(asset('storage/'), '', $announcement->featured_image);
                \Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('featured_image')->store('featured_images', 'public');
            $announcement->featured_image = asset('storage/' . $path);
        }

        $announcement->save();

        return response()->json([
            'success' => true,
            'message' => 'Image updated successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
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
            
            foreach ($request->orders as $item) {
                Announcement::where('id', $item['id'])
                    ->update(['featured_order' => $item['order']]);
            }
            
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