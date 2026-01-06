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
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('return_number')->unique();
            $table->uuid('sale_id');
            $table->date('return_date');
            $table->enum('reason', ['defective', 'wrong_item', 'customer_request', 'other']);
            $table->decimal('total_return_amount', 15, 2);
            $table->decimal('refund_amount', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->index('sale_id');
            $table->index('status');
            $table->index('return_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_returns');
    }
};
