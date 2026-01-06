<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerLoyaltyPoint;
use App\Models\CustomerLoyaltyProgram;
use Illuminate\Http\Request;

class CustomerLoyaltyController extends Controller
{
    /**
     * Display loyalty program dashboard
     */
    public function index()
    {
        $program = CustomerLoyaltyProgram::active()->first();
        
        // Tier distribution
        $tierDistribution = CustomerLoyaltyPoint::selectRaw('tier_level, COUNT(*) as count')
            ->groupBy('tier_level')
            ->get()
            ->pluck('count', 'tier_level')
            ->toArray();

        // Loyalty points with pagination
        $loyaltyPoints = CustomerLoyaltyPoint::with('customer')
            ->orderBy('current_balance', 'desc')
            ->paginate(20);

        // Statistics
        $stats = [
            'total_members' => CustomerLoyaltyPoint::count(),
            'total_points_issued' => CustomerLoyaltyPoint::sum('points_earned'),
            'total_points_redeemed' => CustomerLoyaltyPoint::sum('points_redeemed'),
            'active_points' => CustomerLoyaltyPoint::sum('current_balance'),
            'bronze_members' => $tierDistribution['bronze'] ?? 0,
            'silver_members' => $tierDistribution['silver'] ?? 0,
            'gold_members' => $tierDistribution['gold'] ?? 0,
            'platinum_members' => $tierDistribution['platinum'] ?? 0,
        ];

        return view('customers.loyalty.index', compact('program', 'tierDistribution', 'loyaltyPoints', 'stats'));
    }

    /**
     * Display leaderboard
     */
    public function leaderboard()
    {
        $customers = CustomerLoyaltyPoint::with('customer')
            ->orderBy('current_balance', 'desc')
            ->paginate(50);

        return view('customers.loyalty.leaderboard', compact('customers'));
    }

    /**
     * Display customer's loyalty points
     */
    public function customerPoints(Customer $customer)
    {
        $loyaltyPoints = $customer->loyaltyPoints;
        
        if (!$loyaltyPoints) {
            // Create loyalty points record if doesn't exist
            $program = CustomerLoyaltyProgram::active()->first();
            $loyaltyPoints = CustomerLoyaltyPoint::create([
                'customer_id' => $customer->id,
                'loyalty_program_id' => $program?->id,
                'points_earned' => 0,
                'points_redeemed' => 0,
                'current_balance' => 0,
                'tier_level' => 'bronze',
            ]);
        }

        $loyaltyPoints->load('loyaltyProgram');
        
        // Get tier benefits
        $program = $loyaltyPoints->loyaltyProgram;
        $tierBenefits = $program ? $program->getTierBenefits($loyaltyPoints->tier_level) : [];

        return view('customers.loyalty.customer-points', compact('customer', 'loyaltyPoints', 'tierBenefits', 'program'));
    }

    /**
     * Manually adjust customer points
     */
    public function adjustPoints(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'points' => 'required|numeric',
            'action' => 'required|in:add,redeem',
            'reason' => 'required|string|max:255',
        ]);

        $loyaltyPoints = $customer->loyaltyPoints;
        
        if (!$loyaltyPoints) {
            return redirect()->back()
                ->with('error', 'Customer does not have a loyalty account.');
        }

        try {
            if ($validated['action'] === 'add') {
                $loyaltyPoints->addPoints($validated['points'], $validated['reason']);
                $message = 'Points added successfully.';
            } else {
                $loyaltyPoints->redeemPoints($validated['points'], $validated['reason']);
                $message = 'Points redeemed successfully.';
            }

            return redirect()->route('customers.loyalty.points', $customer)
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
