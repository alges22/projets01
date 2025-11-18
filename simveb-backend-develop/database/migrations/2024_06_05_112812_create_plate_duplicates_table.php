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
        Schema::create('plate_duplicates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->nullable()->unique();
            $table->string('reason');
            $table->foreignUuid('old_plate_id')->nullable()->constrained('plates');
            $table->foreignUuid('new_plate_id')->nullable()->constrained('plates');
            $table->foreignUuid('demand_id')->constrained();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plate_duplicates');
    }
};
