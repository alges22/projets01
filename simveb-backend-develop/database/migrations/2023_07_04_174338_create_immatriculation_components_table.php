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
        Schema::create('immatriculation_components', function (Blueprint $table) {
            $table->foreignUuid('format_component_id')->constrained();
            $table->foreignUuid('immatriculation_format_id')->constrained();
            $table->unsignedInteger('length')->nullable();
            $table->unsignedInteger('position');
            $table->string('value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immatriculation_components');
    }
};
