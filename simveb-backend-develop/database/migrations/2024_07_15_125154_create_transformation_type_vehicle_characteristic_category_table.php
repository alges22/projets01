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
        Schema::create('transformation_type_vehicle_characteristic_category', function (Blueprint $table) {
            $table->foreignUuid('type_id')->constrained('transformation_types');
            $table->foreignUuid('category_id')->constrained('vehicle_characteristic_categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transformation_type_vehicle_characteristic_category');
    }
};
