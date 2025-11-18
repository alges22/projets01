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
        Schema::table('transformation_characteristics', function (Blueprint $table) {
            $table->foreignUuid('old_characteristic')->nullable()->constrained('vehicle_characteristics');
            $table->foreignUuid('new_characteristic')->nullable()->constrained('vehicle_characteristics');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transformation_characteristics', function (Blueprint $table) {
            $table->dropColumn('old_characteristic')->nullable();
            $table->dropColumn('new_characteristic')->nullable();
        });
    }
};
