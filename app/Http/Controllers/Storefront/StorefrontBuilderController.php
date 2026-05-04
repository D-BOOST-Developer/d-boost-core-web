<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StorefrontConfig;
use App\Models\MarketplaceLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StorefrontBuilderController extends Controller
{
    /**
     * Display storefront overview/dashboard
     */
    public function index()
    {
        $config = auth()->user()->storefrontConfig;
        $linksCount = auth()->user()->marketplaceLinks()->count();
        $activeLinksCount = auth()->user()->marketplaceLinks()->active()->count();
        $totalClicks = auth()->user()->marketplaceLinks()->sum('click_count');

        return view('storefront.index', compact('config', 'linksCount', 'activeLinksCount', 'totalClicks'));
    }

    /**
     * Show the builder interface
     */
    public function edit()
    {
        $config = auth()->user()->storefrontConfig;
        
        // Create default config if doesn't exist
        if (!$config) {
            $config = StorefrontConfig::create([
                'user_id' => auth()->id(),
                'slug' => StorefrontConfig::generateUniqueSlug(auth()->user()),
                'storefront_title' => auth()->user()->name . ' Store',
                'primary_color' => '#3B82F6',
            ]);
        }

        $links = auth()->user()->marketplaceLinks()->active()->ordered()->get();

        $featuredProducts = Product::query()
            ->where('user_id', auth()->id())
            ->publishedToStorefront()
            ->orderBy('name')
            ->take(8)
            ->get();

        return view('storefront.builder.edit', compact('config', 'links', 'featuredProducts'));
    }

    /**
     * Update storefront configuration
     */
    public function update(Request $request)
    {
        $request->merge(['template_type' => null]);
        $config = auth()->user()->storefrontConfig;

        $validated = $request->validate([
            'storefront_title' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'slug' => [
                'nullable', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/',
                Rule::notIn(StorefrontConfig::RESERVED_SLUGS),
                Rule::unique('storefront_configs', 'slug')->ignore($config?->id),
            ],
            'primary_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'grid_columns' => 'required|integer|in:2,3,4,5',
            'card_style' => 'required|in:shadow,bordered,flat,minimal',
            'header_text' => 'nullable|string|max:500',
            'about_text' => 'nullable|string|max:1000',
            'cta_text' => 'nullable|string|max:255',
            'footer_content' => 'nullable|string|max:500',
        ]);

        if (!$config) {
            // Create new config
            $slug = $validated['slug'] ?? StorefrontConfig::generateUniqueSlug(auth()->user());
            
            $config = StorefrontConfig::create([
                'user_id' => auth()->id(),
                'slug' => $slug,
                'storefront_title' => $validated['storefront_title'],
                'tagline' => $validated['tagline'] ?? null,
                'primary_color' => $validated['primary_color'],
                'secondary_color' => $validated['secondary_color'] ?? null,
                'text_color' => $validated['text_color'] ?? null,
                'grid_columns' => $validated['grid_columns'],
                'card_style' => $validated['card_style'],
                'text_blocks' => json_encode([
                    'header_text' => $validated['header_text'] ?? '',
                    'about_text' => $validated['about_text'] ?? '',
                    'cta_text' => $validated['cta_text'] ?? '',
                ]),
                'footer_content' => $validated['footer_content'] ?? null,
            ]);
        } else {
            // Update existing config
            $updateData = [
                'storefront_title' => $validated['storefront_title'],
                'tagline' => $validated['tagline'] ?? null,
                'primary_color' => $validated['primary_color'],
                'secondary_color' => $validated['secondary_color'] ?? null,
                'text_color' => $validated['text_color'] ?? null,
                'grid_columns' => $validated['grid_columns'],
                'card_style' => $validated['card_style'],
                'text_blocks' => json_encode([
                    'header_text' => $validated['header_text'] ?? '',
                    'about_text' => $validated['about_text'] ?? '',
                    'cta_text' => $validated['cta_text'] ?? '',
                ]),
                'footer_content' => $validated['footer_content'] ?? null,
            ];

            if (isset($validated['slug']) && $validated['slug'] !== $config->slug) {
                $updateData['slug'] = StorefrontConfig::generateUniqueSlug(auth()->user(), $validated['slug']);
            }

            $config->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Configuration saved successfully!',
            'config' => $config
        ]);
    }

    /**
     * Upload logo
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
        ]);

        $config = auth()->user()->storefrontConfig;

        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'Storefront config not found.',
            ], 404);
        }

        // Delete old logo if exists
        if ($config->logo_path) {
            Storage::disk('public')->delete($config->logo_path);
        }

        try {
            $logoPath = $request->file('logo')->store('storefront/logos', 'public');
            
            if (!$logoPath) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload logo.',
                ], 500);
            }

            $config->update(['logo_path' => $logoPath]);

            return response()->json([
                'success' => true,
                'message' => 'Logo uploaded successfully!',
                'logo_url' => $config->logo_url,
            ]);
        } catch (\Exception $e) {
            \Log::error('Logo upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload logo.',
            ], 500);
        }
    }

    /**
     * Publish or unpublish storefront
     */
    public function publish(Request $request)
    {
        $config = auth()->user()->storefrontConfig;

        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'Storefront config not found.',
            ], 404);
        }

        // Validate minimum requirements before publishing
        if ($request->input('action') === 'publish') {
            $linksCount = auth()->user()->marketplaceLinks()->active()->count();
            
            if ($linksCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must add at least one active link before publishing your storefront.',
                ], 422);
            }
        }

        $action = $request->input('action', 'toggle');
        
        if ($action === 'publish') {
            $config->publish();
            $message = 'Storefront published successfully!';
        } elseif ($action === 'unpublish') {
            $config->unpublish();
            $message = 'Storefront unpublished successfully!';
        } else {
            $config->togglePublish();
            $message = $config->is_published ? 'Storefront published successfully!' : 'Storefront unpublished successfully!';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_published' => $config->is_published,
            'public_url' => $config->public_url,
        ]);
    }

    /**
     * Preview the storefront (without publishing)
     */
    public function preview()
    {
        $config = auth()->user()->storefrontConfig;
        
        if (!$config) {
            abort(404, 'Storefront config not found. Please configure your storefront first.');
        }

        $links = auth()->user()->marketplaceLinks()->active()->ordered()->get();
        $businessProfile = auth()->user()->businessProfile;
        $reviews = $config->reviews()->approved()->latest()->take(10)->get();

        $featuredProducts = \App\Models\Product::query()
            ->where('user_id', auth()->id())
            ->publishedToStorefront()
            ->orderBy('name')
            ->take(8)
            ->get();

        return view('storefront.public.show', [
            'config' => $config,
            'links' => $links,
            'featuredProducts' => $featuredProducts,
            'businessProfile' => $businessProfile,
            'reviews' => $reviews,
            'isPreview' => true,
        ]);
    }
}
