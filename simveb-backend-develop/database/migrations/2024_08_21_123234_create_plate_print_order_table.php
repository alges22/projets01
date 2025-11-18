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
        Schema::create('plate_print_order', function (Blueprint $table) {
            $table->foreignUuid('plate_id')->constrained();
            $table->foreignUuid('print_order_id')->constrained();
            $table->string('side')->comment('Plate side front or back.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plate_print_order');
    }
};
