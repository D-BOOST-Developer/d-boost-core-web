<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Simplifies sales returns workflow by removing approval states.
     * Old: pending -> approved -> completed (or rejected)
     * New: pending -> completed (or cancelled)
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        // 1. Prepare column for update (Drop constraint for Postgres, or expand Enum for MySQL)
        if ($driver === 'pgsql') {
             DB::statement("ALTER TABLE sales_returns DROP CONSTRAINT IF EXISTS sales_returns_status_check");
        } else {
             DB::statement("ALTER TABLE sales_returns MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending'");
        }

        // 2. Update existing 'approved' and 'rejected' statuses
        DB::table('sales_returns')
            ->where('status', 'approved')
            ->update(['status' => 'completed']);
            
        DB::table('sales_returns')
            ->where('status', 'rejected')
            ->update(['status' => 'cancelled']);
        
        // 3. Finalize column definition
        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE sales_returns ADD CONSTRAINT sales_returns_status_check CHECK (status IN ('pending', 'completed', 'cancelled'))");
        } else {
            DB::statement("ALTER TABLE sales_returns MODIFY COLUMN status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the old status enum with all options
        DB::statement("ALTER TABLE sales_returns MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending'");
    }
};
