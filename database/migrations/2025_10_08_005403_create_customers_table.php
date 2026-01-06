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
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->integer('age')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->decimal('total_spent', 15, 2)->default(0);
            $table->integer('purchase_frequency')->default(0);
            $table->date('last_purchase_date')->nullable();
            $table->boolean('is_active')->default(true);
            
            // Audit fields
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['city', 'gender']);
            $table->index('total_spent');
            $table->index('last_purchase_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
