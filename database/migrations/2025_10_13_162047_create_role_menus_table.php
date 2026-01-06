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
        Schema::create('role_menus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('role_id');
            $table->uuid('menu_id');
            $table->json('permissions')->nullable(); // create, read, update, delete, etc.
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            $table->unique(['role_id', 'menu_id']);
            $table->index(['role_id', 'is_active']);
            $table->index(['menu_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_menus');
    }
};
