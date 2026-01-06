<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\StorefrontConfig;
use App\Models\MarketplaceLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicStorefrontController extends Controller
{
    /**
     * Display public storefront by slug
     */
    public function show(string $slug)
    {
        // Cache the storefront for 5 minutes
        $cacheKey = "storefront_{$slug}";
        
        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($slug) {
            $config = StorefrontConfig::where('slug', $slug)
                ->published()
                ->with('user')
                ->first();

            if (!$config) {
                return null;
            }

            $links = $config->user->marketplaceLinks()
                ->active()
                ->ordered()
                ->get();

            return [
                'config' => $config,
                'links' => $links,
            ];
        });

        if (!$data) {
            abort(404, 'Storefront not found or not published.');
        }

        return view('storefront.public.show', [
            'config' => $data['config'],
            'links' => $data['links'],
            'isPreview' => false,
        ]);
    }

    /**
     * Track clicks on marketplace links (AJAX)
     */
    public function trackClick(string $slug, MarketplaceLink $link)
    {
        // Verify link belongs to the storefront owner
        $config = StorefrontConfig::where('slug', $slug)->first();
        
        if (!$config || $link->user_id !== $config->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid link or storefront.',
            ], 404);
        }

        // Increment click count
        $link->incrementClicks();

        return response()->json([
            'success' => true,
            'redirect_url' => $link->url,
            'click_count' => $link->click_count,
        ]);
    }
}
