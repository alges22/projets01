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
        Schema::create('gma_vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vehicle_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->string('vin')->unique();
            $table->string('customs_reference')->nullable()->unique();
            $table->foreignUuid('institution_id')
                ->nullable()
                ->constrained();
            $table->foreignUuid('author_id')->constrained('profiles');
            $table->string('status')->default(Status::submitted->name);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gma_vehicles');
    }
};
