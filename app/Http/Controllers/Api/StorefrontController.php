<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StorefrontConfig;
use App\Models\MarketplaceLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StorefrontController extends Controller
{
    /**
     * Get storefront overview/dashboard data
     */
    public function index()
    {
        $user = auth()->user();
        $config = $user->storefrontConfig;
        $linksCount = $user->marketplaceLinks()->count();
        $activeLinksCount = $user->marketplaceLinks()->active()->count();
        $totalClicks = $user->marketplaceLinks()->sum('click_count');

        return response()->json([
            'data' => [
                'config' => $config,
                'stats' => [
                    'links_count' => $linksCount,
                    'active_links_count' => $activeLinksCount,
                    'total_clicks' => $totalClicks,
                ],
            ],
        ]);
    }

    /**
     * Get storefront configuration
     */
    public function getConfig()
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

        return response()->json([
            'data' => $config,
        ]);
    }

    /**
     * Update storefront configuration
     */
    public function updateConfig(Request $request)
    {
        $validated = $request->validate([
            'storefront_title' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:100|regex:/^[a-z0-9-]+$/',
            'primary_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'grid_columns' => 'required|integer|in:2,3,4',
            'card_style' => 'required|in:shadow,bordered,flat',
            'header_text' => 'nullable|string|max:500',
            'about_text' => 'nullable|string|max:1000',
            'cta_text' => 'nullable|string|max:255',
            'footer_content' => 'nullable|string|max:500',
        ]);

        $config = auth()->user()->storefrontConfig;

        if (!$config) {
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
            'data' => $config,
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
                'data' => [
                    'logo_url' => $config->logo_url,
                ],
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
            'data' => [
                'is_published' => $config->is_published,
                'public_url' => $config->public_url,
            ],
        ]);
    }
}
