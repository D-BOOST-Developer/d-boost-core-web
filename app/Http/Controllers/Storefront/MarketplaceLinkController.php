<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarketplaceLinkController extends Controller
{
    /**
     * Display a listing of the resource.
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

        $links = $query->ordered()->paginate(12);
        $platforms = auth()->user()->marketplaceLinks()
            ->distinct()
            ->pluck('platform')
            ->filter();

        return view('storefront.links.index', compact('links', 'platforms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('storefront.links.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'link_type' => 'required|in:marketplace_profile,product',
            'url' => 'required|url|max:2000',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
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
                    return back()->withErrors(['image' => 'Failed to upload image. Please try again.'])->withInput();
                }
            } catch (\Exception $e) {
                \Log::error('Marketplace link image upload failed: ' . $e->getMessage());
                return back()->withErrors(['image' => 'Failed to upload image. Please try again.'])->withInput();
            }
        }

        // Set priority order to be last
        $maxPriority = auth()->user()->marketplaceLinks()->max('priority_order') ?? 0;
        $validated['priority_order'] = $maxPriority + 1;

        MarketplaceLink::create($validated);

        return redirect()->route('storefront.links.index')
            ->with('success', 'Link created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(MarketplaceLink $link)
    {
        // Ensure user owns this link
        if ($link->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('storefront.links.show', compact('link'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarketplaceLink $link)
    {
        // Ensure user owns this link
        if ($link->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('storefront.links.edit', compact('link'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MarketplaceLink $link)
    {
        // Ensure user owns this link
        if ($link->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'link_type' => 'required|in:marketplace_profile,product',
            'url' => 'required|url|max:2000',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
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
                    return back()->withErrors(['image' => 'Failed to upload image. Please try again.']);
                }
            } catch (\Exception $e) {
                \Log::error('Marketplace link image upload failed: ' . $e->getMessage());
                return back()->withErrors(['image' => 'Failed to upload image. Please try again.']);
            }
        }

        $link->update($validated);

        return redirect()->route('storefront.links.index')
            ->with('success', 'Link updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarketplaceLink $link)
    {
        // Ensure user owns this link
        if ($link->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete image if exists
        if ($link->image_path) {
            Storage::disk('public')->delete($link->image_path);
        }

        $link->delete();

        return redirect()->route('storefront.links.index')
            ->with('success', 'Link deleted successfully!');
    }

    /**
     * Toggle link status (active/inactive)
     */
    public function toggleStatus(MarketplaceLink $link)
    {
        // Ensure user owns this link
        if ($link->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $link->update(['is_active' => !$link->is_active]);

        $status = $link->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $status = $link->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Link {$status} successfully!");
    }

    /**
     * Reorder links via drag-and-drop
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
            'message' => 'Link has successfully updated!'
        ]);
    }
}
