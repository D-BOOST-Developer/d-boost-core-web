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
        Schema::create('customer_feedbacks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('sale_id')->nullable();
            $table->uuid('product_id')->nullable();
            $table->integer('rating')->unsigned(); // 1-5 stars
            $table->enum('feedback_type', ['product', 'service', 'general'])->default('general');
            $table->text('comment')->nullable();
            $table->boolean('is_published')->default(false);
            $table->text('admin_response')->nullable();
            $table->uuid('responded_by')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->json('metadata')->nullable(); // Extra info like photos, videos
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('responded_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['customer_id', 'created_at']);
            $table->index('sale_id');
            $table->index('product_id');
            $table->index('rating');
            $table->index('feedback_type');
            $table->index('is_published');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_feedbacks');
    }
};
