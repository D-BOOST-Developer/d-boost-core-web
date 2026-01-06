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
        Schema::create('deby_a_i_chat_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->enum('role', ['user', 'assistant']); // user atau assistant
            $table->longText('message'); // Chat message content
            $table->json('metadata')->nullable(); // Additional data (thinking_mode, api type, etc)
            $table->boolean('thinking_mode')->default(false); // Track if thinking mode was used
            $table->timestamps();
            
            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Index untuk query yang lebih cepat
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deby_a_i_chat_logs');
    }
};
