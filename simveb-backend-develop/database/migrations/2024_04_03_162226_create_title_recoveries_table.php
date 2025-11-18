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
        Schema::create('title_recoveries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('demand_id')->constrained();
            $table->foreignUuid('vehicle_owner_id')->constrained();
            $table->foreignUuid('vehicle_id')->constrained();
            $table->foreignUuid('deposit_id')->constrained('title_deposits');
            $table->text('comment')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('title_recoveries');
    }
};
