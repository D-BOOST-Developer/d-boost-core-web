<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarketplaceLinkController extends Controller
{
    /**
     * Get list of marketplace links
     */
    public function index(Request $request)
    {
        $query = auth()->user()->marketplaceLinks();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('display_name', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%")
                  ->orWhere('platform', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by platform
        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } else {
                $query->where('is_active', false);
            }
        }

        $links = $query->ordered()->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $links->items(),
            'meta' => [
                'current_page' => $links->currentPage(),
                'last_page' => $links->lastPage(),
                'per_page' => $links->perPage(),
                'total' => $links->total(),
            ],
        ]);
    }

    /**
     * Get single marketplace link
     */
    public function show($id)
    {
        $link = auth()->user()->marketplaceLinks()->findOrFail($id);

        return response()->json([
            'data' => $link,
        ]);
    }

    /**
     * Create new marketplace link
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'link_type' => 'required|in:marketplace_profile,product',
            'url' => 'required|url|max:2000',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Auto-detect platform
        $validated['platform'] = MarketplaceLink::detectPlatform($validated['url']);
        $validated['user_id'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active', true);

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
                $validated['image_path'] = $request->file('image')->store('storefront/links', 'public');
                
                if (!$validated['image_path']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload image.',
                    ], 500);
                }
            } catch (\Exception $e) {
                \Log::error('Marketplace link image upload failed: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload image.',
                ], 500);
            }
        }

        // Set priority order to be last
        $maxPriority = auth()->user()->marketplaceLinks()->max('priority_order') ?? 0;
        $validated['priority_order'] = $maxPriority + 1;

        $link = MarketplaceLink::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Link created successfully!',
            'data' => $link,
        ], 201);
    }

    /**
     * Update marketplace link
     */
    public function update(Request $request, $id)
    {
        $link = auth()->user()->marketplaceLinks()->findOrFail($id);

        $validated = $request->validate([
            'link_type' => 'required|in:marketplace_profile,product',
            'url' => 'required|url|max:2000',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Auto-detect platform
        $validated['platform'] = MarketplaceLink::detectPlatform($validated['url']);
        $validated['is_active'] = $request->boolean('is_active', true);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($link->image_path) {
                Storage::disk('public')->delete($link->image_path);
            }

            try {
                $validated['image_path'] = $request->file('image')->store('storefront/links', 'public');
                
                if (!$validated['image_path']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload image.',
                    ], 500);
                }
            } catch (\Exception $e) {
                \Log::error('Marketplace link image upload failed: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload image.',
                ], 500);
            }
        }

        $link->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Link updated successfully!',
            'data' => $link,
        ]);
    }

    /**
     * Delete marketplace link
     */
    public function destroy($id)
    {
        $link = auth()->user()->marketplaceLinks()->findOrFail($id);

        // Delete image if exists
        if ($link->image_path) {
            Storage::disk('public')->delete($link->image_path);
        }

        $link->delete();

        return response()->json([
            'success' => true,
            'message' => 'Link deleted successfully!',
        ]);
    }

    /**
     * Toggle link status
     */
    public function toggleStatus($id)
    {
        $link = auth()->user()->marketplaceLinks()->findOrFail($id);
        $link->update(['is_active' => !$link->is_active]);

        $status = $link->is_active ? 'activated' : 'deactivated';
        return response()->json([
            'success' => true,
            'message' => "Link {$status} successfully!",
            'data' => $link,
        ]);
    }

    /**
     * Reorder links
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'links' => 'required|array',
            'links.*.id' => 'required|exists:marketplace_links,id',
            'links.*.priority_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['links'] as $linkData) {
            MarketplaceLink::where('id', $linkData['id'])
                ->where('user_id', auth()->id())
                ->update(['priority_order' => $linkData['priority_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Links reordered successfully!',
        ]);
    }
}
