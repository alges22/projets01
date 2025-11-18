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
        Schema::table('service_steps', function (Blueprint $table) {
            $table->dropUnique('service_steps_service_id_position_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_steps', function (Blueprint $table) {
            $table->unique(['service_id', 'position']);
        });
    }
};
