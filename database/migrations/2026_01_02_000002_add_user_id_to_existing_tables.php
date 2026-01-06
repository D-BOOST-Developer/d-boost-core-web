<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add user_id to products
        Schema::table('products', function (Blueprint $table) {
            $table->uuid('user_id')->after('id')->nullable();
            $table->integer('max_stock_level')->default(1000)->after('min_stock_level');
            $table->string('unit', 50)->default('pcs')->after('category');
        });
        
        // Add user_id to suppliers
        Schema::table('suppliers', function (Blueprint $table) {
            $table->uuid('user_id')->after('id')->nullable();
            $table->string('code', 50)->unique()->after('user_id')->nullable();
            $table->decimal('credit_limit', 15, 2)->nullable()->after('payment_terms');
            $table->integer('rating')->default(5)->after('is_active');
        });
        
        // Assign existing data to first user (temporary - will be properly assigned later)
        $firstUserId = DB::table('users')->orderBy('created_at')->value('id');
        
        if ($firstUserId) {
            DB::table('products')->whereNull('user_id')->update(['user_id' => $firstUserId]);
            DB::table('suppliers')->whereNull('user_id')->update(['user_id' => $firstUserId]);
        }
        
        // Make user_id NOT NULL and add foreign keys
        Schema::table('products', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->index(['user_id', 'is_active', 'category']);
        });
        
        Schema::table('suppliers', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->index(['user_id', 'is_active', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id', 'is_active', 'category']);
            $table->dropColumn(['user_id', 'max_stock_level', 'unit']);
        });
        
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id', 'is_active', 'name']);
            $table->dropColumn(['user_id', 'code', 'credit_limit', 'rating']);
        });
    }
};

