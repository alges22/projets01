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
        Schema::table('last_immatriculations', function (Blueprint $table) {
            $table->foreignUuid('vehicle_category_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('last_immatriculations', function (Blueprint $table) {
            $table->dropColumn(['vehicle_category_id']);
        });
    }
};
