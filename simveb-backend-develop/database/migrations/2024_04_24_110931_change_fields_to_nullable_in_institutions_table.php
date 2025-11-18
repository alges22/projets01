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
        Schema::table('institutions', function (Blueprint $table) {
            $table->string('acronym')->nullable()->change();
            $table->string('ifu')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('telephone')->nullable()->change();
            $table->uuid('town_id')->nullable()->change();
            $table->uuid('district_id')->nullable()->change();
            $table->uuid('village_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            //
        });
    }
};
