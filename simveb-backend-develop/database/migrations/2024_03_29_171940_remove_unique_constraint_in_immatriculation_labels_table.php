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
            $table->dropUnique('immatriculation_labels_label_unique');
        });

        Schema::table('immatriculations', function (Blueprint $table) {
            $table->dropUnique('immatriculations_label_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('immatriculation_labels', function (Blueprint $table) {
            //
        });
    }
};
