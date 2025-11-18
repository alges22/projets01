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
        Schema::create('plate_transformations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('demand_id')->nullable()->constrained('demands');
            $table->string('number')->nullable();
            $table->string('status')->default(Status::pending->name);
            $table->foreignUuid('vehicle_id')->nullable()->constrained('vehicles');
            $table->foreignUuid('plate_color_id')->nullable()->constrained('plate_colors');
            $table->foreignUuid('front_plate_shape_id')->nullable()->constrained('plate_shapes');
            $table->foreignUuid('back_plate_shape_id')->nullable()->constrained('plate_shapes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plate_transformations');
    }
};
