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
        Schema::create('vehicle_transformations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('demand_id')->constrained();
            $table->foreignUuid('vehicle_id')->constrained();
            $table->foreignUuid('owner_id')->constrained('vehicle_owners');
            $table->foreignUuid('type_id')->constrained('transformation_types');
            $table->foreignUuid('category_id')->constrained('vehicle_characteristic_categories');
            $table->foreignUuid('value_id')->constrained('vehicle_characteristics');
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
        Schema::dropIfExists('vehicle_transformations');
    }
};
