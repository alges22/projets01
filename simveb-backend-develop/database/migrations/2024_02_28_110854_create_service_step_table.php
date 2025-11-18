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
        Schema::create('service_step', function (Blueprint $table) {
            $table->foreignUuid('service_id')->nullable()->constrained();
            $table->foreignUuid('step_id')->nullable()->constrained();
            $table->integer('position')->nullable();
            $table->integer('duration')->nullable();
            $table->primary(['service_id', 'step_id']);
            $table->unique(['service_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_step');
    }
};
