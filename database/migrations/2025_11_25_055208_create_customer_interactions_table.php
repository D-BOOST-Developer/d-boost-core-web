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
        Schema::create('customer_interactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->enum('interaction_type', ['email', 'phone', 'meeting', 'chat', 'note', 'other']);
            $table->string('subject');
            $table->text('description')->nullable();
            $table->dateTime('interaction_date');
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'pending'])->default('completed');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->uuid('assigned_to')->nullable();
            $table->dateTime('follow_up_date')->nullable();
            $table->json('metadata')->nullable(); // Extra data like call duration, email subject, etc.
            
            // Audit fields
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['customer_id', 'interaction_date']);
            $table->index('interaction_type');
            $table->index('status');
            $table->index('assigned_to');
            $table->index('follow_up_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_interactions');
    }
};
