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
        Schema::create('customer_segment_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('customer_segment_id'); // Changed from segment_id to customer_segment_id
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('customer_segment_id')->references('id')->on('customer_segments')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate assignments
            $table->unique(['customer_id', 'customer_segment_id']);
            
            // Indexes
            $table->index('customer_id');
            $table->index('customer_segment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_segment_members');
    }
};
