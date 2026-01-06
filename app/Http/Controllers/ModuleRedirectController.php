<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Services\NavigationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleRedirectController extends Controller
{
    /**
     * Redirect to first menu of a module
     */
    public function redirectToModule(string $moduleName, NavigationService $navigationService)
    {
        // Find module by name
        $module = Module::where('name', $moduleName)->first();
        
        if (!$module) {
            return redirect()->route('dashboard')->with('error', 'Module not found');
        }
        
        // Get first menu (lowest sort_order) for this module
        $firstMenu = $navigationService->getModuleNavigation(Auth::user(), $module)->first();
        
        if (!$firstMenu) {
            return redirect()->route('dashboard')->with('error', 'No accessible menus in this module');
        }
        
        // Redirect to the first menu's URL
        return redirect($firstMenu->getUrl());
    }
}
