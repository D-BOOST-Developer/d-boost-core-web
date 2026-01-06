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
        Schema::create('umkm_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 30)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            
            // Business age criteria (in months)
            $table->integer('min_business_age')->nullable();
            $table->integer('max_business_age')->nullable();
            
            // Revenue criteria (in IDR)
            $table->bigInteger('min_revenue')->nullable();
            $table->bigInteger('max_revenue')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index(['is_active']);
            $table->index(['code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkm_categories');
    }
};
