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
        Schema::create('transformation_characteristics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('state');
            $table->foreignUuid('transformation_id')->constrained('vehicle_transformations');
            $table->foreignUuid('characteristic_id')->constrained('vehicle_characteristics');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transformation_characteristics');
    }
};
