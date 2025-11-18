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
            $table->boolean('with_immatriculation')->default(false);

            $table->foreignUuid('vehicle_owner_id')->nullable()->constrained('vehicle_owners');
            $table->foreignUuid('plate_color_id')->nullable()->constrained('plate_colors');
            $table->foreignUuid('back_plate_shape_id')->nullable()->constrained('plate_shapes');
            $table->foreignUuid('front_plate_shape_id')->nullable()->constrained('plate_shapes');
            $table->string('desired_number')->nullable();
            $table->string('label')->nullable();

            $table->foreignUuid('immatriculation_id')->nullable()->constrained('immatriculations');
            $table->foreignUuid('plate_transformation_id')->nullable()->constrained('plate_transformations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reimmatriculations', function (Blueprint $table) {
            $table->dropColumn('with_immatriculation');

            $table->dropConstrainedForeignId('vehicle_owner_id');
            $table->dropConstrainedForeignId('plate_color_id');
            $table->dropConstrainedForeignId('back_plate_shape_id');
            $table->dropConstrainedForeignId('front_plate_shape_id');

            $table->dropColumn('desired_number');
            $table->dropColumn('label');

            $table->dropConstrainedForeignId('immatriculation_id');
            $table->dropConstrainedForeignId('plate_transformation_id');
        });
    }
};
