<?php

use App\Enums\VehiclePassageType;
use App\Enums\VehicleTypeAtBorder;
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
        Schema::create('vehicle_passages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('officer_id')->constrained('profiles');
            $table->foreignUuid('vehicle_id')->nullable()->constrained();
            $table->string('foreign_vehicle_immatriculation_number')->nullable();
            $table->foreignId('immatriculation_country_id')->nullable()->constrained('countries');
            $table->string('driver_firstname')->nullable();
            $table->string('driver_lastname')->nullable();
            $table->string('driver_telephone')->nullable();
            $table->string('vehicle_owner_firstname')->nullable();
            $table->string('vehicle_owner_lastname')->nullable();
            $table->enum('vehicle_provenance', VehicleTypeAtBorder::toArray());
            $table->integer('total_passengers_on_board');
            $table->enum('passage_type', VehiclePassageType::toArray());
            $table->string('driving_license_number')->nullable();
            $table->string('gray_card_number')->nullable();
            $table->json('driving_license_photo')->nullable();
            $table->json('gray_card_photo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_passages');
    }
};
