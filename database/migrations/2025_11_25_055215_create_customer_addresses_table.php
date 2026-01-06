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
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->enum('address_type', ['shipping', 'billing', 'both'])->default('shipping');
            $table->string('label')->nullable(); // e.g., "Home", "Office"
            $table->string('recipient_name')->nullable();
            $table->string('phone')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Indonesia');
            $table->boolean('is_default')->default(false);
            $table->text('notes')->nullable(); // Delivery notes
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            
            // Indexes
            $table->index('customer_id');
            $table->index('is_default');
            $table->index('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
