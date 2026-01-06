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
        Schema::create('customer_wishlists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('product_id');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->text('notes')->nullable();
            $table->boolean('notify_on_sale')->default(false);
            $table->boolean('notify_on_stock')->default(false);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
            // Unique constraint
            $table->unique(['customer_id', 'product_id']);
            
            // Indexes
            $table->index(['customer_id', 'created_at']);
            $table->index('product_id');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_wishlists');
    }
};
