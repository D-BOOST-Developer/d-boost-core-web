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
        Schema::create('profit_losses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('period_type', ['daily', 'weekly', 'monthly', 'yearly']);
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('total_expenses', 15, 2)->default(0);
            $table->decimal('gross_profit', 15, 2)->default(0);
            $table->decimal('net_profit', 15, 2)->default(0);
            $table->decimal('profit_margin', 8, 4)->default(0); // Percentage
            $table->json('revenue_breakdown')->nullable(); // JSON for detailed breakdown
            $table->json('expense_breakdown')->nullable(); // JSON for detailed breakdown
            $table->boolean('is_calculated')->default(false);
            $table->timestamp('calculated_at')->nullable();
            
            // Audit fields
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->unique(['period_start', 'period_end', 'period_type']);
            $table->index(['period_type', 'period_start']);
            $table->index('net_profit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_losses');
    }
};
