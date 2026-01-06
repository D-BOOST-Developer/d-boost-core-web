<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update products menu route (under inventory module)
        // Only update if route is still using old format
        DB::table('menus')
            ->where('name', 'products')
            ->where(function($query) {
                $query->where('route', 'products.index')
                      ->orWhere('route', 'like', 'products.%')
                      ->orWhereNull('route');
            })
            ->update([
                'route' => 'inventory.products.index',
                'updated_at' => now(),
            ]);

        // Update sales menu route (under finance module)
        // Only update if route is still using old format
        DB::table('menus')
            ->where('name', 'sales')
            ->where(function($query) {
                $query->where('route', 'sales.index')
                      ->orWhere('route', 'like', 'sales.%')
                      ->orWhereNull('route');
            })
            ->update([
                'route' => 'finance.sales.index',
                'updated_at' => now(),
            ]);

        // Update expenses menu route (under finance module)
        // Only update if route is still using old format
        DB::table('menus')
            ->where('name', 'expenses')
            ->where(function($query) {
                $query->where('route', 'expenses.index')
                      ->orWhere('route', 'like', 'expenses.%')
                      ->orWhereNull('route');
            })
            ->update([
                'route' => 'finance.expenses.index',
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert products menu route
        DB::table('menus')
            ->where('name', 'products')
            ->update([
                'route' => 'products.index',
                'updated_at' => now(),
            ]);

        // Revert sales menu route
        DB::table('menus')
            ->where('name', 'sales')
            ->update([
                'route' => 'sales.index',
                'updated_at' => now(),
            ]);

        // Revert expenses menu route
        DB::table('menus')
            ->where('name', 'expenses')
            ->update([
                'route' => 'expenses.index',
                'updated_at' => now(),
            ]);
    }
};
