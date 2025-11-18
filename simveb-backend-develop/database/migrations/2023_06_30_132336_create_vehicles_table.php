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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('origin_country_id')->index();
            $table->string('customs_reference')->nullable();
            $table->string('chassis_number');
            $table->foreignUuid('vehicle_brand_id')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->integer('number_of_seats');
            $table->uuid('vehicle_type_id')->index()->nullable();
            $table->uuid('vehicle_category_id')->index()->nullable();
            $table->uuid('owner_type_id')->index()->nullable();
            $table->foreignUuid('owner_id')->nullable()->constrained('vehicle_owners');
            $table->string('engin_number')->nullable();
            $table->double('charged_weight')->nullable();
            $table->double('empty_weight')->nullable();
            $table->integer('first_circulation_year')->nullable()->index();
            $table->foreignUuid('park_id')->nullable()->constrained();
            $table->boolean('is_transformed')->default(false);
            $table->foreignUuid('front_plate_id')->nullable()->constrained('plates');
            $table->foreignUuid('back_plate_id')->nullable()->constrained('plates');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
