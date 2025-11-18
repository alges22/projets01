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
        Schema::table('reimmatriculations', function (Blueprint $table) {
            $table->foreignUuid('plate_transformation_id')->nullable()->constrained('plate_transformations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reimmatriculations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plate_transformation_id');
        });
    }
};
