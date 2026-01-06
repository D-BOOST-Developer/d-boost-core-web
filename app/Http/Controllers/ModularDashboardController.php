<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NavigationService;
use App\Models\Module;
use App\Models\Menu;
use App\Models\Role;

class ModularDashboardController extends Controller
{
    protected $navigationService;

    public function __construct(NavigationService $navigationService)
    {
        $this->navigationService = $navigationService;
    }

    /**
     * Display the modular dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get user's accessible modules with their menus
        $accessibleModules = $this->getUserAccessibleModules($user);
        
        // Get recent activities or stats for each module
        $moduleStats = $this->getModuleStats($user);
        
        return view('modular-dashboard.index', compact('accessibleModules', 'moduleStats'));
    }

    /**
     * Get modules accessible by user with their menus
     */
    private function getUserAccessibleModules($user)
    {
        $navigation = $this->navigationService->getNavigationForUser($user);
        
        $modules = [];
        foreach ($navigation as $moduleData) {
            $module = $moduleData['module'];
            $menus = $moduleData['menus'];
            
            $modules[] = [
                'id' => $module->id,
                'name' => $module->name,
                'display_name' => $module->display_name,
                'description' => $module->description,
                'icon' => $module->icon,
                'color' => $module->color,
                'sort_order' => $module->sort_order,
                'menus' => $menus->map(function($menu) {
                    return [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'display_name' => $menu->display_name,
                        'description' => $menu->description,
                        'route' => $menu->route,
                        'url' => $menu->getUrl(),
                        'icon' => $menu->icon,
                    ];
                })
            ];
        }

        // Ensure Settings module appears for super admins even if missing from role menus
        if ($user->isSuperAdmin() && !collect($modules)->pluck('name')->contains('settings')) {
            $settingsModule = Module::where('name', 'settings')->where('is_active', true)->first();
            if ($settingsModule) {
                $settingsMenus = Menu::where('module_id', $settingsModule->id)
                                     ->where('is_active', true)
                                     ->orderBy('sort_order')
                                     ->get();

                if ($settingsMenus->isNotEmpty()) {
                    $modules[] = [
                        'id' => $settingsModule->id,
                        'name' => $settingsModule->name,
                        'display_name' => $settingsModule->display_name,
                        'description' => $settingsModule->description,
                        'icon' => $settingsModule->icon,
                        'color' => $settingsModule->color,
                        'sort_order' => $settingsModule->sort_order,
                        'menus' => $settingsMenus->map(function($menu) {
                            return [
                                'id' => $menu->id,
                                'name' => $menu->name,
                                'display_name' => $menu->display_name,
                                'description' => $menu->description,
                                'route' => $menu->route,
                                'url' => $menu->getUrl(),
                                'icon' => $menu->icon,
                            ];
                        })
                    ];
                }
            }
        }
        
        return collect($modules)->sortBy('sort_order')->values();
    }

