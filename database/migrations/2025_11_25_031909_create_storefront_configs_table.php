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
        Schema::create('storefront_configs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique(); // One config per user
            $table->string('slug')->unique(); // For public URL
            $table->string('storefront_title');
            $table->string('tagline')->nullable();
            $table->string('logo_path')->nullable();
            
            // Header background
            $table->enum('header_bg_type', ['color', 'gradient', 'image'])->default('color');
            $table->text('header_bg_value')->nullable(); // hex color, gradient CSS, or image path
            
            // Color scheme
            $table->string('primary_color', 7)->default('#3B82F6'); // hex color
            $table->string('secondary_color', 7)->nullable();
            $table->string('text_color', 7)->nullable();
            
            // Layout settings
            $table->integer('grid_columns')->default(3); // 2, 3, or 4
            $table->enum('card_style', ['bordered', 'shadow', 'flat'])->default('shadow');
            $table->integer('card_spacing')->default(16); // pixels
            
            // Content blocks (JSON)
            $table->json('text_blocks')->nullable(); // Array of text content sections
            $table->text('footer_content')->nullable();
            $table->json('social_links')->nullable(); // {instagram: url, facebook: url, etc}
            
            // SEO
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            
            // Status
            $table->boolean('is_published')->default(false);
            
            // Audit fields (required by HasUuidAndAudit trait)
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('slug');
            $table->index('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storefront_configs');
    }
};
