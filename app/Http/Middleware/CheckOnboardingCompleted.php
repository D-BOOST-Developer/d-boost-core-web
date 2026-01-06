<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOnboardingCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip check if user is not authenticated
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Skip check for onboarding routes
        if ($request->routeIs('onboarding.*')) {
            return $next($request);
        }

        // Skip check for logout route
        if ($request->routeIs('logout')) {
            return $next($request);
        }

        // Check if user has completed onboarding
        if (!$user->hasCompletedOnboarding()) {
            return redirect()->route('onboarding.show')
                ->with('warning', 'Silakan lengkapi profil bisnis Anda terlebih dahulu.');
        }

        return $next($request);
    }
}
