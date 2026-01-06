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
        Schema::create('umkm_module_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('module_id');
            
            // Personalization Settings (does NOT affect RBAC)
            $table->integer('priority_level')->default(99); // 1 = highest priority
            $table->boolean('is_recommended')->default(false);
            $table->text('reason')->nullable(); // 'user_selected', 'system_suggestion', etc.
            
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
            
            // Unique Constraint - One preference per user per module
            $table->unique(['user_id', 'module_id']);
            
            // Indexes
            $table->index(['user_id', 'priority_level']);
            $table->index(['is_recommended']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkm_module_preferences');
    }
};
