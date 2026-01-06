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
        Schema::table('customer_segment_members', function (Blueprint $table) {
            // Drop existing foreign key and index
            $table->dropForeign(['segment_id']);
            $table->dropUnique(['customer_id', 'segment_id']);
            $table->dropIndex(['segment_id']);
            
            // Rename column
            $table->renameColumn('segment_id', 'customer_segment_id');
        });
        
        Schema::table('customer_segment_members', function (Blueprint $table) {
            // Re-add foreign key, unique constraint, and index with new column name
            $table->foreign('customer_segment_id')->references('id')->on('customer_segments')->onDelete('cascade');
            $table->unique(['customer_id', 'customer_segment_id']);
            $table->index('customer_segment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_segment_members', function (Blueprint $table) {
            // Drop new foreign key and index
            $table->dropForeign(['customer_segment_id']);
            $table->dropUnique(['customer_id', 'customer_segment_id']);
            $table->dropIndex(['customer_segment_id']);
            
            // Rename column back
            $table->renameColumn('customer_segment_id', 'segment_id');
        });
        
        Schema::table('customer_segment_members', function (Blueprint $table) {
            // Re-add original foreign key, unique constraint, and index
            $table->foreign('segment_id')->references('id')->on('customer_segments')->onDelete('cascade');
            $table->unique(['customer_id', 'segment_id']);
            $table->index('segment_id');
        });
    }
};
