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
            $table->boolean('keep_immatriculation_number')->default(false)->comment("Garder le numéro d'immatriculation");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reimmatriculations', function (Blueprint $table) {
            $table->dropColumn('keep_immatriculation_number');
        });
    }
};
