<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Add structured geographic fields
            $table->string('province')->nullable()->after('phone');
            $table->string('district')->nullable()->after('city'); // kecamatan
            $table->string('detail_address')->nullable()->after('postal_code'); // RT/RW, patokan, gang, dll

            // Link to delivery zone (nullable — legacy addresses won't have this)
            $table->foreignId('delivery_zone_id')->nullable()->constrained('delivery_zones')->nullOnDelete()->after('detail_address');
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['delivery_zone_id']);
            $table->dropColumn(['province', 'district', 'detail_address', 'delivery_zone_id']);
        });
    }
};
