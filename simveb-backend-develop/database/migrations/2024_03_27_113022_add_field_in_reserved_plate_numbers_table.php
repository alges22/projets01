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
            $table->uuid('vehicle_id')->nullable()->change();
            $table->uuid('vehicle_owner_id')->nullable()->change();
            $table->string('alphabetic_label')->nullable()->change();
            $table->string('numeric_label')->nullable()->change();
            $table->integer('min')->nullable();
            $table->integer('max')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reserved_plate_numbers', function (Blueprint $table) {
            $table->dropColumn('max');
            $table->dropColumn('min');
        });
    }
};
