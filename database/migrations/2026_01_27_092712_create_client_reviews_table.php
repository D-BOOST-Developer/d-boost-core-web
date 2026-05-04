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
        Schema::create('client_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('storefront_config_id')->constrained('storefront_configs')->cascadeOnDelete();
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->text('review_text');
            $table->integer('rating')->default(5); // 1-5 stars
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
            
            $table->index(['storefront_config_id', 'is_approved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_reviews');
    }
};
