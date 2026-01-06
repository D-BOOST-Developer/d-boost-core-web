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
        Schema::create('financial_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('report_type', ['income_statement', 'balance_sheet', 'cash_flow', 'trial_balance']);
            $table->date('period_start');
            $table->date('period_end');
            
            // Income Statement fields
            $table->decimal('total_revenue', 15, 2)->nullable();
            $table->decimal('total_cost_of_goods_sold', 15, 2)->nullable();
            $table->decimal('gross_profit', 15, 2)->nullable();
            $table->decimal('total_operating_expenses', 15, 2)->nullable();
            $table->decimal('operating_profit', 15, 2)->nullable();
            $table->decimal('total_other_income', 15, 2)->nullable();
            $table->decimal('total_other_expenses', 15, 2)->nullable();
            $table->decimal('net_profit', 15, 2)->nullable();
            
            // Balance Sheet fields
            $table->decimal('total_assets', 15, 2)->nullable();
            $table->decimal('total_liabilities', 15, 2)->nullable();
            $table->decimal('total_equity', 15, 2)->nullable();
            
            // Cash Flow fields
            $table->decimal('cash_beginning', 15, 2)->nullable();
            $table->decimal('cash_from_operations', 15, 2)->nullable();
            $table->decimal('cash_from_investing', 15, 2)->nullable();
            $table->decimal('cash_from_financing', 15, 2)->nullable();
            $table->decimal('cash_ending', 15, 2)->nullable();
            
            $table->uuid('generated_by')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('generated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->index('report_type');
            $table->index('period_start');
            $table->index('period_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_reports');
    }
};
