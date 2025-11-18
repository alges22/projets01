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
        Schema::table('reserved_plate_numbers', function (Blueprint $table) {
            $table->dropColumn('value');
            $table->string('alphabetic_label');
            $table->string('numeric_label');
            $table->foreignUuid('vehicle_id')->constrained();
            $table->foreignUuid('vehicle_owner_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reserved_plate_numbers', function (Blueprint $table) {
            $table->string('value');
            $table->dropColumn('alphabetic_label');
            $table->dropColumn('numeric_label');
            $table->dropConstrainedForeignId('vehicle_id');
            $table->dropConstrainedForeignId('vehicle_owner_id');
        });
    }
};
