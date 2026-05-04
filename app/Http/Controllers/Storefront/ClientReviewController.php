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

        // Clear cache
        \Cache::forget("storefront_{$config->slug}");

        return back()->with('success', 'Review approved successfully.');
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

        // Clear cache
        \Cache::forget("storefront_{$config->slug}");

        return back()->with('success', 'Review rejected successfully.');
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

        // Clear cache
        \Cache::forget("storefront_{$config->slug}");

        return back()->with('success', 'Review deleted successfully.');
    }
}
