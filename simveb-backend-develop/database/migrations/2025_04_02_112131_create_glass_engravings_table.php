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
        Schema::create('glass_engravings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('number')->nullable();
            $table->foreignUuid('vehicle_id')->constrained();
            $table->foreignUuid('vehicle_owner_id')->constrained();
            $table->foreignUuid('demand_id')->constrained();
            $table->string('status');
            $table->boolean('expired')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('glass_engravings');
    }
};
