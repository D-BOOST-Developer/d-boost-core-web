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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('payment_number')->unique();
            $table->enum('reference_type', ['sale', 'purchase_order', 'expense']);
            $table->uuid('reference_id');
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'credit_card', 'debit_card', 'bank_transfer', 'e_wallet', 'check']);
            $table->date('payment_date');
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->index('reference_type');
            $table->index('reference_id');
            $table->index('status');
            $table->index('payment_date');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
