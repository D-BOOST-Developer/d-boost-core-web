<?php

namespace App\Services;

use App\Models\User;
use App\Models\Module;
use App\Models\Menu;
use App\Models\UmkmModulePreference;
use Illuminate\Support\Collection;

class PersonalizationService
{
    /**
     * Get recommended modules for a UMKM category
     * 
     * @param string $categoryCode
     * @return Collection
     */
    public function getRecommendedModules(string $categoryCode): Collection
    {
        $recommendedNames = $this->getRecommendationsByCategory($categoryCode);
        
        return Module::active()
            ->whereIn('name', $recommendedNames)
            ->ordered()
            ->get();
    }

    /**
     * Get prioritized menus for a user based on their preferences
     * 
     * @param User $user
     * @return Collection
     */
    public function getPrioritizedMenus(User $user): Collection
    {
        // Get user's module preferences
        $modulePreferences = $user->modulePreferences()
            ->with('module')
            ->byPriority()
            ->get();

        if ($modulePreferences->isEmpty()) {
            // No preferences, return default menu order
            return Menu::active()
                ->with('module')
                ->orderBy('sort_order')
                ->get();
        }

        // Get all accessible menus
        $menus = Menu::active()
            ->with('module')
            ->get();

        // Sort menus based on module priority
        return $menus->sortBy(function ($menu) use ($modulePreferences) {
            $preference = $modulePreferences->firstWhere('module_id', $menu->module_id);
            
            if ($preference) {
                return $preference->priority_level * 1000 + $menu->sort_order;
            }
            
            return 999000 + $menu->sort_order; // Low priority for non-preferred modules
        })->values();
    }

    /**
     * Get contextual insights for user's business stage
     * 
     * @param User $user
     * @return array
     */
    public function getInsights(User $user): array
    {
        $profile = $user->businessProfile;
        
        if (!$profile) {
            return [];
        }

        $categoryCode = $profile->umkmCategory->code ?? 'STARTING';
        
        $insights = [
            'STARTING' => [
                'focus' => 'Build your foundation',
                'tips' => [
                    'Start by recording all your products and inventory',
                    'Track every sale to understand your revenue patterns',
                    'Build your customer database from day one',
                    'Keep expenses organized for better financial clarity',
                ],
                'next_steps' => [
                    'Complete your product catalog',
                    'Set up basic inventory tracking',
                    'Create customer profiles',
                ],
            ],
            'STAGNANT' => [
                'focus' => 'Optimize and diagnose',
                'tips' => [
                    'Analyze your sales trends to identify slow periods',
                    'Review customer behavior to improve retention',
                    'Identify your most and least profitable products',
                    'Look for operational inefficiencies',
                ],
                'next_steps' => [
                    'Run customer analytics reports',
                    'Review profit margins by product',
                    'Identify cost-saving opportunities',
                ],
            ],
            'GROWING' => [
                'focus' => 'Scale strategically',
                'tips' => [
                    'Use advanced analytics to forecast growth',
                    'Segment customers for targeted marketing',
                    'Monitor KPIs regularly',
                    'Plan for inventory scaling',
                ],
                'next_steps' => [
                    'Set up automated reports',
                    'Create customer segments',
                    'Build marketing campaigns',
                ],
            ],
        ];

        return $insights[$categoryCode] ?? $insights['STARTING'];
    }

    /**
     * Get peer comparison data (businesses in same category)
     * 
     * @param User $user
     * @return array
     */
    public function getPeerComparison(User $user): array
    {
        $profile = $user->businessProfile;
        
        if (!$profile) {
            return [];
        }

        // Get similar businesses (same category and business category)
        $peers = \App\Models\UserBusinessProfile::where('umkm_category_id', $profile->umkm_category_id)
            ->where('business_category', $profile->business_category)
            ->where('user_id', '!=', $user->id)
            ->where('onboarding_completed', true)
            ->count();

        return [
            'total_peers' => $peers,
            'category' => $profile->umkmCategory->name ?? 'Unknown',
            'business_type' => $profile->business_category,
        ];
    }

    /**
     * Get module recommendations by category
     * 
     * @param string $categoryCode
     * @return array
     */
    private function getRecommendationsByCategory(string $categoryCode): array
    {
        $recommendations = [
            'STARTING' => [
                'products',
                'sales',
                'customers',
                'inventory',
                'expenses',
            ],
            'STAGNANT' => [
                'analytics',
                'customers',
                'reports',
                'finance',
                'crm',
                'products',
            ],
            'GROWING' => [
                'analytics',
                'reports',
                'finance',
                'crm',
                'marketing',
                'advanced-analytics',
                'storefront',
            ],
        ];

        return $recommendations[$categoryCode] ?? $recommendations['STARTING'];
    }

    /**
     * Get dashboard widgets personalized for user
     * 
     * @param User $user
     * @return array
     */
    public function getPersonalizedWidgets(User $user): array
    {
        $profile = $user->businessProfile;
        
        if (!$profile) {
            return $this->getDefaultWidgets();
        }

        $categoryCode = $profile->umkmCategory->code ?? 'STARTING';

        $widgets = [
            'STARTING' => [
                'quick_actions' => ['add_product', 'record_sale', 'add_customer'],
                'key_metrics' => ['total_sales', 'product_count', 'customer_count'],
                'focus_areas' => ['inventory_setup', 'customer_database'],
            ],
            'STAGNANT' => [
                'quick_actions' => ['view_analytics', 'customer_insights', 'expense_review'],
                'key_metrics' => ['sales_trend', 'customer_retention', 'profit_margin'],
                'focus_areas' => ['sales_optimization', 'cost_reduction'],
            ],
            'GROWING' => [
                'quick_actions' => ['advanced_reports', 'marketing_campaign', 'forecast'],
                'key_metrics' => ['growth_rate', 'customer_lifetime_value', 'roi'],
                'focus_areas' => ['scaling_strategy', 'market_expansion'],
            ],
        ];

        return $widgets[$categoryCode] ?? $widgets['STARTING'];
    }

    /**
     * Get default widgets for users without profile
     * 
     * @return array
     */
    private function getDefaultWidgets(): array
    {
        return [
            'quick_actions' => ['add_product', 'record_sale'],
            'key_metrics' => ['total_sales', 'product_count'],
            'focus_areas' => ['getting_started'],
        ];
    }
}
