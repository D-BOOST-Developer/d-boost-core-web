<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserBusinessProfile;
use App\Models\UmkmCategory;
use App\Models\UmkmModulePreference;
use App\Models\Module;
use Illuminate\Support\Facades\DB;
use Exception;

class OnboardingService
{
    /**
     * Process user onboarding data
     * 
     * @param User $user
     * @param array $data
     * @return UserBusinessProfile
     * @throws Exception
     */
    public function processOnboarding(User $user, array $data): UserBusinessProfile
    {
        \Log::info('=== ONBOARDING SERVICE START ===');
        \Log::info('Processing onboarding for user: ' . $user->id);
        \Log::info('Input data:', $data);
        
        DB::beginTransaction();
        
        try {
            // Determine UMKM category based on business metrics
            \Log::info('Determining UMKM category...');
            \Log::info('Business age (months): ' . $data['business_age_month']);
            \Log::info('Revenue range: ' . $data['monthly_revenue_range']);
            
            $revenueNumeric = $this->parseRevenueRange($data['monthly_revenue_range']);
            \Log::info('Parsed revenue numeric: ' . $revenueNumeric);
            
            $umkmCategory = $this->determineUmkmCategory(
                $data['business_age_month'],
                $revenueNumeric
            );

            if (!$umkmCategory) {
                \Log::error('UMKM category could not be determined!');
                \Log::info('Available categories:', \App\Models\UmkmCategory::all()->toArray());
                throw new Exception('Unable to determine UMKM category. Please check your business information.');
            }
            
            \Log::info('UMKM Category determined:', [
                'id' => $umkmCategory->id,
                'code' => $umkmCategory->code,
                'name' => $umkmCategory->name
            ]);

            // Create or update business profile
            \Log::info('Creating/updating business profile...');
            $profile = UserBusinessProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'business_name' => $data['business_name'],
                    'business_category' => $data['business_category'],
                    'business_logo_path' => $data['business_logo_path'] ?? null,
                    'business_age_month' => $data['business_age_month'],
                    'monthly_revenue_range' => $data['monthly_revenue_range'],
                    'umkm_category_id' => $umkmCategory->id,
                    'owner_name' => $data['owner_name'],
                    'contact_email' => $data['contact_email'],
                    'contact_phone' => $data['contact_phone'] ?? null,
                    'usage_reason' => $data['usage_reason'] ?? null,
                    'onboarding_completed' => true,
                ]
            );
            
            \Log::info('Profile created/updated:', ['profile_id' => $profile->id]);

            // Generate module preferences
            \Log::info('Generating module preferences...');
            $selectedModules = $data['selected_modules'] ?? [];
            \Log::info('Selected modules:', $selectedModules);
            
            $this->generateModulePreferences($user, $selectedModules, $umkmCategory);
            \Log::info('Module preferences generated successfully');

            DB::commit();
            \Log::info('=== ONBOARDING SERVICE SUCCESS ===');
            
            return $profile;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('=== ONBOARDING SERVICE ERROR ===');
            \Log::error('Error: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Generate module preferences for user
     * 
     * @param User $user
     * @param array $selectedModuleIds
     * @param UmkmCategory $umkmCategory
     * @return void
     */
    public function generateModulePreferences(User $user, array $selectedModuleIds, UmkmCategory $umkmCategory): void
    {
        // Clear existing preferences
        UmkmModulePreference::where('user_id', $user->id)->delete();

        $allModules = Module::active()->get();
        $recommendedModules = $this->getRecommendedModulesByCategory($umkmCategory->code);

        $priority = 1;

        // First, add user-selected modules with highest priority
        foreach ($selectedModuleIds as $moduleId) {
            $module = $allModules->firstWhere('id', $moduleId);
            if ($module) {
                UmkmModulePreference::create([
                    'user_id' => $user->id,
                    'module_id' => $moduleId,
                    'priority_level' => $priority++,
                    'is_recommended' => in_array($module->name, $recommendedModules),
                    'reason' => 'user_selected',
                ]);
            }
        }

        // Then, add system-recommended modules that weren't selected
        foreach ($allModules as $module) {
            if (!in_array($module->id, $selectedModuleIds) && in_array($module->name, $recommendedModules)) {
                UmkmModulePreference::create([
                    'user_id' => $user->id,
                    'module_id' => $module->id,
                    'priority_level' => $priority++,
                    'is_recommended' => true,
                    'reason' => 'system_suggestion',
                ]);
            }
        }

        // Finally, add remaining modules with lower priority
        foreach ($allModules as $module) {
            $exists = UmkmModulePreference::where('user_id', $user->id)
                ->where('module_id', $module->id)
                ->exists();

            if (!$exists) {
                UmkmModulePreference::create([
                    'user_id' => $user->id,
                    'module_id' => $module->id,
                    'priority_level' => $priority++,
                    'is_recommended' => false,
                    'reason' => 'default',
                ]);
            }
        }
    }

    /**
     * Determine UMKM category based on business metrics
     * 
     * @param int $businessAgeMonths
     * @param int $monthlyRevenue
     * @return UmkmCategory|null
     */
    public function determineUmkmCategory(int $businessAgeMonths, int $monthlyRevenue): ?UmkmCategory
    {
        return UmkmCategory::determineCategory($businessAgeMonths, $monthlyRevenue);
    }

    /**
     * Get recommended modules by UMKM category
     * 
     * @param string $categoryCode
     * @return array
     */
    private function getRecommendedModulesByCategory(string $categoryCode): array
    {
        $recommendations = [
            'STARTING' => [
                'products',
                'sales',
                'customers',
                'inventory',
            ],
            'STAGNANT' => [
                'analytics',
                'customers',
                'reports',
                'finance',
                'crm',
            ],
            'GROWING' => [
                'analytics',
                'reports',
                'finance',
                'crm',
                'marketing',
                'advanced-analytics',
            ],
        ];

        return $recommendations[$categoryCode] ?? [];
    }

    /**
     * Parse revenue range string to numeric value
     * 
     * @param string $revenueRange
     * @return int
     */
    private function parseRevenueRange(string $revenueRange): int
    {
        // Remove currency symbols and spaces
        $cleaned = preg_replace('/[^\d\-<>MKmk]/', '', $revenueRange);
        
        // Extract first number
        if (preg_match('/(\d+)/', $cleaned, $matches)) {
            $value = (int) $matches[1];
            
            // Convert based on suffix
            if (stripos($revenueRange, 'M') !== false || stripos($revenueRange, 'juta') !== false) {
                return $value * 1000000; // Million
            } elseif (stripos($revenueRange, 'K') !== false || stripos($revenueRange, 'ribu') !== false) {
                return $value * 1000; // Thousand
            }
            
            return $value;
        }
        
        // Handle special cases
        if (stripos($revenueRange, '<') !== false || stripos($revenueRange, 'kurang') !== false) {
            return 5000000; // Default to 5M for "< 10M" ranges
        }
        
        if (stripos($revenueRange, '>') !== false || stripos($revenueRange, 'lebih') !== false) {
            return 75000000; // Default to 75M for "> 50M" ranges
        }
        
        return 0;
    }

    /**
     * Check if user can skip onboarding (already completed)
     * 
     * @param User $user
     * @return bool
     */
    public function canSkipOnboarding(User $user): bool
    {
        return $user->hasCompletedOnboarding();
    }
}
