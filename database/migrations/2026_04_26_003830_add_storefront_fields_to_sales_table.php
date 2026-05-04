<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE sales ADD COLUMN order_source VARCHAR(20) NOT NULL DEFAULT 'walk_in' CHECK (order_source IN ('walk_in', 'storefront'))");
            DB::statement("ALTER TABLE sales ADD COLUMN customer_snapshot JSONB NULL");
        } else {
            DB::statement("ALTER TABLE sales ADD COLUMN order_source ENUM('walk_in', 'storefront') NOT NULL DEFAULT 'walk_in' AFTER payment_method");
            DB::statement("ALTER TABLE sales ADD COLUMN customer_snapshot JSON NULL AFTER notes");
        }

        Schema::table('sales', function (Blueprint $table) {
            $table->index(['status', 'order_source'], 'sales_status_source_idx');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('sales_status_source_idx');
            $table->dropColumn(['order_source', 'customer_snapshot']);
        });
    }
};
