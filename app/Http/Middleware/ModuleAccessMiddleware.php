<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Menu;
use App\Services\NavigationService;

class ModuleAccessMiddleware
{
    protected $navigationService;

    public function __construct(NavigationService $navigationService)
    {
        $this->navigationService = $navigationService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission = 'read'): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin has access to everything
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        $routeName = $request->route()->getName();
        
        // Check if user can access this route
        if (!$this->navigationService->canAccessRoute($user, $routeName)) {
            abort(403, 'You do not have permission to access this module.');
        }

        // Check specific permission if menu exists
        $menu = Menu::where('route', $routeName)->first();
        if ($menu && !$user->canAccessMenu($menu->id, $permission)) {
            abort(403, "You do not have '{$permission}' permission for this menu.");
        }

        return $next($request);
    }
}
