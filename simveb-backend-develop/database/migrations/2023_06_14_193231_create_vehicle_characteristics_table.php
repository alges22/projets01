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
        Schema::create('vehicle_characteristics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('vehicle_characteristic_categories');
            $table->string('value')->nullable();
            $table->double('min_value')->nullable();
            $table->double('max_value')->nullable();
            $table->double('price')->nullable();
            $table->unsignedDouble('cost')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['category_id', 'value', 'min_value', 'max_value'],'unicity_cats');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_characteristics');
    }
};
