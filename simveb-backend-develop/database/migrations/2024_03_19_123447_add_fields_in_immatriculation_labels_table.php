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
        Schema::table('immatriculation_labels', function (Blueprint $table) {
            $table->foreignUuid('plate_color_id')->nullable()->constrained();
            $table->foreignUuid('front_plate_shape_id')->nullable()->constrained('plate_shapes');
            $table->foreignUuid('back_plate_shape_id')->nullable()->constrained('plate_shapes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('immatriculation_labels', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plate_color_id');
            $table->dropConstrainedForeignId('front_plate_shape_id');
            $table->dropConstrainedForeignId('back_plate_shape_id');
        });
    }
};
