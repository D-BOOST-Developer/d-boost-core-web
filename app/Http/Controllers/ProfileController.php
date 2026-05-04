<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\UserBusinessProfile;
use App\Models\UmkmCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $businessProfile = $user->businessProfile;
        $umkmCategories = UmkmCategory::active()->get();
        $banks = UserBusinessProfile::getIndonesianBanks();

        return view('profile.edit', [
            'user' => $user,
            'businessProfile' => $businessProfile,
            'umkmCategories' => $umkmCategories,
            'banks' => $banks,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's business profile.
     */
    public function updateBusinessProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'business_category' => ['required', 'string', 'max:150'],
            'owner_name' => ['required', 'string', 'max:150'],
            'contact_email' => ['required', 'email', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'business_address' => ['nullable', 'string', 'max:1000'],
            'google_maps_link' => ['nullable', 'string', 'max:2000'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_holder' => ['nullable', 'string', 'max:150'],
        ]);

        $user = $request->user();
        $businessProfile = $user->businessProfile;

        if ($businessProfile) {
            $businessProfile->update($validated);
        } else {
            // Create new business profile if doesn't exist
            $validated['user_id'] = $user->id;
            $validated['umkm_category_id'] = UmkmCategory::first()->id ?? null;
            $validated['business_age_month'] = 0;
            $validated['monthly_revenue_range'] = '< 10M';
            UserBusinessProfile::create($validated);
        }

        return Redirect::route('profile.edit')->with('status', 'business-profile-updated');
    }

    /**
     * Upload business logo.
     */
    public function uploadLogo(Request $request): RedirectResponse
    {
        $request->validate([
            'business_logo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $businessProfile = $user->businessProfile;

        if (!$businessProfile) {
            return Redirect::route('profile.edit')->with('error', 'Business profile not found. Please complete onboarding first.');
        }

        // Delete old logo if exists
        if ($businessProfile->business_logo_path) {
            Storage::disk('public')->delete($businessProfile->business_logo_path);
        }

        // Store new logo
        $path = $request->file('business_logo')->store('business-logos', 'public');
        $businessProfile->update(['business_logo_path' => $path]);

        return Redirect::route('profile.edit')->with('status', 'logo-updated');
    }

    /**
     * Upload QRIS barcode.
     */
    public function uploadQris(Request $request): RedirectResponse
    {
        $request->validate([
            'qris_barcode' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $businessProfile = $user->businessProfile;

        if (!$businessProfile) {
            return Redirect::route('profile.edit')->with('error', 'Business profile not found. Please complete onboarding first.');
        }

        // Delete old QRIS if exists
        if ($businessProfile->qris_barcode_path) {
            Storage::disk('public')->delete($businessProfile->qris_barcode_path);
        }

        // Store new QRIS
        $path = $request->file('qris_barcode')->store('qris-barcodes', 'public');
        $businessProfile->update(['qris_barcode_path' => $path]);

        return Redirect::route('profile.edit')->with('status', 'qris-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Delete associated files
        if ($user->businessProfile) {
            if ($user->businessProfile->business_logo_path) {
                Storage::disk('public')->delete($user->businessProfile->business_logo_path);
            }
            if ($user->businessProfile->qris_barcode_path) {
                Storage::disk('public')->delete($user->businessProfile->qris_barcode_path);
            }
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    /**
     * API: Get user profile data.
     */
    public function apiShow(Request $request)
    {
        $user = $request->user();
        $businessProfile = $user->businessProfile;
        
        // Get dropdown data
        $umkmCategories = UmkmCategory::active()->select('id', 'name')->get();
        $banks = UserBusinessProfile::getIndonesianBanks();

        return response()->json([
            'user' => $user,
            'business_profile' => $businessProfile,
            'meta' => [
                'umkm_categories' => $umkmCategories,
                'banks' => $banks,
            ]
        ]);
    }

    /**
     * API: Update user profile information.
     */
    public function apiUpdate(ProfileUpdateRequest $request)
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $request->user(),
        ]);
    }

    /**
     * API: Update business profile information.
     */
    public function apiUpdateBusiness(Request $request)
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'business_category' => ['required', 'string', 'max:150'],
            'owner_name' => ['required', 'string', 'max:150'],
            'contact_email' => ['required', 'email', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'business_address' => ['nullable', 'string', 'max:1000'],
            'google_maps_link' => ['nullable', 'string', 'max:2000'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_holder' => ['nullable', 'string', 'max:150'],
        ]);

        $user = $request->user();
        $businessProfile = $user->businessProfile;

        if ($businessProfile) {
            $businessProfile->update($validated);
        } else {
            // Create new business profile if doesn't exist
            $validated['user_id'] = $user->id;
            $validated['umkm_category_id'] = UmkmCategory::first()->id ?? null; // Fallback or handle better
            $validated['business_age_month'] = 0;
            $validated['monthly_revenue_range'] = '< 10M';
            $businessProfile = UserBusinessProfile::create($validated);
        }

        return response()->json([
            'message' => 'Business profile updated successfully',
            'business_profile' => $user->fresh()->businessProfile,
        ]);
    }
    /**
     * API: Upload business logo.
     */
    public function apiUploadLogo(Request $request)
    {
        $request->validate([
            'business_logo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $businessProfile = $user->businessProfile;

        if (!$businessProfile) {
            return response()->json(['message' => 'Business profile not found'], 404);
        }

        if ($businessProfile->business_logo_path) {
            Storage::disk('public')->delete($businessProfile->business_logo_path);
        }

        $path = $request->file('business_logo')->store('business-logos', 'public');
        $businessProfile->update(['business_logo_path' => $path]);

        return response()->json([
            'message' => 'Logo updated successfully',
            'business_logo_url' => asset('storage/' . $path),
        ]);
    }

    /**
     * API: Upload QRIS barcode.
     */
    public function apiUploadQris(Request $request)
    {
        $request->validate([
            'qris_barcode' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $businessProfile = $user->businessProfile;

        if (!$businessProfile) {
            return response()->json(['message' => 'Business profile not found'], 404);
        }

        if ($businessProfile->qris_barcode_path) {
            Storage::disk('public')->delete($businessProfile->qris_barcode_path);
        }

        $path = $request->file('qris_barcode')->store('qris-barcodes', 'public');
        $businessProfile->update(['qris_barcode_path' => $path]);

        return response()->json([
            'message' => 'QRIS updated successfully',
            'qris_image_url' => asset('storage/' . $path),
        ]);
    }

    /**
     * API: Update password.
     */
    public function apiUpdatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $request->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    }

    /**
     * API: Delete account.
     */
    public function apiDestroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Delete associated files
        if ($user->businessProfile) {
            if ($user->businessProfile->business_logo_path) {
                Storage::disk('public')->delete($user->businessProfile->business_logo_path);
            }
            if ($user->businessProfile->qris_barcode_path) {
                Storage::disk('public')->delete($user->businessProfile->qris_barcode_path);
            }
        }

        $user->currentAccessToken()->delete(); // Revoke current token
        $user->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    }
}
