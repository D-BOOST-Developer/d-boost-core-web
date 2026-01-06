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
        Schema::table('user_business_profiles', function (Blueprint $table) {
            // Bank Information
            $table->string('bank_name', 100)->nullable()->after('usage_reason');
            $table->string('bank_account_number', 50)->nullable()->after('bank_name');
            $table->string('bank_account_holder', 150)->nullable()->after('bank_account_number');
            
            // QRIS Barcode
            $table->text('qris_barcode_path')->nullable()->after('bank_account_holder');
            
            // Google Maps Link
            $table->text('google_maps_link')->nullable()->after('qris_barcode_path');
            
            // Business Address
            $table->text('business_address')->nullable()->after('google_maps_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_business_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'bank_account_number',
                'bank_account_holder',
                'qris_barcode_path',
                'google_maps_link',
                'business_address',
            ]);
        });
    }
};
