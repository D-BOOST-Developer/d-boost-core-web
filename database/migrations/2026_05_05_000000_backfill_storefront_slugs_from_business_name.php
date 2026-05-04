<?php

use App\Models\StorefrontConfig;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

return new class extends Migration {
    /**
     * Backfill storefront_configs.slug from UserBusinessProfile.business_name.
     *
     * Idempotent: rerunning produces the same result. Manually customized
     * slugs (slug that differs from the user.name-derived form) are preserved.
     * Old slugs are NOT retained — old public URLs will return 404.
     */
    public function up(): void
    {
        $logChannel = Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/slug-backfill-' . now()->format('Ymd_His') . '.log'),
        ]);

        $updated = 0;
        $preserved = 0;
        $unchanged = 0;

        DB::transaction(function () use ($logChannel, &$updated, &$preserved, &$unchanged) {
            StorefrontConfig::with('user.businessProfile')
                ->orderBy('id')
                ->chunk(200, function ($configs) use ($logChannel, &$updated, &$preserved, &$unchanged) {
                    foreach ($configs as $config) {
                        $user = $config->user;
                        if (!$user) {
                            $logChannel->warning('Skipping config with missing user', [
                                'config_id' => $config->id,
                                'slug' => $config->slug,
                            ]);
                            continue;
                        }

                        $userNameSlug = Str::slug((string) $user->name);
                        $isUserNameDerived = $userNameSlug !== '' && (
                            $config->slug === $userNameSlug ||
                            preg_match('/^' . preg_quote($userNameSlug, '/') . '-\d+$/', $config->slug) === 1
                        );

                        if (!$isUserNameDerived) {
                            $preserved++;
                            $logChannel->info('Preserved manual slug', [
                                'user_id' => $user->id,
                                'slug' => $config->slug,
                            ]);
                            continue;
                        }

                        $desired = StorefrontConfig::generateUniqueSlug($user);

                        if ($desired === $config->slug) {
                            $unchanged++;
                            continue;
                        }

                        $oldSlug = $config->slug;
                        $config->update(['slug' => $desired]);
                        $updated++;

                        $logChannel->info('Updated slug', [
                            'user_id' => $user->id,
                            'old' => $oldSlug,
                            'new' => $desired,
                            'business_name' => $user->businessProfile?->business_name,
                        ]);
                    }
                });
        });

        $logChannel->info('Backfill summary', [
            'updated' => $updated,
            'preserved' => $preserved,
            'unchanged' => $unchanged,
        ]);
    }

    public function down(): void
    {
        // No-op: previous slug values are not retained.
    }
};
