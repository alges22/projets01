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
        Schema::table('reimmatriculation_reasons', function (Blueprint $table) {
            $table->boolean('enable_plate_transformation')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reimmatriculation_reasons', function (Blueprint $table) {
            $table->dropColumn('enable_plate_transformation');
        });
    }
};
