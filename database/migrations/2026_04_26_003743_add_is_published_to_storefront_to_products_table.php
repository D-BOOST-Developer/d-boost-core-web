<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_published_to_storefront')
                ->default(false)
                ->after('is_active');

            $table->index(
                ['user_id', 'is_published_to_storefront'],
                'products_user_storefront_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_user_storefront_idx');
            $table->dropColumn('is_published_to_storefront');
        });
    }
};
