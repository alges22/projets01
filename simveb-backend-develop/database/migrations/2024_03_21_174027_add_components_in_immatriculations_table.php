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
            $table->string('prefix')->nullable();
            $table->string('alphabetic_label')->nullable();
            $table->string('zone')->nullable();
            $table->integer('numeric_label')->nullable();
            $table->string('country_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('immatriculations', function (Blueprint $table) {
            $table->dropColumn('prefix');
            $table->dropColumn('alphabetic_label');
            $table->dropColumn('zone');
            $table->dropColumn('numeric_label');
            $table->dropColumn('country_code');
        });
    }
};
