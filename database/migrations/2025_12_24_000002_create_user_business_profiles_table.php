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
        Schema::create('user_business_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            
            // Business Information
            $table->string('business_name', 255);
            $table->string('business_category', 150); // Industry/sector
            $table->text('business_logo_path')->nullable();
            
            // Business Metrics
            $table->integer('business_age_month');
            $table->string('monthly_revenue_range', 50);
            
            // UMKM Category (auto-determined)
            $table->uuid('umkm_category_id');
            
            // Contact Information
            $table->string('owner_name', 150);
            $table->string('contact_email', 150);
            $table->string('contact_phone', 30)->nullable();
            
            // Additional Information
            $table->text('usage_reason')->nullable();
            
            // Onboarding Status
            $table->boolean('onboarding_completed')->default(false);
            
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('umkm_category_id')->references('id')->on('umkm_categories')->onDelete('restrict');
            
            // Indexes
            $table->index(['user_id']);
            $table->index(['umkm_category_id']);
            $table->index(['onboarding_completed']);
            $table->index(['business_category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_business_profiles');
    }
};
