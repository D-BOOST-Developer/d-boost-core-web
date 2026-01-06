<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::with(['loyaltyPoints', 'segments']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // City filter
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        // Gender filter
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // CRM: Segment filter
        if ($request->filled('segment')) {
            $query->bySegment($request->segment);
        }

        // CRM: Customer value filter
        if ($request->filled('value')) {
            switch ($request->value) {
                case 'vip':
                    $query->where('total_spent', '>=', 5000000);
                    break;
                case 'high':
                    $query->whereBetween('total_spent', [2000000, 4999999]);
                    break;
                case 'medium':
                    $query->whereBetween('total_spent', [500000, 1999999]);
                    break;
                case 'low':
                    $query->where('total_spent', '<', 500000);
                    break;
            }
        }

        // CRM: Churn risk filter
        if ($request->filled('churn_risk')) {
            if ($request->churn_risk === 'high') {
                $query->atRisk();
            }
        }

        // CRM: Loyalty tier filter
        if ($request->filled('loyalty_tier')) {
            $query->byLoyaltyTier($request->loyalty_tier);
        }

        $customers = $query->orderBy('total_spent', 'desc')->paginate(15);
        
        // Get filter data
        $cities = Customer::distinct()->whereNotNull('city')->pluck('city')->filter();
        $segments = \App\Models\CustomerSegment::active()->get();

        // CRM: Statistics
        $stats = [
            'total' => Customer::count(),
            'vip' => Customer::where('total_spent', '>=', 5000000)->count(),
            'at_risk' => Customer::atRisk()->count(),
            'avg_lifetime_value' => Customer::avg('total_spent') ?? 0,
        ];

        return view('customers.index', compact('customers', 'cities', 'segments', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:120',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date|before:today',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['total_spent'] = 0;
        $validated['purchase_frequency'] = 0;

        $customer = Customer::create($validated);

        // Create loyalty points record for new customer
        if (!$customer->loyaltyPoints) {
            $customer->loyaltyPoints()->create([
                'loyalty_program_id' => \App\Models\CustomerLoyaltyProgram::first()?->id,
                'points_earned' => 0,
                'points_redeemed' => 0,
                'points_expired' => 0,
                'current_balance' => 0,
                'tier_level' => 'bronze',
            ]);
        }

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        // Load all relationships including CRM data
        $customer->load([
            'sales.salesDetails.product',
            'interactions' => function ($query) {
                $query->latest('interaction_date')->take(10);
            },
            'segments',
            'loyaltyPoints',
            'feedbacks.product',
            'wishlists.product',
            'notes' => function ($query) {
                $query->latest()->take(10);
            },
            'addresses',
        ]);
        
        // Get customer statistics
        $totalOrders = $customer->sales()->count();
        $totalSpent = $customer->total_spent;
        $avgOrderValue = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;
        $lastOrderDate = $customer->last_purchase_date;

        // CRM: Analytics
        $rfmScore = $customer->rfm_score;
        $customerValueSegment = $customer->customer_value_segment;
        $churnRisk = $customer->churn_risk;
        $loyaltyTier = $customer->loyalty_tier;
        $loyaltyPoints = $customer->total_loyalty_points;
        $recommendedProducts = $customer->recommended_products;

        // CRM: Recent activity
        $recentInteractions = $customer->interactions;
        $recentFeedback = $customer->feedbacks()->latest()->take(5)->get();
        $activeWishlists = $customer->wishlists;
        $pinnedNotes = $customer->notes()->pinned()->get();
        $recentNotes = $customer->notes()->latest()->take(5)->get();

        return view('customers.show', compact(
            'customer',
            'totalOrders',
            'totalSpent',
            'avgOrderValue',
            'lastOrderDate',
            'rfmScore',
            'customerValueSegment',
            'churnRisk',
            'loyaltyTier',
            'loyaltyPoints',
            'recommendedProducts',
            'recentInteractions',
            'recentFeedback',
            'activeWishlists',
            'pinnedNotes',
            'recentNotes'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'age' => 'nullable|integer|min:1|max:120',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date|before:today',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer berhasil dihapus.');
    }

    // ==================== CRM METHODS ====================

    /**
     * Add a note to customer
     */
    public function addNote(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'note_type' => 'required|in:general,important,warning,reminder',
            'content' => 'required|string',
            'is_pinned' => 'boolean',
        ]);

        $validated['is_pinned'] = $request->boolean('is_pinned', false);
        $customer->notes()->create($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Catatan berhasil ditambahkan.');
    }

    /**
     * Add product to customer wishlist
     */
    public function addToWishlist(Request $request, Customer $customer, $productId)
    {
        $validated = $request->validate([
            'priority' => 'nullable|in:low,medium,high',
            'notes' => 'nullable|string',
            'notify_on_sale' => 'boolean',
            'notify_on_stock' => 'boolean',
        ]);

        $validated['notify_on_sale'] = $request->boolean('notify_on_sale', false);
        $validated['notify_on_stock'] = $request->boolean('notify_on_stock', false);

        $customer->wishlists()->updateOrCreate(
            ['product_id' => $productId],
            $validated
        );

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Produk berhasil ditambahkan ke wishlist.');
    }

    /**
     * Remove product from customer wishlist
     */
    public function removeFromWishlist(Customer $customer, $wishlistId)
    {
        $customer->wishlists()->where('id', $wishlistId)->delete();

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Produk berhasil dihapus dari wishlist.');
    }

    /**
     * Export customers to Excel/CSV
     */
    public function export(Request $request)
    {
        // This is a simplified version. In production, use Laravel Excel or similar
        $customers = Customer::with(['loyaltyPoints', 'segments'])->get();

        $csv = "Name,Email,Phone,City,Total Spent,Purchase Frequency,Loyalty Tier,Customer Value,Churn Risk\n";
        
        foreach ($customers as $customer) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%d,%s,%s,%d%%\n",
                $customer->name,
                $customer->email ?? '',
                $customer->phone ?? '',
                $customer->city ?? '',
                $customer->total_spent,
                $customer->purchase_frequency,
                $customer->loyalty_tier,
                $customer->customer_value_segment,
                $customer->churn_risk
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customers-' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Get customer statistics for dashboard
     */
    public function getStatistics()
    {
        return [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::where('is_active', true)->count(),
            'vip_customers' => Customer::where('total_spent', '>=', 5000000)->count(),
            'at_risk_customers' => Customer::atRisk()->count(),
            'new_customers_this_month' => Customer::where('created_at', '>=', now()->startOfMonth())->count(),
            'avg_lifetime_value' => Customer::avg('total_spent') ?? 0,
            'total_revenue' => Customer::sum('total_spent'),
            'avg_purchase_frequency' => Customer::avg('purchase_frequency') ?? 0,
        ];
    }

    /**
     * Bulk update customer status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:customers,id',
            'is_active' => 'required|boolean',
        ]);

        Customer::whereIn('id', $validated['customer_ids'])
            ->update(['is_active' => $validated['is_active']]);

        return redirect()->route('customers.index')
            ->with('success', 'Status pelanggan berhasil diperbarui.');
    }

    /**
     * Bulk assign to segment
     */
    public function bulkAssignSegment(Request $request)
    {
        $validated = $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:customers,id',
            'segment_id' => 'required|exists:customer_segments,id',
        ]);

        $segment = \App\Models\CustomerSegment::findOrFail($validated['segment_id']);
        
        foreach ($validated['customer_ids'] as $customerId) {
            $segment->customers()->attach($customerId);
        }

        return redirect()->route('customers.index')
            ->with('success', 'Pelanggan berhasil ditambahkan ke segmen.');
    }

    /**
     * Get customer interactions summary
     */
    public function getInteractionsSummary(Customer $customer)
    {
        return [
            'total_interactions' => $customer->interactions()->count(),
            'pending_interactions' => $customer->interactions()->where('status', 'pending')->count(),
            'overdue_interactions' => $customer->interactions()->overdue()->count(),
            'upcoming_followups' => $customer->interactions()->upcomingFollowUps()->count(),
            'last_interaction' => $customer->interactions()->latest('interaction_date')->first(),
        ];
    }

    /**
     * Get customer feedback summary
     */
    public function getFeedbackSummary(Customer $customer)
    {
        $feedbacks = $customer->feedbacks();
        
        return [
            'total_feedback' => $feedbacks->count(),
            'avg_rating' => $feedbacks->avg('rating') ?? 0,
            'positive_feedback' => $feedbacks->positive()->count(),
            'negative_feedback' => $feedbacks->negative()->count(),
            'pending_responses' => $feedbacks->pending()->count(),
        ];
    }

}
