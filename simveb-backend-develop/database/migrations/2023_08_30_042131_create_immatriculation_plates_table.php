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
        Schema::create('immatriculation_plates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('plate_id')->constrained();
            $table->foreignUuid('immatriculation_id')->constrained();
            $table->boolean('is_lost')->default(false);
            $table->boolean('is_spoiled')->default(false);
            $table->text('comment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('deactivated_at')->nullable();
            $table->string('deactivation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immatriculation_plates');
    }
};
