<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Add Biteship destination area ID — used for realtime shipping rates.
            // Nullable: old addresses won't have this; they get resolved on first use.
            if (!Schema::hasColumn('addresses', 'biteship_area_id')) {
                $table->string('biteship_area_id', 100)->nullable()->after('postal_code')
                      ->comment('Biteship destination area ID resolved from Maps API');
            }
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            if (Schema::hasColumn('addresses', 'biteship_area_id')) {
                $table->dropColumn('biteship_area_id');
            }
        });
    }
};
