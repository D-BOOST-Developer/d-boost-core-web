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
        Schema::create('sales_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sale_id');
            $table->uuid('product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount_per_item', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2);
            
            // Audit fields
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Add soft delete support
            
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['sale_id', 'product_id']);
            $table->index('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_details');
    }
};
