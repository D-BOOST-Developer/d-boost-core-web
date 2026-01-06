<?php

namespace App\Http\Controllers;

use App\Http\Requests\OnboardingRequest;
use App\Services\OnboardingService;
use App\Services\PersonalizationService;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Exception;

class OnboardingController extends Controller
{
    protected OnboardingService $onboardingService;
    protected PersonalizationService $personalizationService;

    public function __construct(
        OnboardingService $onboardingService,
        PersonalizationService $personalizationService
    ) {
        $this->middleware('auth');
        $this->onboardingService = $onboardingService;
        $this->personalizationService = $personalizationService;
    }

    /**
     * Display the onboarding form
     */
    public function show(): View|RedirectResponse
    {
        $user = auth()->user();

        // If already completed, redirect to dashboard
        if ($this->onboardingService->canSkipOnboarding($user)) {
            return redirect()->route('dashboard')
                ->with('info', 'Anda sudah menyelesaikan onboarding.');
        }

        // Get user's role IDs
        $roleIds = $user->activeRoles()->pluck('roles.id');

        // Get modules accessible by user's roles
        // Using groupBy instead of distinct to avoid PostgreSQL JSON column error
        $modules = Module::active()
            ->whereHas('menus.roleMenus', function($query) use ($roleIds) {
                $query->whereIn('role_id', $roleIds);
            })
            ->groupBy('modules.id')
            ->ordered()
            ->get();

        // Get business category options
        $businessCategories = $this->getBusinessCategoryOptions();

        // Get revenue range options
        $revenueRanges = $this->getRevenueRangeOptions();

        return view('onboarding.show', compact('modules', 'businessCategories', 'revenueRanges'));
    }

    /**
     * Process onboarding submission
     */
    public function store(OnboardingRequest $request)
    {
        \Log::info('=== ONBOARDING STORE START ===');
        \Log::info('Request Data:', $request->all());
        
        try {
            $user = auth()->user();
            \Log::info('User ID: ' . $user->id);
            \Log::info('User Email: ' . $user->email);

            // Process onboarding data
            \Log::info('Starting onboarding service processing...');
            $validatedData = $request->validated();
            \Log::info('Validated Data:', $validatedData);
            
            $profile = $this->onboardingService->processOnboarding($user, $validatedData);
            \Log::info('Profile created successfully:', ['profile_id' => $profile->id]);

            // Get personalized insights
            \Log::info('Getting personalized insights...');
            $insights = $this->personalizationService->getInsights($user);
            \Log::info('Insights retrieved:', ['insights_count' => count($insights)]);

            \Log::info('=== ONBOARDING STORE SUCCESS ===');
            
            // Set flash session flag to trigger welcome tour on next request
            session()->flash('show_welcome_tour', true);
            \Log::info('Welcome tour flash session flag set');
            
            return response()->json([
                'success' => true,
                'message' => 'Selamat datang! Profil bisnis Anda telah berhasil disimpan.',
                'redirect_url' => route('dashboard'),
                'insights' => $insights
            ], 200);

        } catch (\Exception $e) {
            \Log::error('=== ONBOARDING STORE ERROR ===');
            \Log::error('Error Message: ' . $e->getMessage());
            \Log::error('Error File: ' . $e->getFile());
            \Log::error('Error Line: ' . $e->getLine());
            \Log::error('Stack Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'errors' => []
            ], 422);
        }
    }

    /**
     * Get business category options
     */
    private function getBusinessCategoryOptions(): array
    {
        return [
            'Retail' => 'Retail / Toko',
            'F&B' => 'Makanan & Minuman',
            'Fashion' => 'Fashion & Pakaian',
            'Services' => 'Jasa / Layanan',
            'Manufacturing' => 'Manufaktur / Produksi',
            'Agriculture' => 'Pertanian',
            'Technology' => 'Teknologi',
            'Healthcare' => 'Kesehatan',
            'Education' => 'Pendidikan',
            'Beauty' => 'Kecantikan & Salon',
            'Automotive' => 'Otomotif',
            'Construction' => 'Konstruksi',
            'Trading' => 'Perdagangan',
            'Other' => 'Lainnya',
        ];
    }

    /**
     * Get revenue range options
     */
    private function getRevenueRangeOptions(): array
    {
        return [
            '< 5 Juta' => '< 5 Juta',
            '5 - 10 Juta' => '5 - 10 Juta',
            '10 - 25 Juta' => '10 - 25 Juta',
            '25 - 50 Juta' => '25 - 50 Juta',
            '50 - 100 Juta' => '50 - 100 Juta',
            '100 - 250 Juta' => '100 - 250 Juta',
            '250 - 500 Juta' => '250 - 500 Juta',
            '> 500 Juta' => '> 500 Juta',
        ];
    }
}
