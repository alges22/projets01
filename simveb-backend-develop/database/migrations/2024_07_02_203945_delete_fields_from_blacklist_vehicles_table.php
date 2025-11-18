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
        Schema::table('blacklist_vehicles', function (Blueprint $table) {
            $table->dropColumn('vehicle_type');
            $table->dropColumn('foreign_vehicle_immatriculation_number');
            $table->dropColumn('owner_firstname');
            $table->dropColumn('owner_lastname');
            $table->dropColumn('recorder_officer_id');
            $table->dropColumn('approver_id');
            $table->dropColumn('recorded_at');
            $table->dropColumn('approved_at');
            $table->dropColumn('status');
        });
        Schema::table('blacklist_vehicles', function (Blueprint $table) {
            $table->string('vin');
            $table->foreignUuid('author_id')->constrained('profiles');
            $table->foreignUuid('validator_id')->nullable()->constrained('profiles');
            $table->timestamp('validated_at')->nullable();
            $table->foreignUuid('rejector_id')->nullable()->constrained('profiles');
            $table->timestamp('rejected_at')->nullable();
            $table->string('status')->default(Status::submitted->name);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blacklist_vehicles', function (Blueprint $table) {
            $table->dropColumn('vin');
            $table->dropColumn('author_id');
            $table->dropColumn('validator_id');
            $table->dropColumn('validated_at');
            $table->dropColumn('rejector_id');
            $table->dropColumn('rejected_at');
            $table->dropColumn('status');
        });
        Schema::table('blacklist_vehicles', function (Blueprint $table) {
            $table->enum('vehicle_type', VehicleTypeAtBorder::toArray())->nullable();
            $table->string('foreign_vehicle_immatriculation_number')->nullable();
            $table->string('owner_firstname')->nullable();
            $table->string('owner_lastname')->nullable();
            $table->foreignUuid('recorder_officer_id')->nullable()->constrained('profiles');
            $table->foreignUuid('approver_id')->nullable()->constrained('profiles');
            $table->string('status')->default(Status::pending->name);
            $table->timestamp('recorded_at')->nullable();
            $table->timestamp('approved_at')->nullable();
        });
    }
};
