<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Drop old inventory tables that have been replaced by inventory_transactions
     */
    public function up(): void
    {
        // Drop stock_movements table (replaced by inventory_transactions)
        Schema::dropIfExists('stock_movements');
        
        // Drop stock_adjustments table (replaced by inventory_transactions)
        Schema::dropIfExists('stock_adjustments');
    }

    /**
     * Reverse the migrations.
     * 
     * Note: This will recreate empty tables without data.
     * If you need to restore data, use backups from storage/app/backups/
     */
    public function down(): void
    {
        // Recreate stock_movements table structure (without data)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->enum('movement_type', ['in', 'out', 'adjustment_in', 'adjustment_out', 'return_in', 'return_out', 'transfer_in', 'transfer_out']);
            $table->integer('quantity');
            $table->integer('stock_before')->nullable();
            $table->integer('stock_after')->nullable();
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->string('reference_type')->nullable();
            $table->uuid('reference_id')->nullable();
            $table->uuid('purchase_order_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('movement_date');
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['product_id', 'movement_date']);
            $table->index(['movement_type', 'movement_date']);
        });
        
        // Recreate stock_adjustments table structure (without data)
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->enum('adjustment_type', ['increase', 'decrease', 'damage', 'loss', 'expired', 'restock', 'correction', 'inventory_count']);
            $table->integer('quantity_change');
            $table->integer('old_quantity');
            $table->integer('new_quantity');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('adjustment_date');
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['product_id', 'adjustment_date']);
            $table->index(['status', 'adjustment_date']);
        });
    }
};

