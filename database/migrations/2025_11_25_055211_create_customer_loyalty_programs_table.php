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
        Schema::create('customer_loyalty_programs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('points_per_idr', 10, 4)->default(0.001); // e.g., 1 point per 1000 IDR
            $table->json('tier_thresholds')->nullable(); // {"bronze": 0, "silver": 1000, "gold": 5000, "platinum": 20000}
            $table->json('tier_benefits')->nullable(); // Benefits for each tier
            $table->json('rewards')->nullable(); // Available rewards to redeem
            $table->boolean('is_active')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('points_expiry_days')->nullable(); // Points expire after X days
            
            // Audit fields
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_loyalty_programs');
    }
};
