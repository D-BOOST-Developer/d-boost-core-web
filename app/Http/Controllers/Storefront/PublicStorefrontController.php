<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\ClientReview;
use App\Models\MarketplaceLink;
use App\Models\Product;
use App\Models\StorefrontConfig;
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
        // Use v2 key to avoid conflicts with old cached data
        $cacheKey = "storefront_v3_{$slug}";
        
        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($slug) {
            $config = StorefrontConfig::where('slug', $slug)
                ->published()
                ->with(['user', 'user.businessProfile'])
                ->first();

            if (!$config) {
                return null;
            }

            $links = $config->user->marketplaceLinks()
                ->active()
                ->ordered()
                ->get();

            $featuredProducts = Product::withoutGlobalScope('user')
                ->where('user_id', $config->user_id)
                ->publishedToStorefront()
                ->orderBy('name')
                ->take(8)
                ->get();

            $reviews = $config->reviews()
                ->approved()
                ->latest()
                ->take(10)
                ->get();

            $businessProfile = $config->user->businessProfile;

            return [
                'config' => $config,
                'links' => $links,
                'featuredProducts' => $featuredProducts,
                'reviews' => $reviews,
                'businessProfile' => $businessProfile,
            ];
        });

        if (!$data) {
            abort(404, 'Storefront not found or not published.');
        }

        return view('storefront.public.show', [
            'config' => $data['config'],
            'links' => $data['links'],
            'featuredProducts' => $data['featuredProducts'] ?? collect(),
            'reviews' => $data['reviews'],
            'businessProfile' => $data['businessProfile'],
            'isPreview' => false,
        ]);
    }

    /**
     * Display the public product catalog for a storefront.
     */
    public function catalog(Request $request, string $slug)
    {
        $config = StorefrontConfig::where('slug', $slug)
            ->published()
            ->with(['user.businessProfile'])
            ->firstOrFail();

        $query = Product::withoutGlobalScope('user')
            ->where('user_id', $config->user_id)
            ->publishedToStorefront();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $products = $query->orderBy('name')->paginate(12)->withQueryString();

        $categories = Product::withoutGlobalScope('user')
            ->where('user_id', $config->user_id)
            ->publishedToStorefront()
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        $cartCount = collect(session("storefront_cart.{$slug}", []))
            ->sum('qty');

        $links = $config->user->marketplaceLinks()
            ->active()
            ->ordered()
            ->get();

        return view('storefront.public.catalog', [
            'config' => $config,
            'products' => $products,
            'categories' => $categories,
            'links' => $links,
            'businessProfile' => $config->user->businessProfile,
            'cartCount' => $cartCount,
        ]);
    }

    /**
     * Display a single product detail for the storefront.
     */
    public function productDetail(string $slug, string $product)
    {
        $config = StorefrontConfig::where('slug', $slug)
            ->published()
            ->with(['user.businessProfile'])
            ->firstOrFail();

        $product = Product::withoutGlobalScope('user')
            ->where('user_id', $config->user_id)
            ->where('id', $product)
            ->publishedToStorefront()
            ->firstOrFail();

        return view('storefront.public.product-detail', [
            'config' => $config,
            'product' => $product,
            'businessProfile' => $config->user->businessProfile,
        ]);
    }

    /**
     * Show review submission form page
     */
    public function showReviewForm(string $slug)
    {
        // Use unique cache key to avoid conflict with show() method
        $config = Cache::remember("storefront_review_ctx_{$slug}", 3600, function () use ($slug) {
            return StorefrontConfig::where('slug', $slug)
                ->where('is_published', true)
                ->with(['user.businessProfile'])
                ->first();
        });

        if (!$config) {
            abort(404, 'Storefront not found.');
        }

        $businessProfile = $config->user->businessProfile;

        return view('storefront.public.review-form', compact('config', 'businessProfile'));
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

    /**
     * Submit a client review
     */
    public function submitReview(Request $request, string $slug)
    {
        $config = StorefrontConfig::where('slug', $slug)
            ->published()
            ->first();

        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'Storefront not found.',
            ], 404);
        }

        $validated = $request->validate([
            'client_name' => 'required|string|max:100',
            'client_email' => 'nullable|email|max:255',
            'review_text' => 'required|string|min:10|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $review = ClientReview::create([
            'storefront_config_id' => $config->id,
            'client_name' => $validated['client_name'],
            'client_email' => $validated['client_email'] ?? null,
            'review_text' => $validated['review_text'],
            'rating' => $validated['rating'],
            'is_approved' => false, // Needs admin approval
        ]);

        // Clear cache so new review shows after approval
        Cache::forget("storefront_{$slug}");

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih! Review Anda akan ditampilkan setelah disetujui.',
        ]);
    }
}
