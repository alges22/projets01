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
        Schema::table('immatriculation_formats', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vehicle_category_id');
        });
        Schema::table('immatriculation_formats', function (Blueprint $table) {
            $table->foreignUuid('vehicle_category_id')->nullable()->constrained();
            $table->foreignUuid('profile_type_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('immatriculation_formats', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vehicle_category_id');
            $table->dropConstrainedForeignId('profile_type_id');
            $table->foreignUuid('vehicle_category_id')->constrained();
        });
    }
};
