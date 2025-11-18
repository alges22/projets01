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
        Schema::table('vehicle_alerts', function (Blueprint $table) {
            $table->dropColumn([
                'reference',
                'conductor_name',
                'conductor_phone',
                'validated_at',
                'rejected_at',
                'date',
                'passage_type',
            ]);
            $table->dropConstrainedForeignId('validator_id');
            $table->dropConstrainedForeignId('rejector_id');
            $table->string('driver_firstname');
            $table->string('driver_lastname');
            $table->foreignUuid('recorder_officer_id')->nullable()->constrained('profiles');
            $table->foreignUuid('canceler_officer_id')->nullable()->constrained('profiles');
            $table->timestamp('recorded_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
        });

        Schema::create('alert_type_vehicle_alert', function (Blueprint $table) {
            $table->foreignUuid('alert_type_id')->constrained();
            $table->foreignUuid('vehicle_alert_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_alerts', function (Blueprint $table) {
            $table->string('reference')->unique();
            $table->string('conductor_name');
            $table->string('conductor_phone');
            $table->foreignUuid('validator_id')->nullable()->constrained('users');
            $table->foreignUuid('rejector_id')->nullable()->constrained('users');
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('date');
            $table->enum('passage_type',['in','out']);
            $table->dropColumn(['driver_firstname', 'driver_lastname', 'recorded_at', 'canceled_at']);
            $table->dropConstrainedForeignId('recorder_officer_id');
            $table->dropConstrainedForeignId('canceler_officer_id');
        });

        Schema::dropIfExists('alert_type_vehicle_alert');
    }
};
