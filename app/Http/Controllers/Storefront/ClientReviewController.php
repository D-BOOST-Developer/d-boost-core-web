<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\ClientReview;
use Illuminate\Http\Request;

class ClientReviewController extends Controller
{
    /**
     * Display all reviews for the authenticated user's storefront
     */
    public function index()
    {
        $config = auth()->user()->storefrontConfig;
        
        if (!$config) {
            return redirect()->route('storefront.builder.edit')
                ->with('error', 'Please configure your storefront first.');
        }

        $reviews = $config->reviews()
            ->latest()
            ->paginate(20);

        return view('storefront.reviews.index', compact('reviews', 'config'));
    }

    /**
     * Approve a review
     */
    public function approve(ClientReview $review)
    {
        // Verify review belongs to user's storefront
        $config = auth()->user()->storefrontConfig;
        
        if (!$config || $review->storefront_config_id !== $config->id) {
            abort(403, 'Unauthorized action.');
        }

        $review->update(['is_approved' => true]);

        // Clear ALL related cache keys so public storefront updates immediately
        $this->clearStorefrontCache($config->slug);

        return back()->with('success', 'Review approved! It is now visible on your public storefront.');
    }

    /**
     * Reject (unapprove) a review
     */
    public function reject(ClientReview $review)
    {
        // Verify review belongs to user's storefront
        $config = auth()->user()->storefrontConfig;
        
        if (!$config || $review->storefront_config_id !== $config->id) {
            abort(403, 'Unauthorized action.');
        }

        $review->update(['is_approved' => false]);

        // Clear ALL related cache keys so public storefront updates immediately
        $this->clearStorefrontCache($config->slug);

        return back()->with('success', 'Review unapproved. It has been hidden from your storefront.');
    }

    /**
     * Delete a review
     */
    public function destroy(ClientReview $review)
    {
        // Verify review belongs to user's storefront
        $config = auth()->user()->storefrontConfig;
        
        if (!$config || $review->storefront_config_id !== $config->id) {
            abort(403, 'Unauthorized action.');
        }

        $review->delete();

        // Clear ALL related cache keys so public storefront updates immediately
        $this->clearStorefrontCache($config->slug);

        return back()->with('success', 'Review deleted successfully.');
    }

    /**
     * Clear all storefront cache keys for a given slug.
     * Must stay in sync with all cache keys used in PublicStorefrontController.
     */
    private function clearStorefrontCache(string $slug): void
    {
        \Cache::forget("storefront_v3_{$slug}");        // public show() page
        \Cache::forget("storefront_{$slug}");           // legacy key (kept for safety)
        \Cache::forget("storefront_review_ctx_{$slug}"); // review form context
    }
}
