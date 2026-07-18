<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Store courier info at order time (snapshot)
            $table->string('courier_name')->nullable()->after('shipping_cost');
            $table->string('courier_service')->nullable()->after('courier_name'); // e.g. "REG", "YES"
            $table->string('shipping_type')->nullable()->after('courier_service'); // 'biteship', 'manual', 'free'
            $table->string('province')->nullable()->after('city');
            $table->string('district')->nullable()->after('province');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['courier_name', 'courier_service', 'shipping_type', 'province', 'district']);
        });
    }
};
