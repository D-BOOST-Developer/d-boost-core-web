<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerAnalyticsController extends Controller
{
    /**
     * Display main analytics dashboard
     */
    public function dashboard()
    {
        $totalCustomers = Customer::count();

        // Prevent division by zero
        if ($totalCustomers == 0) {
            $totalCustomers = 1;
        }

        // Customer segmentation by value
        $segments = [
            'vip' => Customer::where('total_spent', '>=', 5000000)->count(),
            'high' => Customer::whereBetween('total_spent', [2000000, 4999999])->count(),
            'medium' => Customer::whereBetween('total_spent', [500000, 1999999])->count(),
            'low' => Customer::where('total_spent', '<', 500000)->count(),
        ];

        // Purchase frequency distribution
        $frequency = [
            'no_purchase' => Customer::where('purchase_frequency', 0)->count(),
            'low' => Customer::whereBetween('purchase_frequency', [1, 3])->count(),
            'medium' => Customer::whereBetween('purchase_frequency', [4, 7])->count(),
            'high' => Customer::where('purchase_frequency', '>=', 8)->count(),
        ];

        // Top 10 customers by revenue
        $topCustomers = Customer::orderBy('total_spent', 'desc')->take(10)->get();

        // Metrics for the dashboard
        $metrics = [
            'total_customers' => $totalCustomers,
            'new_this_month' => Customer::newCustomers(30)->count(),
            'total_revenue' => Customer::sum('total_spent'),
            'avg_lifetime_value' => round(Customer::avg('total_spent'), 2),
            'vip_customers' => $segments['vip'],
            'at_risk_customers' => Customer::atRisk()->count(),
        ];

        return view('customers.analytics.dashboard', compact(
            'metrics',
            'segments',
            'frequency',
            'topCustomers'
        ));
    }

    /**
     * RFM Analysis
     */
    public function rfmAnalysis()
    {
        $customers = Customer::with('loyaltyPoints')->get();

        // Calculate RFM scores for all customers
        $rfmData = $customers->map(function ($customer) {
            $rfm = $customer->rfm_score;
            return [
                'customer' => $customer,
                'recency' => $rfm['recency'],
                'frequency' => $rfm['frequency'],
                'monetary' => $rfm['monetary'],
                'total' => $rfm['total'],
            ];
        });

        // Group by RFM segments
        $segments = [
            'Champions' => $rfmData->filter(fn($c) => $c['total'] >= 13)->count(),
            'Loyal' => $rfmData->filter(fn($c) => $c['total'] >= 10 && $c['total'] < 13)->count(),
            'Potential' => $rfmData->filter(fn($c) => $c['total'] >= 7 && $c['total'] < 10)->count(),
            'At Risk' => $rfmData->filter(fn($c) => $c['total'] >= 4 && $c['total'] < 7)->count(),
            'Lost' => $rfmData->filter(fn($c) => $c['total'] < 4)->count(),
        ];

        return view('customers.analytics.rfm', compact('rfmData', 'segments'));
    }

    /**
     * Cohort Retention Analysis
     */
    public function cohortAnalysis()
    {
        // Group customers by registration month
        $cohorts = Customer::selectRaw('DATE_TRUNC(\'month\', created_at) as cohort_month')
            ->groupBy('cohort_month')
            ->orderBy('cohort_month', 'desc')
            ->take(12)
            ->get();

        $cohortData = [];
        
        foreach ($cohorts as $index => $cohort) {
            $cohortMonth = $cohort->cohort_month;
            $customersInCohort = Customer::whereRaw('DATE_TRUNC(\'month\', created_at) = ?', [$cohortMonth])->pluck('id');
            
            $cohortData[] = [
                'month' => $cohortMonth,
                'size' => $customersInCohort->count(),
                'retention' => [], // Calculate retention for each subsequent month
            ];
        }

        return view('customers.analytics.cohort', compact('cohortData'));
    }

    /**
     * Churn Analysis
     */
    public function churnAnalysis()
    {
        $customers = Customer::all();

        // Calculate churn risk for all customers
        $churnData = $customers->map(function ($customer) {
            return [
                'customer' => $customer,
                'churn_risk' => $customer->churn_risk,
                'last_purchase_days' => $customer->last_purchase_date 
                    ? now()->diffInDays($customer->last_purchase_date) 
                    : 9999,
            ];
        });

        // Group by risk level
        $riskDistribution = [
            'High Risk (>70%)' => $churnData->filter(fn($c) => $c['churn_risk'] > 70)->count(),
            'Medium Risk (40-70%)' => $churnData->filter(fn($c) => $c['churn_risk'] >= 40 && $c['churn_risk'] <= 70)->count(),
            'Low Risk (<40%)' => $churnData->filter(fn($c) => $c['churn_risk'] < 40)->count(),
        ];

        // Top at-risk customers
        $atRiskCustomers = $churnData->sortByDesc('churn_risk')->take(20);

        return view('customers.analytics.churn', compact('churnData', 'riskDistribution', 'atRiskCustomers'));
    }
}
