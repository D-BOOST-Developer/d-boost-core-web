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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Multi-tenancy
            $table->uuid('user_id');
            $table->uuid('product_id');
            
            // Transaction details
            $table->enum('transaction_type', [
                'purchase',
                'sale',
                'adjustment_increase',
                'adjustment_decrease',
                'return_from_customer',
                'return_to_supplier',
                'transfer_in',
                'transfer_out',
                'damaged',
                'expired',
                'initial_stock'
            ]);
            
            $table->integer('quantity');
            
            // Audit trail
            $table->integer('stock_before');
            $table->integer('stock_after');
            
            // Pricing
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            
            // Reference
            $table->enum('reference_type', ['sale', 'manual']);
            $table->uuid('reference_id')->nullable();
            
            // Approval workflow
            $table->enum('status', ['pending', 'approved', 'rejected', 'auto_approved'])
                  ->default('pending');
            
            $table->enum('reason', [
                'purchase',
                'sale',
                'damage',
                'loss',
                'theft',
                'correction',
                'inventory_count',
                'expired',
                'return',
                'other'
            ])->nullable();
            
            $table->text('notes')->nullable();
            
            // Approval
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Timestamps
            $table->timestamp('transaction_date');
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->onDelete('cascade');
                  
            $table->foreign('approved_by')
                  ->references('id')->on('users')
                  ->onDelete('set null');
                  
            $table->foreign('created_by')
                  ->references('id')->on('users')
                  ->onDelete('set null');
            
            // Indexes for performance
            $table->index(['user_id', 'transaction_date']);
            $table->index(['product_id', 'transaction_date']);
            $table->index(['user_id', 'status', 'transaction_date']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};

