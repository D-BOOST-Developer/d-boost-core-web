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
        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sales_return_id');
            $table->uuid('product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('line_total', 15, 2);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('sales_return_id')->references('id')->on('sales_returns')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->index('sales_return_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_items');
    }
};
