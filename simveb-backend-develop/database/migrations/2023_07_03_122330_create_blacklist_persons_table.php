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
        Schema::create('blacklist_persons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ifu')->nullable();
            $table->string('npi')->nullable();
            $table->string('plate_number')->nullable();
            $table->string('vin')->nullable();
            $table->string('id_number')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blacklist_persons');
    }
};
