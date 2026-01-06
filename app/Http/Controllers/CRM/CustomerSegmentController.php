<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerSegment;
use Illuminate\Http\Request;

class CustomerSegmentController extends Controller
{
    /**
     * Display a listing of segments
     */
    public function index()
    {
        $segments = CustomerSegment::withCount('customers')
            ->orderBy('name')
            ->get();

        // Statistics
        $stats = [
            'total_segments' => $segments->count(),
            'auto_segments' => $segments->where('segment_type', 'auto')->count(),
            'manual_segments' => $segments->where('segment_type', 'manual')->count(),
            'total_customers' => Customer::count(),
        ];

        return view('customers.segments.index', compact('segments', 'stats'));
    }

    /**
     * Show the form for creating a new segment
     */
    public function create()
    {
        return view('customers.segments.create');
    }

    /**
     * Store a newly created segment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:customer_segments,name',
            'description' => 'nullable|string',
            'segment_type' => 'required|in:auto,manual',
            'criteria' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $segment = CustomerSegment::create($validated);

        // If auto segment, refresh members immediately
        if ($segment->segment_type === 'auto') {
            $segment->refreshMembers();
        }

        return redirect()->route('customers.segments.index')
            ->with('success', 'Segment created successfully.');
    }

    /**
     * Display the specified segment
     */
    public function show(CustomerSegment $segment)
    {
        $segment->loadCount('customers');
        $customers = $segment->customers()
            ->with('loyaltyPoints')
            ->paginate(20);

        // Segment statistics
        $stats = [
            'total_customers' => $segment->customers()->count(),
            'avg_total_spent' => $segment->customers()->avg('total_spent') ?? 0,
            'avg_purchase_frequency' => $segment->customers()->avg('purchase_frequency') ?? 0,
            'vip_customers' => $segment->customers()->where('total_spent', '>=', 5000000)->count(),
        ];

        return view('customers.segments.show', compact('segment', 'customers', 'stats'));
    }

    /**
     * Show the form for editing the specified segment
     */
    public function edit(CustomerSegment $segment)
    {
        return view('customers.segments.edit', compact('segment'));
    }

    /**
     * Update the specified segment
     */
    public function update(Request $request, CustomerSegment $segment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:customer_segments,name,' . $segment->id,
            'description' => 'nullable|string',
            'segment_type' => 'required|in:auto,manual',
            'criteria' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $segment->update($validated);

        // If auto segment, refresh members
        if ($segment->segment_type === 'auto') {
            $segment->refreshMembers();
        }

        return redirect()->route('customers.segments.index')
            ->with('success', 'Segment updated successfully.');
    }

    /**
     * Remove the specified segment
     */
    public function destroy(CustomerSegment $segment)
    {
        $segment->delete();

        return redirect()->route('customers.segments.index')
            ->with('success', 'Segment deleted successfully.');
    }

    /**
     * Refresh segment members (for auto segments)
     */
    public function refresh(CustomerSegment $segment)
    {
        if ($segment->segment_type !== 'auto') {
            return redirect()->back()
                ->with('error', 'Only auto segments can be refreshed.');
        }

        $segment->refreshMembers();

        return redirect()->route('customers.segments.show', $segment)
            ->with('success', 'Segment refreshed successfully. Members: ' . $segment->customer_count);
    }

    /**
     * Export segment members to CSV
     */
    public function export(CustomerSegment $segment)
    {
        $customers = $segment->customers()->with('loyaltyPoints')->get();

        $csv = "Name,Email,Phone,City,Total Spent,Purchase Frequency,Loyalty Tier\n";
        
        foreach ($customers as $customer) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%d,%s\n",
                $customer->name,
                $customer->email ?? '',
                $customer->phone ?? '',
                $customer->city ?? '',
                $customer->total_spent,
                $customer->purchase_frequency,
                $customer->loyalty_tier
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="segment-' . $segment->id . '-' . date('Y-m-d') . '.csv"',
        ]);
    }
}
