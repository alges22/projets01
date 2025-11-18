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
        Schema::create('immatriculations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('demand_id')->constrained();
            $table->string('number')->nullable();
            $table->foreignUuid('vehicle_id')->constrained();
            $table->foreignUuid('vehicle_owner_id')->constrained();
            $table->timestamp('issued_at')->nullable();
            $table->foreignUuid('immatriculation_format')->nullable()->constrained();
            $table->string('number_label')->nullable();
            $table->foreignUuid('plate_color_id')->nullable()->constrained();
            $table->foreignUuid('front_plate_shape_id')->nullable()->constrained('plate_shapes');
            $table->foreignUuid('back_plate_shape_id')->nullable()->constrained('plate_shapes');
            $table->string('status')->default(Status::pending->name);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immatriculations');
    }
};
