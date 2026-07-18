<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();

            // Geographic hierarchy
            $table->string('province')->index();
            $table->string('city')->index();
            $table->string('district')->nullable()->index(); // kecamatan (optional for zone)
            $table->string('postal_code', 10)->nullable();

            // BiteShip area codes (filled when using API)
            $table->string('biteship_area_id')->nullable();

            // Manual fallback shipping cost (used when BiteShip is unavailable)
            $table->unsignedBigInteger('manual_shipping_cost')->default(0); // in IDR, 0 = no manual fallback

            // Settings
            $table->boolean('is_active')->default(true)->index();

            // Optional: estimated delivery text
            $table->string('estimated_delivery')->nullable(); // e.g. "1-2 hari"

            // Notes for admin
            $table->string('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_zones');
    }
};
