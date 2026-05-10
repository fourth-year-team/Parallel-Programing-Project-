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
        Schema::create('load_balancer_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_uuid')->unique();
            $table->string('selected_server');
            $table->string('strategy')->default('round_robin');
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->timestamps();

            $table->index('selected_server');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('load_balancer_logs');
    }
};
