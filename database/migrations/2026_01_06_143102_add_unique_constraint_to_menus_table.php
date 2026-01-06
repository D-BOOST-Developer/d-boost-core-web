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
        Schema::table('menus', function (Blueprint $table) {
            // Add unique constraint on name + module_id to prevent duplicates
            // This ensures each menu name is unique within a module
            $table->unique(['name', 'module_id'], 'menus_name_module_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropUnique('menus_name_module_unique');
        });
    }
};
