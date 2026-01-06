<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_loyalty_points', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->unique();
            $table->uuid('loyalty_program_id')->nullable();
            $table->decimal('points_earned', 15, 2)->default(0);
            $table->decimal('points_redeemed', 15, 2)->default(0);
            $table->decimal('points_expired', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->string('tier_level')->default('bronze'); // bronze, silver, gold, platinum
            $table->timestamp('last_tier_update')->nullable();
            $table->timestamp('last_earned_at')->nullable();
            $table->timestamp('last_redeemed_at')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('loyalty_program_id')->references('id')->on('customer_loyalty_programs')->onDelete('set null');
            
            // Indexes
            $table->index('tier_level');
            $table->index('current_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_loyalty_points');
    }
};