    /**
     * Get statistics for each module
     */
    private function getModuleStats($user)
    {
        $stats = [];
        
        // Dashboard stats
        $stats['dashboard'] = [
            'total_sales' => \App\Models\Sale::count(),
            'total_customers' => \App\Models\Customer::count(),
            'total_products' => \App\Models\Product::count(),
            'last_activity' => \App\Models\Sale::latest()->first()?->created_at?->diffForHumans() ?? 'No activity',
            'status' => 'active',
            'health_score' => 95
        ];
        
        // Inventory stats
        $stats['inventory'] = [
            'total_products' => \App\Models\Product::count(),
            'low_stock' => \App\Models\Product::where('stock_quantity', '<', 10)->count(),
            'out_of_stock' => \App\Models\Product::where('stock_quantity', 0)->count(),
            'last_update' => \App\Models\Product::latest('updated_at')->first()?->updated_at?->diffForHumans() ?? 'No updates',
            'status' => 'active',
            'health_score' => \App\Models\Product::where('stock_quantity', 0)->count() > 0 ? 75 : 90
        ];
        
        // Finance stats
        $monthlyRevenue = \App\Models\Sale::whereMonth('created_at', now()->month)->sum('total_amount');
        $monthlyExpenses = \App\Models\Expense::whereMonth('created_at', now()->month)->sum('amount');
        $stats['finance'] = [
            'total_sales' => \App\Models\Sale::sum('total_amount'),
            'total_expenses' => \App\Models\Expense::sum('amount'),
            'monthly_profit' => $monthlyRevenue - $monthlyExpenses,
            'last_transaction' => \App\Models\Sale::latest()->first()?->created_at?->diffForHumans() ?? 'No transactions',
            'status' => 'active',
            'health_score' => $monthlyRevenue > $monthlyExpenses ? 85 : 65
        ];
        
        // Customer stats
        $stats['customer'] = [
            'total_customers' => \App\Models\Customer::count(),
            'new_this_month' => \App\Models\Customer::whereMonth('created_at', now()->month)->count(),
            'active_customers' => \App\Models\Customer::where('is_active', true)->count(),
            'last_added' => \App\Models\Customer::latest()->first()?->created_at?->diffForHumans() ?? 'No customers',
            'status' => 'active',
            'health_score' => 88
        ];
        
        // Audit logs stats (if accessible)
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            $todayLogs = \App\Models\AuditLog::whereDate('performed_at', today())->count();
            $stats['audit_logs'] = [
                'total_logs' => \App\Models\AuditLog::count(),
                'today_activities' => $todayLogs,
                'this_week' => \App\Models\AuditLog::whereBetween('performed_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'last_activity' => \App\Models\AuditLog::latest('performed_at')->first()?->performed_at?->diffForHumans() ?? 'No activity',
                'status' => 'active',
                'health_score' => $todayLogs > 0 ? 92 : 75
            ];
        }
        
        // Reports stats
        $stats['reports'] = [
            'total_reports' => 25, // Mock data
            'monthly_reports' => 8,
            'generated_today' => 3,
            'last_generated' => 'Yesterday',
            'status' => 'active',
            'health_score' => 88
        ];
        
        // Marketing stats
        $stats['marketing'] = [
            'active_campaigns' => 5,
            'total_campaigns' => 12,
            'conversion_rate' => '12.5%',
            'last_campaign' => '2 days ago',
            'status' => 'active',
            'health_score' => 85
        ];
        
        // Suppliers stats
        $stats['suppliers'] = [
            'total_suppliers' => 18,
            'active_suppliers' => 15,
            'pending_orders' => 4,
            'last_order' => '3 hours ago',
            'status' => 'active',
            'health_score' => 92
        ];
        
        // HR stats
        $stats['hr'] = [
            'total_employees' => 12,
            'active_employees' => 11,
            'on_leave' => 1,
            'last_update' => '1 day ago',
            'status' => 'active',
            'health_score' => 95
        ];
        
        // Notifications stats
        $stats['notifications'] = [
            'total_notifications' => 45,
            'unread_notifications' => 8,
            'today_notifications' => 12,
            'last_notification' => '5 minutes ago',
            'status' => 'active',
            'health_score' => 90
        ];
        
        // Analytics stats
        $stats['analytics'] = [
            'data_points' => 1250,
            'active_reports' => 6,
            'insights_generated' => 23,
            'last_analysis' => '30 minutes ago',
            'status' => 'active',
            'health_score' => 94
        ];

        // Settings stats (if accessible)
        if ($user->isSuperAdmin()) {
            $activeModules = Module::where('is_active', true)->count();
            $totalModules = Module::count();
            $stats['settings'] = [
                'total_modules' => $totalModules,
                'active_modules' => $activeModules,
                'total_menus' => Menu::count(),
                'total_roles' => Role::count(),
                'last_update' => Module::latest('updated_at')->first()?->updated_at?->diffForHumans() ?? 'No updates',
                'status' => 'active',
                'health_score' => $totalModules > 0 ? (($activeModules / $totalModules) * 100) : 100
            ];
        }
        
        return $stats;
    }

    /**
     * Get module details for AJAX requests
     */
    public function getModuleDetails($moduleId)
    {
        $user = auth()->user();
        $module = Module::with(['menus' => function($query) use ($user) {
            $query->whereHas('roleMenus', function($q) use ($user) {
                $roleIds = $user->activeRoles()->pluck('roles.id');
                $q->whereIn('role_id', $roleIds)->where('is_active', true);
            })->where('is_active', true)->orderBy('sort_order');
        }])->findOrFail($moduleId);
        
        // Check if user can access this module
        if (!$user->canAccessMenu($module->menus->first()->id ?? null, 'read')) {
            abort(403, 'Unauthorized access to this module.');
        }
        
        return response()->json([
            'module' => $module,
            'stats' => $this->getModuleStats($user)[$module->name] ?? []
        ]);
    }
}
