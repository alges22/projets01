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
        Schema::create('institutions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('acronym');
            $table->string('name')->unique();
            $table->string('ifu')->unique()->comment("Numéro IFU de l'institution");
            $table->string('email')->unique();
            $table->string('telephone');
            $table->string('address')->nullable();
            $table->foreignUuid('type_id')->constrained('institution_types');
            $table->foreignUuid('town_id')->constrained();
            $table->foreignUuid('border_id')->nullable()->constrained();
            $table->foreignUuid('district_id')->constrained();
            $table->foreignUuid('village_id')->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
