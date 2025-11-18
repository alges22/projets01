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
        Schema::create('tinted_window_authorizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('number');
            $table->foreignUuid('vehicle_id')->constrained();
            $table->foreignUuid('vehicle_owner_id')->constrained();
            $table->foreignUuid('demand_id')->constrained();
            $table->string('status');
            $table->boolean('expired')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tinted_window_authorizations');
    }
};
