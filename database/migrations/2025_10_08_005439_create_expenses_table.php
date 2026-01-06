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
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->enum('category', [
                'rent', 'utilities', 'supplies', 'marketing', 'transportation', 
                'maintenance', 'insurance', 'salaries', 'taxes', 'other'
            ]);
            $table->string('receipt_path')->nullable();
            $table->enum('payment_method', ['cash', 'credit_card', 'debit_card', 'bank_transfer', 'e_wallet'])->default('cash');
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurring_period', ['daily', 'weekly', 'monthly', 'yearly'])->nullable();
            
            // Audit fields
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['expense_date', 'category']);
            $table->index('amount');
            $table->index('is_recurring');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
