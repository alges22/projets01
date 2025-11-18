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
        Schema::table('plate_orders', function (Blueprint $table) {
            $table->uuid('plate_shape_id')->nullable()->change();
            $table->uuid('plate_color_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plate_orders', function (Blueprint $table) {
            //
        });
    }
};
