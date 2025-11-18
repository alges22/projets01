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
        Schema::table('vehicle_transformations', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropColumn('type_id');
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            $table->dropForeign(['value_id']);
            $table->dropColumn('value_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_transformations', function (Blueprint $table) {
            $table->foreignUuid('type_id')->nullable()->constrained('vehicle_transformations');
            $table->foreignUuid('category_id')->nullable()->constrained('vehicle_transformations');
            $table->foreignUuid('value_id')->nullable()->constrained('vehicle_transformations');
        });
    }
};
