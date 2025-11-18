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
        Schema::table('immatriculations', function (Blueprint $table) {
            $table->renameColumn('immatriculation_format', 'immatriculation_format_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('immatriculations', function (Blueprint $table) {
            $table->renameColumn('immatriculation_format_id', 'immatriculation_format');

        });
    }
};
