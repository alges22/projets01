<?php

use App\Enums\Status;
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
        Schema::create('blacklist_vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('vehicle_type', VehicleTypeAtBorder::toArray());
            $table->foreignUuid('vehicle_id')->nullable()->constrained();
            $table->string('foreign_vehicle_immatriculation_number')->nullable();
            $table->string('owner_firstname')->nullable();
            $table->string('owner_lastname')->nullable();
            $table->foreignUuid('recorder_officer_id')->constrained('profiles');
            $table->foreignUuid('approver_id')->nullable()->constrained('profiles');
            $table->string('status')->default(Status::pending->name);
            $table->timestamp('recorded_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blacklist_vehicles');
    }
};
