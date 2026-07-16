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
        Schema::create('job_executions', function (Blueprint $table) {
            $table->id();
            $table->string('job_name');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('status')->default('completed');
            $table->timestamp('executed_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['job_name', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_executions');
    }
};
