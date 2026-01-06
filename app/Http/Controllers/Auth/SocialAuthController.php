<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user exists by google_id
            $user = User::where('google_id', $googleUser->getId())->first();
            
            if ($user) {
                // User exists, log them in
                Auth::login($user);
                return redirect()->intended('/dashboard');
            }
            
            // Check if user exists by email
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // User exists with same email, link Google account
                $user->update([
                    'google_id' => $googleUser->getId(),
                ]);
                
                // Assign 'users' role if user doesn't have any roles
                $this->ensureUserHasRole($user);
                
                Auth::login($user);
                return redirect()->intended('/dashboard');
            }
            
            // Create new user with transaction to ensure role assignment
            DB::beginTransaction();
            try {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(24)), // Random password for OAuth users
                    'role' => 'owner', // Legacy role for backward compatibility
                    'is_active' => true,
                ]);
                
                // Assign 'users' role in the modular role system
                $this->assignDefaultRole($user);
                
                DB::commit();
                
                Auth::login($user);
                
                // Redirect to onboarding for new users
                return redirect()->route('onboarding.show');
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('register')
                ->with('error', 'Failed to authenticate with Google. Please try again.');
        }
    }
    
    /**
     * Assign default 'users' role to new user
     */
    private function assignDefaultRole(User $user)
    {
        $usersRole = Role::where('name', 'users')->where('is_active', true)->first();
        
        if ($usersRole) {
            $user->assignRole($usersRole->id);
        }
    }
    
    /**
     * Ensure user has at least the 'users' role
     */
    private function ensureUserHasRole(User $user)
    {
        // Check if user has any active roles
        if ($user->activeRoles()->count() === 0) {
            $this->assignDefaultRole($user);
        }
    }
}
