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
        Schema::create('last_immatriculations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('prefix')->nullable();
            $table->string('alphabetic_label')->nullable();
            $table->string('zone')->nullable();
            $table->integer('numeric_label')->nullable();
            $table->string('country_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('last_immatriculations');
    }
};
