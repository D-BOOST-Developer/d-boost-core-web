<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration removes the payments table as part of the finance system simplification.
     * Payment tracking is now handled directly within the sales table (payment_method, status).
     */
    public function up(): void
    {
        // Archive existing payment data before dropping
        if (Schema::hasTable('payments')) {
            DB::statement('CREATE TABLE IF NOT EXISTS payments_archive AS SELECT * FROM payments');
        }
        
        // Drop the payments table
        Schema::dropIfExists('payments');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate payments table from archive if needed
        if (Schema::hasTable('payments_archive')) {
            DB::statement('CREATE TABLE payments AS SELECT * FROM payments_archive');
        }
    }
};
