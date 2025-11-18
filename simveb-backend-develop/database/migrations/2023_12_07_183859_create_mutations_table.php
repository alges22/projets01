<?php

use App\Enums\Status;
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
        Schema::create('mutations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sale_declaration_reference')->unique();
            $table->string('sale_declaration_id')->constrained();
            $table->string('demand_id')->constrained();
            $table->string('vehicle_owner_id')->constrained();
            $table->string('vehicle_id')->constrained();
            $table->string('new_owner_id')->constrained();
            $table->foreignUuid('gray_card_id')->constrained();
            $table->text('comment')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutations');
    }
};
