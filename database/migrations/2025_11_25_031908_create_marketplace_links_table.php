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
        Schema::create('marketplace_links', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->enum('link_type', ['marketplace_profile', 'product'])->default('product');
            $table->string('platform')->nullable(); // shopee, tokopedia, lazada, etc.
            $table->text('url');
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->integer('priority_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('click_count')->default(0);
            
            // Audit fields
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['user_id', 'is_active']);
            $table->index('priority_order');
            $table->index('platform');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_links');
    }
};
