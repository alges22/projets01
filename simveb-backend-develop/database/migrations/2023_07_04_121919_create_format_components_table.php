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
        Schema::create('format_components', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code');
            $table->text('description')->nullable();
            $table->string('default_value')->nullable();
            $table->integer('default_length');
            $table->json('possible_values')->nullable();
            $table->boolean('is_auto')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('format_components');
    }
};
