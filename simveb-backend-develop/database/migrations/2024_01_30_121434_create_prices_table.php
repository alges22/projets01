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
        Schema::create('prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid( 'characteristic_id')->constrained('vehicle_characteristics')->cascadeOnDelete();
            $table->foreignUuid( 'service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignUuid( 'owner_type_id')->constrained('owner_types')->cascadeOnDelete();
            $table->foreignUuid( 'vehicle_type_id')->constrained('vehicle_types')->cascadeOnDelete();
            $table->foreignUuid( 'vehicle_category_id')->constrained('vehicle_categories')->cascadeOnDelete();
            $table->unsignedDouble( 'price');
            $table->softDeletes();
            $table->unique([
                'characteristic_id',
                'service_id',
                'owner_type_id',
                'vehicle_type_id',
                'vehicle_category_id'
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
