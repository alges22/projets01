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
        Schema::create('treatment_times', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('start_at');
            $table->timestamp('end_at')->nullable();
            $table->foreignUuid('profile_id')->constrained();
            $table->foreignUuid('treatment_id')->constrained();
            $table->string('status');
            $table->integer('duration')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_times');
    }
};
