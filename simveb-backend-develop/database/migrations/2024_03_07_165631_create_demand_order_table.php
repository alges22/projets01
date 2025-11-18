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
        Schema::create('demand_order', function (Blueprint $table) {
            $table->foreignUuid('demand_id')->constrained();
            $table->foreignUuid('order_id')->constrained();
            $table->unsignedDouble('amount');
            $table->primary(['demand_id','order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demand_order');
    }
};
