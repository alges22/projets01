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
        Schema::table('reimmatriculations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('immatriculation_id');
            $table->dropConstrainedForeignId('vehicle_id');
            $table->dropColumn('keep_immatriculation_number');
            $table->dropConstrainedForeignId('plate_transformation_id');

            $table->foreignUuid('additional_service_id')->nullable()->constrained('services');
            $table->nullableUuidMorphs('additional_process');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
